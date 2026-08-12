<?php

namespace App\Http\Controllers\Api\Chandla;

use App\Http\Controllers\Controller;
use App\Models\Chandla;
use App\Models\Event;
use App\Models\User;
use App\Models\ActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChandlaController extends Controller
{
    private function denominations(): array
    {
        return [1, 2, 5, 10, 20, 50, 100, 200, 500];
    }

    private function emptyNotes(): array
    {
        return array_fill_keys($this->denominations(), 0);
    }

    private function sumNotes(array $notes): float
    {
        $total = 0;
        foreach ($notes as $denom => $count) {
            $total += $denom * $count;
        }
        return $total;
    }

    public function downloadPdf(Request $request)
    {
        /** @var \App\Models\User $authUser */
        $authUser = $request->user();
        $ownerId = $authUser->dataOwnerId();
        if ($authUser->isFamilyMember() && Event::where('user_id', $authUser->id)->exists()) {
            $ownerId = $authUser->id;
        }

        $owner = User::find($ownerId) ?? $authUser;

        $entries = Chandla::with('event')
            ->where('user_id', $ownerId)
            ->orderBy('received_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $cash = $entries->where('category', 'chandla')->where('payment_method', '!=', 'gpay')
            ->sortBy(fn ($row) => mb_strtolower(trim((string) $row->giver_name)))
            ->values();
        $gpay = $entries->where('payment_method', 'gpay')
            ->sortBy(fn ($row) => mb_strtolower(trim((string) $row->giver_name)))
            ->values();
        $cover = $entries->where('category', 'cover')
            ->sortBy(fn ($row) => mb_strtolower(trim((string) $row->giver_name)))
            ->values();
        $gift = $entries->where('category', 'gift')
            ->sortBy(fn ($row) => mb_strtolower(trim((string) $row->giver_name)))
            ->values();
            
        $gujaratiFontPath = $this->resolvePdfGujaratiFontPath();

        $pdf = Pdf::loadView('client.chandlas.ledger-pdf', [
            'cash'             => $cash,
            'gpay'             => $gpay,
            'cover'            => $cover,
            'gift'             => $gift,
            'user'             => $owner,
            'gujaratiFontPath' => $gujaratiFontPath,
        ]);

        $this->decorateEventChandlaPdf($pdf);

        return $pdf->download('entire-ledger.pdf');
    }

    private function resolvePdfGujaratiFontPath(): ?string
    {
        $fontDirectory = storage_path('fonts');
        $projectFontPath = $fontDirectory . DIRECTORY_SEPARATOR . 'gujarati.ttf';

        if (is_file($projectFontPath)) {
            return $projectFontPath;
        }
        return null;
    }

    private function decorateEventChandlaPdf(\Barryvdh\DomPDF\PDF $pdf): void
    {
        try {
            $dompdf = $pdf->getDomPDF();
            $canvas = $dompdf->getCanvas();
            $fontMetrics = $dompdf->getFontMetrics();
            $font = $fontMetrics->get_font('DejaVu Sans', 'normal');
            if (!$font) {
                return;
            }

            $muted = [0.38, 0.41, 0.45];
            $canvas->page_text(34, 806, 'Chandla Book • ' . config('app.name'), $font, 7.5, $muted);
            $canvas->page_text(238, 806, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, 9, [0.18, 0.23, 0.29]);
        } catch (\Throwable $e) {
            Log::warning('Event PDF footer chrome skipped', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $userId = $request->user()->dataOwnerId();

        $query = Chandla::where('user_id', $userId)->with('event');

        if ($request->has('event_id')) {
            $query->where('event_id', $request->event_id);
        }
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('giver_name', 'like', "%{$search}%")
                  ->orWhere('giver_phone', 'like', "%{$search}%")
                  ->orWhere('giver_email', 'like', "%{$search}%");
            });
        }

        $chandlas = $query->orderBy('received_date', 'desc')->orderBy('id', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $chandlas
        ]);
    }

    public function show(Request $request, $id)
    {
        $userId = $request->user()->dataOwnerId();
        $chandla = Chandla::where('user_id', $userId)->with('event')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $chandla
        ]);
    }

    public function stats(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'nullable|exists:events,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId  = $request->user()->dataOwnerId();
        $eventId = $request->input('event_id');

        // ── Single-event stats ─────────────────────────────────────────────
        if ($eventId) {
            // Ensure the event belongs to this user
            Event::where('user_id', $userId)->findOrFail($eventId);

            $stats = Chandla::where('user_id', $userId)
                ->where('event_id', $eventId)
                ->selectRaw("
                    SUM(amount) as total_amount,
                    SUM(CASE WHEN payment_method = 'cash'      THEN amount ELSE 0 END) as cash_amount,
                    SUM(CASE WHEN payment_method = 'gpay'      THEN amount ELSE 0 END) as gpay_amount,
                    SUM(CASE WHEN payment_method = 'hard_form' THEN amount ELSE 0 END) as hard_form_amount,
                    SUM(CASE WHEN payment_method = 'other'     THEN amount ELSE 0 END) as other_amount,
                    SUM(CASE WHEN category = 'cover' THEN 1 ELSE 0 END) as cover_count,
                    SUM(CASE WHEN category = 'gift' THEN 1 ELSE 0 END) as gift_count,
                    SUM(CASE WHEN payment_method = 'gpay' THEN 1 ELSE 0 END) as gpay_txns,
                    COUNT(*) as total_entries
                ")
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'event_id'         => (int) $eventId,
                    'total_amount'     => (float) ($stats->total_amount     ?? 0),
                    'cash_amount'      => (float) ($stats->cash_amount      ?? 0),
                    'gpay_amount'      => (float) ($stats->gpay_amount      ?? 0),
                    'hard_form_amount' => (float) ($stats->hard_form_amount ?? 0),
                    'other_amount'     => (float) ($stats->other_amount     ?? 0),
                    'cover_count'      => (int)   ($stats->cover_count      ?? 0),
                    'gift_count'       => (int)   ($stats->gift_count       ?? 0),
                    'gpay_txns'        => (int)   ($stats->gpay_txns        ?? 0),
                    'total_entries'    => (int)   ($stats->total_entries    ?? 0),
                ]
            ]);
        }

        // ── All-events aggregate stats ─────────────────────────────────────
        $overall = Chandla::where('user_id', $userId)
            ->selectRaw("
                SUM(amount) as total_amount,
                SUM(CASE WHEN payment_method = 'cash'      THEN amount ELSE 0 END) as cash_amount,
                SUM(CASE WHEN payment_method = 'gpay'      THEN amount ELSE 0 END) as gpay_amount,
                SUM(CASE WHEN payment_method = 'hard_form' THEN amount ELSE 0 END) as hard_form_amount,
                SUM(CASE WHEN payment_method = 'other'     THEN amount ELSE 0 END) as other_amount,
                SUM(CASE WHEN category = 'cover' THEN 1 ELSE 0 END) as cover_count,
                SUM(CASE WHEN category = 'gift' THEN 1 ELSE 0 END) as gift_count,
                SUM(CASE WHEN payment_method = 'gpay' THEN 1 ELSE 0 END) as gpay_txns,
                COUNT(*) as total_entries,
                COUNT(DISTINCT event_id) as total_events
            ")
            ->first();

        // Per-event breakdown
        $perEvent = Chandla::where('chandlas.user_id', $userId)
            ->join('events', 'events.id', '=', 'chandlas.event_id')
            ->selectRaw("
                chandlas.event_id,
                events.title as event_title,
                SUM(chandlas.amount) as total_amount,
                SUM(CASE WHEN chandlas.payment_method = 'cash'      THEN chandlas.amount ELSE 0 END) as cash_amount,
                SUM(CASE WHEN chandlas.payment_method = 'gpay'      THEN chandlas.amount ELSE 0 END) as gpay_amount,
                SUM(CASE WHEN chandlas.payment_method = 'hard_form' THEN chandlas.amount ELSE 0 END) as hard_form_amount,
                SUM(CASE WHEN chandlas.payment_method = 'other'     THEN chandlas.amount ELSE 0 END) as other_amount,
                SUM(CASE WHEN chandlas.category = 'cover' THEN 1 ELSE 0 END) as cover_count,
                SUM(CASE WHEN chandlas.category = 'gift' THEN 1 ELSE 0 END) as gift_count,
                SUM(CASE WHEN chandlas.payment_method = 'gpay' THEN 1 ELSE 0 END) as gpay_txns,
                COUNT(*) as total_entries
            ")
            ->groupBy('chandlas.event_id', 'events.title')
            ->orderByDesc('total_amount')
            ->get()
            ->map(fn($row) => [
                'event_id'         => (int)   $row->event_id,
                'event_title'      =>          $row->event_title,
                'total_amount'     => (float)  $row->total_amount,
                'cash_amount'      => (float)  $row->cash_amount,
                'gpay_amount'      => (float)  $row->gpay_amount,
                'hard_form_amount' => (float)  $row->hard_form_amount,
                'other_amount'     => (float)  $row->other_amount,
                'cover_count'      => (int)    $row->cover_count,
                'gift_count'       => (int)    $row->gift_count,
                'gpay_txns'        => (int)    $row->gpay_txns,
                'total_entries'    => (int)    $row->total_entries,
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'overall' => [
                    'total_amount'     => (float) ($overall->total_amount     ?? 0),
                    'cash_amount'      => (float) ($overall->cash_amount      ?? 0),
                    'gpay_amount'      => (float) ($overall->gpay_amount      ?? 0),
                    'hard_form_amount' => (float) ($overall->hard_form_amount ?? 0),
                    'other_amount'     => (float) ($overall->other_amount     ?? 0),
                    'cover_count'      => (int)   ($overall->cover_count      ?? 0),
                    'gift_count'       => (int)   ($overall->gift_count       ?? 0),
                    'gpay_txns'        => (int)   ($overall->gpay_txns        ?? 0),
                    'total_entries'    => (int)   ($overall->total_entries    ?? 0),
                    'total_events'     => (int)   ($overall->total_events     ?? 0),
                ],
                'per_event' => $perEvent,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'giver_name' => 'required|string|max:255',
            'giver_phone' => 'nullable|string',
            'giver_email' => 'nullable|email',
            'giver_address' => 'nullable|string',
            'category' => 'required|in:chandla,cover,gift',
            'payment_method' => 'nullable|in:cash,gpay,hard_form,other',
            'gpay_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'gpay_transaction_id' => 'nullable|required_if:payment_method,gpay|string|max:255',
            'amount' => 'required_if:category,chandla|nullable|numeric|min:0',
            'gift_item_name' => 'nullable|required_if:category,gift|string|max:255',
            'gift_received' => 'nullable|required_if:category,gift|boolean',
            'received_date' => 'required|date',
            'receipt_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->user()->dataOwnerId();
        $event = Event::where('user_id', $userId)->findOrFail($request->event_id);

        $data = $request->only([
            'event_id', 'giver_name', 'giver_phone', 'giver_email', 'giver_address',
            'category', 'payment_method', 'amount', 'gift_item_name', 'gift_received',
            'gpay_transaction_id', 'received_date', 'receipt_number', 'notes', 'description'
        ]);

        $data['user_id'] = $userId;

        // Apply sensible cash note defaults
        foreach ($this->denominations() as $denom) {
            $data['cash_note_' . $denom] = (int) $request->input('cash_note_' . $denom, 0);
            $data['change_note_' . $denom] = 0;
        }

        $data['change_amount'] = 0;
        $data['change_status'] = null;

        // Process gift / cover fields
        if ($data['category'] === 'gift') {
            $data['payment_method'] = 'other';
            $data['amount'] = 0;
        } elseif ($data['category'] === 'cover') {
            if (($data['payment_method'] ?? null) === 'cash') {
                $notes = [];
                foreach ($this->denominations() as $denom) {
                    $notes[$denom] = $data['cash_note_' . $denom];
                }
                $data['amount'] = $this->sumNotes($notes);
            } else {
                $data['amount'] = $data['amount'] ?? 0;
            }
        } else {
            if (($data['payment_method'] ?? null) === 'cash') {
                $notes = [];
                foreach ($this->denominations() as $denom) {
                    $notes[$denom] = $data['cash_note_' . $denom];
                }
                $receivedTotal = $this->sumNotes($notes);
                if ($receivedTotal > 0 && $data['amount'] !== null) {
                    $data['change_amount'] = max(0, $receivedTotal - (float)$data['amount']);
                    if ($data['change_amount'] > 0) {
                        $data['change_status'] = 'returned';
                    }
                }
            }
        }

        // Handle GPay transaction image upload
        if (($data['payment_method'] ?? null) === 'gpay' && $request->hasFile('gpay_image')) {
            $data['gpay_image'] = $request->file('gpay_image')->store('gpay_images', 'public');
        }

        $chandla = Chandla::create($data);

        if (!empty($chandla->giver_phone)) {
            try {
                $waService = new \App\Services\WhatsAppService();
                $cleanPhone = preg_replace('/^\+?91/', '', $chandla->giver_phone);
                $waService->sendTemplateMessage(
                    to: '91' . $cleanPhone,
                    templateName: 'chandla_added',
                    languageCode: 'en',
                    components: [
                        [
                            'type' => 'body',
                            'parameters' => [
                                \App\Services\WhatsAppService::formatTextParameter($chandla->giver_name ?? 'Guest'),
                                \App\Services\WhatsAppService::formatTextParameter((string) ($chandla->amount ?? 0))
                            ]
                        ]
                    ]
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('WhatsApp chandla_added failed', [
                    'chandla_id' => $chandla->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'create_chandla',
            'model_type' => Chandla::class,
            'model_id' => $chandla->id,
            'new_values' => $chandla->toArray(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Chandla created successfully',
            'data' => $chandla->load('event')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $userId = $request->user()->dataOwnerId();
        $chandla = Chandla::where('user_id', $userId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'giver_name' => 'nullable|string|max:255',
            'giver_phone' => 'nullable|string',
            'giver_email' => 'nullable|email',
            'giver_address' => 'nullable|string',
            'category' => 'nullable|in:chandla,cover,gift',
            'payment_method' => 'nullable|in:cash,gpay,hard_form,other',
            'gpay_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'gpay_transaction_id' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'gift_item_name' => 'nullable|string|max:255',
            'gift_received' => 'nullable|boolean',
            'received_date' => 'nullable|date',
            'receipt_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $oldValues = $chandla->toArray();

        $updateData = $request->only([
            'giver_name', 'giver_phone', 'giver_email', 'giver_address',
            'category', 'payment_method', 'amount', 'gift_item_name', 'gift_received',
            'gpay_transaction_id', 'received_date', 'receipt_number', 'notes', 'description'
        ]);

        // Handing GPay image
        if ($request->hasFile('gpay_image')) {
            if ($chandla->gpay_image && Storage::disk('public')->exists($chandla->gpay_image)) {
                Storage::disk('public')->delete($chandla->gpay_image);
            }
            $updateData['gpay_image'] = $request->file('gpay_image')->store('gpay_images', 'public');
        }

        // Apply denomination updates if provided
        foreach ($this->denominations() as $denom) {
            if ($request->has('cash_note_' . $denom)) {
                $updateData['cash_note_' . $denom] = (int) $request->input('cash_note_' . $denom);
            }
        }

        $chandla->update($updateData);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'update_chandla',
            'model_type' => Chandla::class,
            'model_id' => $chandla->id,
            'old_values' => $oldValues,
            'new_values' => $chandla->toArray(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Chandla updated successfully',
            'data' => $chandla->load('event')
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $userId = $request->user()->dataOwnerId();
        $chandla = Chandla::where('user_id', $userId)->findOrFail($id);

        if ($chandla->gpay_image && Storage::disk('public')->exists($chandla->gpay_image)) {
            Storage::disk('public')->delete($chandla->gpay_image);
        }

        $oldValues = $chandla->toArray();
        $chandla->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delete_chandla',
            'model_type' => Chandla::class,
            'model_id' => $id,
            'old_values' => $oldValues,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Chandla deleted successfully'
        ]);
    }
}
