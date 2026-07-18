<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\MarriageInvitation;
use App\Models\PaymentTransaction;
use App\Models\UPITransaction;
use App\Services\RazorpayService;
use App\Support\MarriageInvitationCard;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MarriageInvitationController extends Controller
{
    public function index()
    {
        $templates = config('marriage_invitations.templates', []);
        $invitations = MarriageInvitation::whereIn('user_id', Auth::user()->allowedUserIds())
            ->orderByDesc('updated_at')
            ->paginate(10);
        $latestInvitation = MarriageInvitation::whereIn('user_id', Auth::user()->allowedUserIds())
            ->orderByDesc('updated_at')
            ->first();
        $showDemoThumbnails = ! Auth::user()->hasCelebrationPackAccess();

        return view('client.marriage-invitations.index', compact('templates', 'invitations', 'latestInvitation', 'showDemoThumbnails'));
    }

    public function create()
    {
        $existing = MarriageInvitation::whereIn('user_id', Auth::user()->allowedUserIds())->orderByDesc('updated_at')->first();
        if ($existing) {
            return redirect()
                ->route('client.marriage-invitations.edit', $existing->id)
                ->with('success', 'You already have an invitation. The same details power every print style — edit below.');
        }

        $meta = [
            'name' => 'Your marriage invitation',
            'subtitle' => 'Enter details once, then pick any of our print styles when you view, save, or share.',
            'fields' => config('marriage_invitations.shared_fields'),
        ];

        return view('client.marriage-invitations.create', compact('meta'));
    }

    /**
     * Old URL: /create/{template} — remember layout preference, single form.
     */
    public function createWithTemplateHint(string $template): RedirectResponse
    {
        $templates = config('marriage_invitations.templates', []);
        if (!isset($templates[$template])) {
            abort(404);
        }

        $existing = MarriageInvitation::whereIn('user_id', Auth::user()->allowedUserIds())->orderByDesc('updated_at')->first();
        if ($existing) {
            return redirect()->route('client.marriage-invitations.edit', $existing->id);
        }

        session(['marriage_invitation_create_layout' => $template]);

        return redirect()->route('client.marriage-invitations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->storeInvitation($request, null);
    }

    /**
     * Old POST URL: /template/{template}
     */
    public function storeWithTemplate(Request $request, string $template): RedirectResponse
    {
        $templates = config('marriage_invitations.templates', []);
        if (!isset($templates[$template])) {
            abort(404);
        }

        return $this->storeInvitation($request, $template);
    }

    private function storeInvitation(Request $request, ?string $layoutHint): RedirectResponse
    {
        if (MarriageInvitation::whereIn('user_id', Auth::user()->allowedUserIds())->exists()) {
            $latest = MarriageInvitation::whereIn('user_id', Auth::user()->allowedUserIds())->orderByDesc('updated_at')->first();

            return redirect()
                ->route('client.marriage-invitations.edit', $latest->id)
                ->with('success', 'You already have an invitation. Edit it here — both designs use the same details.');
        }

        $fieldConfig = ['fields' => config('marriage_invitations.shared_fields')];
        $rules = $this->buildValidationRules($fieldConfig);
        $data = $request->validate($rules);
        $data = $this->normalizeInvitationTimes($data);
        $data = $this->mergeInvitationImageUploads($request, $fieldConfig, $data, null);
        $data = $this->normalizeScheduleEvents($data);
        $amount = (float) config('marriage_invitations.amount', 300);

        $keys = MarriageInvitationCard::templateKeys();
        $defaultKey = $keys[0] ?? 'heritage';
        $layout = $layoutHint;
        if ($layout === null || !in_array($layout, $keys, true)) {
            $sessionKey = session()->pull('marriage_invitation_create_layout', $defaultKey);
            $layout = in_array($sessionKey, $keys, true) ? $sessionKey : $defaultKey;
        }
        if (!in_array($layout, $keys, true)) {
            $layout = $defaultKey;
        }

        $invitation = MarriageInvitation::create([
            'user_id' => Auth::user()->id,
            'template_key' => $layout,
            'data' => $data,
            'amount' => $amount,
        ]);

        return redirect()
            ->route('client.marriage-invitations.show', $invitation->id)
            ->with('success', 'Details saved. Pay ₹' . number_format($amount, 0) . ' to unlock download and sharing.');
    }

    public function show(int $id)
    {
        $invitation = MarriageInvitation::whereIn('user_id', Auth::user()->allowedUserIds())->with('upiTransaction')->findOrFail($id);
        $templates = config('marriage_invitations.templates', []);
        $meta = [
            'name' => 'Marriage invitation',
            'subtitle' => 'Same details for every look — pick a style below to view, print, or save as PNG.',
        ];

        return view('client.marriage-invitations.show', compact('invitation', 'meta', 'templates'));
    }

    public function edit(int $id)
    {
        $invitation = MarriageInvitation::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);
        if ($invitation->isUnlocked()) {
            return redirect()
                ->route('client.marriage-invitations.show', $invitation->id)
                ->withErrors(['edit' => 'This card is already paid. Create a new invitation to use different text.']);
        }

        $meta = [
            'name' => 'Edit invitation details',
            'subtitle' => 'One form powers all print styles — change names, date, venue, and photo here.',
            'fields' => config('marriage_invitations.shared_fields'),
        ];
        $data = $invitation->data ?? [];

        return view('client.marriage-invitations.edit', compact('invitation', 'meta', 'data'));
    }

    public function update(Request $request, int $id)
    {
        $invitation = MarriageInvitation::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);
        if ($invitation->isUnlocked()) {
            return back()->withErrors(['edit' => 'Cannot edit after payment. Create a new invitation.']);
        }

        $fieldConfig = ['fields' => config('marriage_invitations.shared_fields')];
        $rules = $this->buildValidationRules($fieldConfig);
        $data = $request->validate($rules);
        $data = $this->normalizeInvitationTimes($data);
        $data = $this->mergeInvitationImageUploads($request, $fieldConfig, $data, $invitation->data ?? []);
        $data = $this->normalizeScheduleEvents($data);
        $invitation->data = $data;
        $invitation->save();

        return redirect()
            ->route('client.marriage-invitations.show', $invitation->id)
            ->with('success', 'Details updated.');
    }

    public function payment(int $id)
    {
        $invitation = MarriageInvitation::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);
        if ($invitation->exportsUnlockedForUser(Auth::user())) {
            return redirect()
                ->route('client.marriage-invitations.show', $invitation->id)
                ->with('success', 'Your invitation is already unlocked for downloads.');
        }

        $amount = (float) $invitation->amount;
        $upiId = config('services.upi.id');
        $upiName = config('services.upi.name', 'Chandla Book');

        $upiUri = null;
        $qrSvg = null;
        if (!empty($upiId)) {
            $upiUri = 'upi://pay?pa=' . urlencode($upiId)
                . '&pn=' . urlencode($upiName)
                . '&am=' . urlencode(number_format($amount, 2, '.', ''))
                . '&cu=INR'
                . '&tn=' . urlencode('Marriage card #' . $invitation->id);
            $qrSvg = QrCode::size(260)->generate($upiUri);
        }

        return view('client.marriage-invitations.payment', compact('invitation', 'amount', 'upiId', 'upiName', 'upiUri', 'qrSvg'));
    }


    public function createRazorpayOrder(Request $request, int $id)
    {
        $invitation = MarriageInvitation::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);
        $ownerId = $invitation->user_id;

        if ($invitation->exportsUnlockedForUser(Auth::user())) {
            return response()->json(['message' => 'Invitation is already unlocked.'], 400);
        }

        $razorpay = RazorpayService::make();
        if (!$razorpay) {
            return response()->json(['message' => 'Razorpay is not configured.'], 503);
        }

        $amount = (float) config('marriage_invitations.amount', 300);
        $amountPaise = (int) round($amount * 100);
        $packageKey = PaymentTransaction::PKG_MARRIAGE_INVITATION;
        $receipt = RazorpayService::generateReceipt($packageKey, $ownerId);

        try {
            $order = $razorpay->createOrder($amountPaise, $receipt, [
                'chandla_type' => 'marriage_inv',
                'invitation_id' => (string) $invitation->id,
                'user_id' => (string) $ownerId,
            ]);

            // Save pending transaction
            $razorpay->createPendingTransaction(
                userId:          $ownerId,
                packageKey:      $packageKey,
                amountInr:       $amount,
                razorpayOrderId: $order['id'],
                referenceId:     (string) $invitation->id,
                metadata:        ['invitation_id' => $invitation->id]
            );

            return response()->json([
                'order_id' => $order['id'],
                'amount' => $razorpay->resolveTestAmount($amountPaise),
                'key_id' => $razorpay->getKeyId(),
                'name' => 'Chandla Book',
                'description' => 'Marriage Invitation Unlock',
                'prefill' => [
                    'email' => Auth::user()?->email,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Could not create payment order.'], 500);
        }
    }

    public function verifyRazorpay(Request $request, int $id)
    {
        $invitation = MarriageInvitation::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);
        $ownerId = $invitation->user_id;

        $validated = $request->validate([
            'razorpay_order_id' => 'required|string|max:64',
            'razorpay_payment_id' => 'required|string|max:64',
            'razorpay_signature' => 'required|string|max:255',
        ]);

        $razorpay = RazorpayService::make();
        if (!$razorpay) {
            return back()->withErrors(['pay' => 'Razorpay verification is not configured.']);
        }

        // Find the pending transaction
        $packageKey = PaymentTransaction::PKG_MARRIAGE_INVITATION;
        $txn = PaymentTransaction::where('razorpay_order_id', $validated['razorpay_order_id'])
            ->where('user_id', $ownerId)
            ->first();

        try {
            $razorpay->verifySignature(
                $validated['razorpay_order_id'],
                $validated['razorpay_payment_id'],
                $validated['razorpay_signature']
            );
        } catch (\Throwable $e) {
            if ($txn) {
                $razorpay->markFailed($txn, 'Signature verification failed: ' . $e->getMessage());
            }
            return back()->withErrors(['pay' => 'Payment verification failed. Contact support with your payment id.']);
        }

        try {
            $fetched = $razorpay->fetchOrder($validated['razorpay_order_id']);
            $expectedPaise = $razorpay->resolveTestAmount((int) round((float) config('marriage_invitations.amount', 300) * 100));
            
            if (isset($fetched['amount']) && (int) $fetched['amount'] !== $expectedPaise) {
                return back()->withErrors(['pay' => 'Amount does not match this invitation.']);
            }

            $notes = $fetched['notes'] ?? [];
            if (($notes['chandla_type'] ?? '') !== 'marriage_inv' || (int) ($notes['invitation_id'] ?? 0) !== (int) $invitation->id
                || (int) ($notes['user_id'] ?? 0) !== (int) $ownerId) {
                return back()->withErrors(['pay' => 'This payment does not belong to this invitation order.']);
            }
        } catch (\Throwable $e) {
            return back()->withErrors(['pay' => 'Could not confirm order details with the gateway.']);
        }

        // Fetch payment to get method
        $paymentData = [];
        $paymentMethod = null;
        try {
            $paymentData = $razorpay->fetchPayment($validated['razorpay_payment_id']);
            $paymentMethod = $paymentData['method'] ?? null;
        } catch (\Throwable) {}

        if (!$txn) {
            $txn = PaymentTransaction::create([
                'user_id' => $ownerId,
                'package_key' => $packageKey,
                'package_name' => PaymentTransaction::packageName($packageKey) . ' ₹' . number_format(config('marriage_invitations.amount', 300), 0),
                'amount_inr' => (float) config('marriage_invitations.amount', 300),
                'currency' => 'INR',
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'status' => PaymentTransaction::STATUS_PENDING,
                'reference_id' => (string) $invitation->id,
            ]);
        }

        $razorpay->markSuccess($txn, $validated['razorpay_payment_id'], $validated['razorpay_signature'], $paymentMethod, $paymentData);

        // Mark as paid
        if (!$invitation->paid_at) {
            $invitation->paid_at = now();
            $invitation->save();
        }

        return redirect()
            ->route('client.marriage-invitations.show', $invitation->id)
            ->with('success', 'Payment successful! Your invitation is now unlocked.');
    }

    public function paymentStore(Request $request, int $id)
    {
        $invitation = MarriageInvitation::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);
        $ownerId = $invitation->user_id;

        if ($invitation->exportsUnlockedForUser(Auth::user())) {
            return redirect()->route('client.marriage-invitations.show', $invitation->id);
        }

        if ($invitation->upi_transaction_id && $invitation->upiTransaction && $invitation->upiTransaction->status === 'pending') {
            return redirect()
                ->route('client.marriage-invitations.show', $invitation->id)
                ->withErrors(['transaction_id' => 'A payment is already waiting for verification.']);
        }

        $validated = $request->validate([
            'transaction_id' => 'required|string|max:255|unique:upi_transactions,transaction_id',
        ]);

        $amount = (float) $invitation->amount;

        $tx = UPITransaction::create([
            'user_id' => $ownerId,
            'event_id' => null,
            'transaction_id' => $validated['transaction_id'],
            'amount' => $amount,
            'status' => 'pending',
            'payment_method' => 'upi',
            'description' => 'Marriage invitation template unlock',
            'metadata' => [
                'type' => 'marriage_invitation',
                'marriage_invitation_id' => $invitation->id,
                'expected_amount' => $amount,
            ],
        ]);

        $invitation->upi_transaction_id = $tx->id;
        $invitation->save();

        return redirect()
            ->route('client.marriage-invitations.show', $invitation->id)
            ->with('success', 'Reference submitted for verification. Prefer Pay with Razorpay on this page next time for instant unlock.');
    }

    /**
     * Render a template with demo data (for iframe thumbnails on the index page). Auth only; no payment check.
     */
    public function templateDemoPreview(Request $request, string $layout)
    {
        $templates = config('marriage_invitations.templates', []);
        if (!isset($templates[$layout])) {
            abort(404);
        }

        $view = 'client.marriage-invitations.templates.' . $layout;
        if (!view()->exists($view)) {
            abort(404);
        }

        $invitation = null;
        if ($request->has('invitation_id')) {
            $invitation = MarriageInvitation::whereIn('user_id', Auth::user()->allowedUserIds())
                ->find($request->query('invitation_id'));
        }
        if (!$invitation) {
            $invitation = MarriageInvitation::whereIn('user_id', Auth::user()->allowedUserIds())
                ->orderByDesc('updated_at')
                ->first();
        }

        if ($invitation && $invitation->exportsUnlockedForUser(Auth::user())) {
            $d = MarriageInvitationCard::mergeUserDataWithDemoDefaults($invitation->data ?? []);
        } else {
            $d = config('marriage_invitations.demo_card_data', []);
            $d = is_array($d) ? $d : [];
        }

        return response()
            ->view($view, [
                'd' => $d,
                'invitation' => $invitation,
                'demoThumbIframe' => true,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function download(Request $request, int $id)
    {
        $invitation = MarriageInvitation::where('user_id', Auth::user()->dataOwnerId())->findOrFail($id);
        if (!$invitation->exportsUnlockedForUser(Auth::user())) {
            abort(403, 'Pay and complete verification to download this card.');
        }

        $layout = MarriageInvitationCard::normalizeLayoutKey(
            $request->query('layout'),
            $invitation->template_key
        );

        $view = 'client.marriage-invitations.templates.' . $layout;
        if (!view()->exists($view)) {
            abort(500, 'Template view missing.');
        }

        $d = MarriageInvitationCard::mergeUserDataWithDemoDefaults($invitation->data ?? []);

        return response()
            ->view($view, [
                'd' => $d,
                'invitation' => $invitation,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    /**
     * Full-page card with client-side PNG capture (html2canvas). Opens in a new tab.
     */
    public function exportPng(Request $request, int $id)
    {
        $invitation = MarriageInvitation::where('user_id', Auth::user()->dataOwnerId())->findOrFail($id);
        if (!$invitation->exportsUnlockedForUser(Auth::user())) {
            abort(403, 'Pay and complete verification to download this card.');
        }

        $layout = MarriageInvitationCard::normalizeLayoutKey(
            $request->query('layout'),
            $invitation->template_key
        );

        $view = 'client.marriage-invitations.templates.' . $layout;
        if (!view()->exists($view)) {
            abort(500, 'Template view missing.');
        }

        $d = MarriageInvitationCard::mergeUserDataWithDemoDefaults($invitation->data ?? []);

        return response()
            ->view($view, [
                'd' => $d,
                'invitation' => $invitation,
                'pngExportScript' => true,
                'pngExportFilename' => $this->invitationCardFilename($d, $layout, 'png'),
                'coupleImageDataUri' => $this->publicImageAsDataUri(($invitation->data ?? [])['couple_image'] ?? null),
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    /**
     * Full-page card with client-side short video (canvas + MediaRecorder). Opens in a new tab.
     */
    public function exportVideo(int $id)
    {
        $invitation = MarriageInvitation::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);
        if (!$invitation->exportsUnlockedForUser(Auth::user())) {
            abort(403, 'Pay and complete verification to download this card.');
        }

        $layout = (string) config('marriage_invitations.video_export_template', 'canva_reel');
        $layout = preg_match('/^[a-z0-9_]+$/', $layout) ? $layout : 'canva_reel';

        $view = 'client.marriage-invitations.templates.' . $layout;
        if (!view()->exists($view)) {
            abort(500, 'Video template view missing.');
        }

        $d = MarriageInvitationCard::mergeUserDataWithDemoDefaults($invitation->data ?? []);
        $fileBase = pathinfo($this->invitationCardFilename($d, 'video', 'webm'), PATHINFO_FILENAME);

        return response()
            ->view($view, [
                'd' => $d,
                'invitation' => $invitation,
                'videoExportScript' => true,
                'videoExportBasename' => $fileBase,
                'videoExportDurationSec' => (int) config('marriage_invitations.video_export_duration_sec', 30),
                'coupleImageDataUri' => $this->publicImageAsDataUri(($invitation->data ?? [])['couple_image'] ?? null),
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    /**
     * Inline image for PNG export so html2canvas always paints it (no lazy-load / CORS timing).
     *
     * @param  mixed  $rawPath
     */
    private function publicImageAsDataUri($rawPath): ?string
    {
        $normalized = $this->normalizePublicDiskPath(is_string($rawPath) ? $rawPath : null);
        if ($normalized === null) {
            return null;
        }

        $full = storage_path('app/public/' . $normalized);
        if (!is_readable($full)) {
            return null;
        }

        $binary = @file_get_contents($full);
        if ($binary === false || $binary === '') {
            return null;
        }

        $mime = @mime_content_type($full);
        if (!is_string($mime) || !str_starts_with($mime, 'image/')) {
            $mime = 'image/jpeg';
        }

        return 'data:'.$mime.';base64,'.base64_encode($binary);
    }

    /**
     * @param  array<string, mixed>  $d
     */
    private function invitationCardFilename(array $d, string $layout, string $ext): string
    {
        $groom = trim((string) ($d['groom_name'] ?? ''));
        $bride = trim((string) ($d['bride_name'] ?? ''));
        $slug = Str::slug($groom !== '' || $bride !== '' ? "{$groom}-{$bride}" : 'invitation');
        if ($slug === '') {
            $slug = 'invitation';
        }

        return "chandla-invitation-{$layout}-{$slug}.{$ext}";
    }

    private function buildValidationRules(array $templateConfig): array
    {
        $rules = [];
        foreach ($templateConfig['fields'] ?? [] as $key => $field) {
            $isReq = !empty($field['required']);
            $type = $field['type'] ?? 'string';
            if ($type === 'date') {
                $suffix = $key === 'wedding_date' ? '|after_or_equal:today' : '';
                $rules[$key] = ($isReq ? 'required|date' : 'nullable|date').$suffix;
            } elseif ($type === 'time') {
                $rules[$key] = $isReq ? 'required|date_format:H:i' : 'nullable|date_format:H:i';
            } elseif ($type === 'textarea') {
                $rules[$key] = $isReq ? 'required|string|max:2000' : 'nullable|string|max:2000';
            } elseif ($type === 'image') {
                $rules[$key] = $isReq
                    ? 'required|image|mimes:jpeg,png,jpg,webp'
                    : 'nullable|image|mimes:jpeg,png,jpg,webp';
            } elseif ($type === 'schedule') {
                $rules[$key] = $isReq ? 'required|array|max:12' : 'nullable|array|max:12';
                $rules[$key.'.*.title'] = 'nullable|string|max:120';
                $rules[$key.'.*.date'] = 'nullable|date';
                $rules[$key.'.*.time'] = 'nullable|string|max:40';
            } else {
                $rules[$key] = $isReq ? 'required|string|max:500' : 'nullable|string|max:500';
            }
        }

        return $rules;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @param  array<string, mixed>|null  $existingData
     * @return array<string, mixed>
     */
    private function mergeInvitationImageUploads(Request $request, array $templateConfig, array $validated, ?array $existingData): array
    {
        $existingData = $existingData ?? [];
        $disk = Storage::disk('public');

        foreach ($templateConfig['fields'] ?? [] as $key => $field) {
            if (($field['type'] ?? '') !== 'image') {
                continue;
            }
            unset($validated[$key]);
            if ($request->hasFile($key)) {
                $oldPath = $this->normalizePublicDiskPath(
                    is_string($existingData[$key] ?? null) ? $existingData[$key] : null
                );
                if ($oldPath !== null && $disk->exists($oldPath)) {
                    $disk->delete($oldPath);
                }
                $validated[$key] = $request->file($key)->store('marriage_invitations/' . Auth::user()->id, 'public');
            } elseif (array_key_exists($key, $existingData) && $existingData[$key] !== '') {
                $kept = $this->normalizePublicDiskPath(
                    is_string($existingData[$key]) ? $existingData[$key] : null
                );
                $validated[$key] = $kept !== null ? $kept : $existingData[$key];
            }
        }

        return $validated;
    }

    /**
     * Path relative to the public disk (e.g. marriage_invites/1/x.jpg), never prefixed with "storage/".
     */
    private function normalizePublicDiskPath(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }
        $path = ltrim(str_replace('\\', '/', $path), '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        return $path;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeInvitationTimes(array $data): array
    {
        if (! array_key_exists('wedding_time', $data)) {
            return $data;
        }

        $v = trim((string) ($data['wedding_time'] ?? ''));
        if ($v === '') {
            $data['wedding_time'] = '';

            return $data;
        }

        try {
            $data['wedding_time'] = Carbon::parse($v)->format('H:i');
        } catch (\Throwable) {
            $data['wedding_time'] = '';
        }

        return $data;
    }

    private function normalizeScheduleEvents(array $data): array
    {
        if (empty($data['schedule_events']) || !is_array($data['schedule_events'])) {
            unset($data['schedule_events']);

            return $data;
        }

        $rows = [];
        foreach ($data['schedule_events'] as $row) {
            if (!is_array($row)) {
                continue;
            }
            $title = trim((string) ($row['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $dateVal = $row['date'] ?? null;
            $rows[] = [
                'title' => $title,
                'date' => $dateVal !== '' && $dateVal !== null ? $dateVal : null,
                'time' => trim((string) ($row['time'] ?? '')),
            ];
        }

        if ($rows === []) {
            unset($data['schedule_events']);
        } else {
            $data['schedule_events'] = array_values($rows);
        }

        return $data;
    }
}
