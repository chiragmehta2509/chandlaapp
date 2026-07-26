<?php

namespace App\Http\Controllers\Api\Event;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\EventUnlimitedRazorpayCompletion;
use App\Services\DirectGpayUnlockRazorpayCompletion;
use App\Services\GuestPayPackUnlock;
use App\Services\RazorpayService;
use App\Support\RazorpayTestAmount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Razorpay\Api\Api;

class EventController extends Controller
{
    private function userEvents(Request $request)
    {
        $authUser = $request->user();
        $ownerId  = $authUser->dataOwnerId();
        $selfId   = $authUser->id;

        if ($authUser->isFamilyMember() && $selfId !== $ownerId) {
            return Event::where(function ($q) use ($ownerId, $selfId) {
                $q->where('user_id', $ownerId)->orWhere('user_id', $selfId);
            });
        }

        return Event::where('user_id', $ownerId);
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $query = $this->userEvents($request)->with(['entries', 'invitations']);

        if ($request->has('type')) {
            $query->where('event_type', $request->type);
        }

        if ($request->has('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $events = $query->orderBy('event_date', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    public function upcoming(Request $request)
    {
        $events = $this->userEvents($request)
            ->active()
            ->upcoming()
            ->orderBy('event_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    public function past(Request $request)
    {
        $events = $this->userEvents($request)
            ->active()
            ->past()
            ->orderBy('event_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    public function archived(Request $request)
    {
        $events = $this->userEvents($request)
            ->archived()
            ->orderBy('archived_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    public function show(Request $request, $id)
    {
        $event = $this->userEvents($request)
            ->with(['entries.contact', 'invitations', 'collaborators'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $event
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'event_time' => 'nullable|date_format:H:i',
            'venue' => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'event_type' => 'nullable|string|in:wedding,birthday,anniversary,other',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'title', 'description', 'event_date', 'event_time', 
            'venue', 'event_type'
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('events', 'public');
        }

        $data['user_id'] = $request->user()->dataOwnerId();
        $event = Event::create($data);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'create_event',
            'model_type' => Event::class,
            'model_id' => $event->id,
            'new_values' => $event->toArray(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'data' => $event
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'nullable|date',
            'event_time' => 'nullable|date_format:H:i',
            'venue' => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'event_type' => 'nullable|string|in:wedding,birthday,anniversary,other',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $oldValues = $event->toArray();
        $data = $request->only([
            'title', 'description', 'event_date', 'event_time', 
            'venue', 'event_type'
        ]);

        if ($request->hasFile('cover_image')) {
            if ($event->cover_image) {
                Storage::disk('public')->delete($event->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('events', 'public');
        }

        $event->update($data);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'update_event',
            'model_type' => Event::class,
            'model_id' => $event->id,
            'old_values' => $oldValues,
            'new_values' => $event->toArray(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data' => $event
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        if ($event->cover_image) {
            Storage::disk('public')->delete($event->cover_image);
        }

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delete_event',
            'model_type' => Event::class,
            'model_id' => $event->id,
            'old_values' => $event->toArray(),
            'ip_address' => $request->ip(),
        ]);

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    }

    public function archive(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        $event->update([
            'is_archived' => true,
            'archived_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event archived successfully',
            'data' => $event
        ]);
    }

    public function unarchive(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        $event->update([
            'is_archived' => false,
            'archived_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event unarchived successfully',
            'data' => $event
        ]);
    }

    public function duplicate(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        $newEvent = $event->replicate();
        $newEvent->title = $event->title . ' (Copy)';
        $newEvent->is_archived = false;
        $newEvent->archived_at = null;
        $newEvent->save();

        return response()->json([
            'success' => true,
            'message' => 'Event duplicated successfully',
            'data' => $newEvent
        ], 201);
    }

    public function getStats(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        $stats = [
            'total_entries' => $event->entries()->count(),
            'confirmed_entries' => $event->entries()->confirmed()->count(),
            'pending_entries' => $event->entries()->pending()->count(),
            'declined_entries' => $event->entries()->declined()->count(),
            'total_invitations' => $event->invitations()->count(),
            'sent_invitations' => $event->invitations()->sent()->count(),
            'opened_invitations' => $event->invitations()->opened()->count(),
            'accepted_invitations' => $event->invitations()->accepted()->count(),
            'total_guests' => $event->entries()->sum('adults_count') + $event->entries()->sum('children_count'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function getCollaborators(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);
        $collaborators = $event->collaborators;

        return response()->json([
            'success' => true,
            'data' => $collaborators
        ]);
    }

    public function addCollaborator(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|in:owner,editor,viewer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $event->collaborators()->syncWithoutDetaching([
            $request->user_id => ['role' => $request->role]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Collaborator added successfully'
        ]);
    }

    public function updateCollaborator(Request $request, $id, $userId)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'role' => 'required|string|in:owner,editor,viewer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $event->collaborators()->updateExistingPivot($userId, [
            'role' => $request->role
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Collaborator updated successfully'
        ]);
    }

    public function removeCollaborator(Request $request, $id, $userId)
    {
        $event = $this->userEvents($request)->findOrFail($id);
        $event->collaborators()->detach($userId);

        return response()->json([
            'success' => true,
            'message' => 'Collaborator removed successfully'
        ]);
    }

    public function sync(Request $request)
    {
        // Handle offline sync
        $validator = Validator::make($request->all(), [
            'events' => 'required|array',
            'events.*.id' => 'nullable|integer',
            'events.*.title' => 'required|string',
            'events.*.event_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->user()->dataOwnerId();
        $synced = [];
        foreach ($request->events as $eventData) {
            if (isset($eventData['id'])) {
                $event = Event::where('user_id', $userId)->find($eventData['id']);
                if ($event) {
                    $event->update($eventData);
                    $synced[] = $event;
                }
            } else {
                $eventData['user_id'] = $userId;
                $synced[] = Event::create($eventData);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Events synced successfully',
            'data' => $synced
        ]);
    }

    public function createPlanRazorpayOrder(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        if ($event->pricing_plan === 'unlimited' || $event->unlimited_purchased_at) {
            return response()->json([
                'success' => false,
                'message' => 'This event is already on the Unlimited plan.'
            ], 400);
        }

        $key = config('services.razorpay.key_id');
        $secret = config('services.razorpay.key_secret');
        if (empty($key) || empty($secret)) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay is not configured. Set RAZORPAY_KEY_ID and RAZORPAY_KEY_SECRET.'
            ], 503);
        }

        $amount = (float) ($event->unlimited_price ?? 500);
        $amountPaise = RazorpayTestAmount::resolve((int) round($amount * 100));

        try {
            $api = new Api($key, $secret);
            $receipt = 'evt_' . $event->id . '_' . Str::lower(Str::random(6));
            $order = $api->order->create([
                'receipt' => $receipt,
                'amount' => $amountPaise,
                'currency' => 'INR',
                'payment_capture' => 1,
                'notes' => [
                    'chandla_type' => 'event_unlimited',
                    'event_id' => (string) $event->id,
                    'user_id' => (string) $event->user_id,
                ],
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order['id'],
                    'amount' => $amountPaise,
                    'key_id' => $key,
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not create payment order.'
            ], 500);
        }
    }

    public function verifyPlanRazorpay(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'razorpay_order_id' => 'required|string|max:64',
            'razorpay_payment_id' => 'required|string|max:64',
            'razorpay_signature' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $key = config('services.razorpay.key_id');
        $secret = config('services.razorpay.key_secret');
        if (empty($secret)) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay verification is not configured.'
            ], 503);
        }

        try {
            $api = new Api($key, $secret);
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature' => $validated['razorpay_signature'],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment signature verification failed.'
            ], 400);
        }

        try {
            $fetched = $api->order->fetch($validated['razorpay_order_id']);
            $expectedPaise = RazorpayTestAmount::resolve((int) round((float) ($event->unlimited_price ?? 500) * 100));
            if (isset($fetched['amount']) && (int) $fetched['amount'] !== $expectedPaise) {
                return response()->json([
                    'success' => false,
                    'message' => 'Amount does not match this event.'
                ], 400);
            }

            $notes = $fetched['notes'] ?? [];
            if (($notes['chandla_type'] ?? '') !== 'event_unlimited' || (int) ($notes['event_id'] ?? 0) !== (int) $event->id
                || (int) ($notes['user_id'] ?? 0) !== (int) $event->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'This payment does not belong to this event order.'
                ], 400);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not confirm order details with the gateway.'
            ], 500);
        }

        $ok = EventUnlimitedRazorpayCompletion::applyIfNeeded(
            $event,
            $event->user_id,
            $validated['razorpay_payment_id'],
            $validated['razorpay_order_id']
        );
        if (! $ok) {
            return response()->json([
                'success' => false,
                'message' => 'Could not complete upgrade.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment successful. Your event is now on the Unlimited plan.'
        ]);
    }

    public function createDirectGpayRazorpayOrder(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        if ($event->hasDirectGpayQrUnlocked()) {
            return response()->json([
                'success' => false,
                'message' => 'Direct GPay is already unlocked for this event.'
            ], 409);
        }

        $razorpay = RazorpayService::make();
        if (!$razorpay) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay is not configured. Set RAZORPAY_KEY_ID and RAZORPAY_KEY_SECRET.'
            ], 503);
        }

        $amount = (float) config('services.direct_gpay_unlock.amount', 400);
        $amountPaise = (int) round($amount * 100);
        $packageKey = PaymentTransaction::PKG_DIRECT_GPAY;
        $receipt = RazorpayService::generateReceipt($packageKey, $event->user_id);

        try {
            $order = $razorpay->createOrder($amountPaise, $receipt, [
                'chandla_type' => 'direct_gpay_unlock',
                'event_id' => (string) $event->id,
                'user_id' => (string) $event->user_id,
            ]);

            // Save pending transaction
            $razorpay->createPendingTransaction(
                userId:          $event->user_id,
                packageKey:      $packageKey,
                amountInr:       $amount,
                razorpayOrderId: $order['id'],
                referenceId:     (string) $event->id,
                metadata:        ['event_id' => $event->id]
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order['id'],
                    'amount' => $razorpay->resolveTestAmount($amountPaise),
                    'key_id' => $razorpay->getKeyId(),
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not create payment order.'
            ], 500);
        }
    }

    public function verifyDirectGpayRazorpay(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        if ($event->hasDirectGpayQrUnlocked()) {
            return response()->json([
                'success' => true,
                'message' => 'Direct GPay QR is already unlocked for ' . $event->title . '.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'razorpay_order_id' => 'required|string|max:64',
            'razorpay_payment_id' => 'required|string|max:64',
            'razorpay_signature' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $razorpay = RazorpayService::make();
        if (!$razorpay) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay verification is not configured.'
            ], 503);
        }

        // Find the pending transaction
        $packageKey = PaymentTransaction::PKG_DIRECT_GPAY;
        $txn = PaymentTransaction::where('razorpay_order_id', $validated['razorpay_order_id'])
            ->where('user_id', $event->user_id)
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
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed.'
            ], 400);
        }

        try {
            $fetched = $razorpay->fetchOrder($validated['razorpay_order_id']);
            $expectedPaise = $razorpay->resolveTestAmount((int) round((float) config('services.direct_gpay_unlock.amount', 400) * 100));
            if (isset($fetched['amount']) && (int) $fetched['amount'] !== $expectedPaise) {
                return response()->json([
                    'success' => false,
                    'message' => 'Amount does not match this unlock price.'
                ], 400);
            }

            $notes = $fetched['notes'] ?? [];
            if (($notes['chandla_type'] ?? '') !== 'direct_gpay_unlock' || (int) ($notes['event_id'] ?? 0) !== (int) $event->id
                || (int) ($notes['user_id'] ?? 0) !== (int) $event->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'This payment does not belong to this event order.'
                ], 400);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not confirm order details with the gateway.'
            ], 500);
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
                'user_id' => $event->user_id,
                'package_key' => $packageKey,
                'package_name' => PaymentTransaction::packageName($packageKey) . ' ₹' . number_format(config('services.direct_gpay_unlock.amount', 400), 0),
                'amount_inr' => (float) config('services.direct_gpay_unlock.amount', 400),
                'currency' => 'INR',
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'status' => PaymentTransaction::STATUS_PENDING,
                'reference_id' => (string) $event->id,
            ]);
        }

        $razorpay->markSuccess($txn, $validated['razorpay_payment_id'], $validated['razorpay_signature'], $paymentMethod, $paymentData);

        $ok = DirectGpayUnlockRazorpayCompletion::applyIfNeeded(
            $event,
            $event->user_id,
            $validated['razorpay_payment_id'],
            $validated['razorpay_order_id']
        );
        if (! $ok) {
            return response()->json([
                'success' => false,
                'message' => 'Could not complete unlock.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Direct GPay QR is unlocked for ' . $event->title . '.'
        ]);
    }

    public function redeemGuestPayPack(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        if ($event->hasDirectGpayQrUnlocked()) {
            return response()->json([
                'success' => true,
                'message' => 'Direct GPay QR is already unlocked for ' . $event->title . '.'
            ]);
        }

        $user = $request->user();
        if ((int) ($user->guest_pay_single_event_credits ?? 0) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'No Guest Contribution credit on your account.'
            ], 400);
        }

        try {
            DB::transaction(function () use ($event, $user) {
                $locked = User::whereKey($user->id)->lockForUpdate()->first();
                if ($locked === null || (int) ($locked->guest_pay_single_event_credits ?? 0) < 1) {
                    throw new \RuntimeException('no_credits');
                }
                $locked->guest_pay_single_event_credits = (int) $locked->guest_pay_single_event_credits - 1;
                $locked->save();
                if (! GuestPayPackUnlock::applyFromPackCredit($event, $locked)) {
                    throw new \RuntimeException('unlock_failed');
                }
            });
        } catch (\RuntimeException $e) {
            $msg = $e->getMessage();
            if ($msg === 'no_credits') {
                return response()->json([
                    'success' => false,
                    'message' => 'No credits left.'
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'Could not apply pack credit.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Guest Contribution applied to ' . $event->title . '.'
        ]);
    }
}
