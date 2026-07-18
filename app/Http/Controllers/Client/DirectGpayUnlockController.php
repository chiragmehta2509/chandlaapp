<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\DirectGpayUnlockRazorpayCompletion;
use App\Services\GuestPayPackUnlock;
use App\Services\RazorpayService;
use App\Support\RazorpayTestAmount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DirectGpayUnlockController extends Controller
{
    /**
     * Old global URL: send users to event list.
     */
    public function redirectLegacy()
    {
        return redirect()
            ->route('client.events.index')
            ->with('info', 'Direct GPay is unlocked per event. Open an event and use “Unlock Direct QR” for that event.');
    }

    public function show(Event $event)
    {
        $this->assertOrganizerEvent($event);

        if ($event->hasDirectGpayQrUnlocked()) {
            return redirect()
                ->route('client.events.index')
                ->with('success', 'Direct GPay QR is already unlocked for ' . $event->title . '.');
        }

        $amount = (float) config('services.direct_gpay_unlock.amount', 400);
        $keyId = config('services.razorpay.key_id');
        $guestPayPackCredits = (int) (Auth::user()->guest_pay_single_event_credits ?? 0);
        $guestPayPackInr = (float) config('packs.guest_pay_single.amount_inr', 400);

        return view('client.direct-gpay-unlock.payment', compact('event', 'amount', 'keyId', 'guestPayPackCredits', 'guestPayPackInr'));
    }

    public function redeemGuestPayPack(Event $event)
    {
        $this->assertOrganizerEvent($event);

        if ($event->hasDirectGpayQrUnlocked()) {
            return redirect()
                ->route('client.events.index')
                ->with('success', 'Direct GPay QR is already unlocked for ' . $event->title . '.');
        }

        $user = Auth::user();
        if ((int) ($user->guest_pay_single_event_credits ?? 0) < 1) {
            return redirect()
                ->route('client.events.direct-gpay-unlock.show', $event)
                ->withErrors(['pack' => 'No Guest Contribution credit on your account. Buy the ₹' . number_format((float) config('packs.guest_pay_single.amount_inr', 400), 0) . ' single-event pack on the website, then return here.']);
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
                return redirect()
                    ->route('client.events.direct-gpay-unlock.show', $event)
                    ->withErrors(['pack' => 'No credits left. Refresh the page or buy another pack.']);
            }

            return redirect()
                ->route('client.events.direct-gpay-unlock.show', $event)
                ->withErrors(['pack' => 'Could not apply pack. Try again or contact support.']);
        }

        return redirect()
            ->route('client.events.index')
            ->with('success', 'Guest Contribution applied to ' . $event->title . '. Set your UPI or QR on the event, then open Direct GPay to share the guest link — chandla entries are unlimited for this event.');
    }

    public function createRazorpayOrder(Request $request, Event $event)
    {
        $this->assertOrganizerEvent($event);

        if ($event->hasDirectGpayQrUnlocked()) {
            return response()->json(['message' => 'Direct GPay is already unlocked for this event.'], 409);
        }

        $razorpay = RazorpayService::make();
        if (!$razorpay) {
            return response()->json(['message' => 'Razorpay is not configured. Set RAZORPAY_KEY_ID and RAZORPAY_KEY_SECRET.'], 503);
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
                'order_id' => $order['id'],
                'amount' => $razorpay->resolveTestAmount($amountPaise),
                'key_id' => $razorpay->getKeyId(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Could not create payment order.'], 500);
        }
    }

    public function verifyRazorpay(Request $request, Event $event)
    {
        $this->assertOrganizerEvent($event);

        if ($event->hasDirectGpayQrUnlocked()) {
            return redirect()
                ->route('client.events.index')
                ->with('success', 'Direct GPay QR is already unlocked for ' . $event->title . '.');
        }

        $validated = $request->validate([
            'razorpay_order_id' => 'required|string|max:64',
            'razorpay_payment_id' => 'required|string|max:64',
            'razorpay_signature' => 'required|string|max:255',
        ]);

        $razorpay = RazorpayService::make();
        if (!$razorpay) {
            return back()->withErrors(['payment' => 'Razorpay verification is not configured.']);
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
            return back()->withErrors(['payment' => 'Payment verification failed. Contact support with your payment id.']);
        }

        try {
            $fetched = $razorpay->fetchOrder($validated['razorpay_order_id']);
            $expectedPaise = $razorpay->resolveTestAmount((int) round((float) config('services.direct_gpay_unlock.amount', 400) * 100));
            if (isset($fetched['amount']) && (int) $fetched['amount'] !== $expectedPaise) {
                return back()->withErrors(['payment' => 'Amount does not match this unlock price.']);
            }

            $notes = $fetched['notes'] ?? [];
            if (($notes['chandla_type'] ?? '') !== 'direct_gpay_unlock' || (int) ($notes['event_id'] ?? 0) !== (int) $event->id
                || (int) ($notes['user_id'] ?? 0) !== (int) $event->user_id) {
                return back()->withErrors(['payment' => 'This payment does not belong to this event order.']);
            }
        } catch (\Throwable $e) {
            return back()->withErrors(['payment' => 'Could not confirm order details with the gateway.']);
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
            return back()->withErrors(['payment' => 'Could not complete unlock. Try again or contact support.']);
        }

        return redirect()
            ->route('client.events.index')
            ->with('success', 'Direct GPay QR is unlocked for ' . $event->title . '. Use the Direct QR button on that event to share the guest link.');
    }

    /**
     * Manual UPI + admin verification removed — redirect if old clients POST here.
     */
    public function store(Request $request, Event $event)
    {
        $this->assertOrganizerEvent($event);

        return redirect()
            ->route('client.events.direct-gpay-unlock.show', $event)
            ->with('info', 'Please pay with Razorpay on this page. Manual UPI is no longer used for this unlock.');
    }

    private function assertOrganizerEvent(Event $event): void
    {
        if (!in_array((int) $event->user_id, Auth::user()->allowedUserIds(), true)) {
            abort(403);
        }
        if ($event->is_archived) {
            abort(404);
        }
    }
}
