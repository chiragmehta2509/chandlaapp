<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\UPITransaction;
use App\Services\EventUnlimitedRazorpayCompletion;
use App\Support\RazorpayTestAmount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Razorpay\Api\Api;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PlanPaymentController extends Controller
{
    public function show(Request $request, $eventId)
    {
        $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($eventId);

        $plan = $request->query('plan');
        if ($plan !== 'unlimited') {
            return redirect()->route('client.events.show', $event->id);
        }

        if ($event->pricing_plan === 'unlimited' || $event->unlimited_purchased_at) {
            return redirect()->route('client.events.show', $event->id)->with('error', 'This event is already on the Unlimited plan.');
        }

        // Test mode: redirect to hosted Razorpay Payment Page (reliable for UPI QR scans).
        if (RazorpayTestAmount::hasTestPaymentLink()) {
            $this->stashTestPaymentIntent($event);
            return $this->redirectToTestPaymentLink();
        }

        $amount = RazorpayTestAmount::resolveRupees((float) ($event->unlimited_price ?? 500));
        $upiId = config('services.upi.id');
        $upiName = config('services.upi.name', 'Chandla Book');
        $keyId = config('services.razorpay.key_id');

        $upiUri = null;
        $qrSvg = null;
        if (! empty($upiId)) {
            $upiUri = 'upi://pay?pa=' . urlencode($upiId)
                . '&pn=' . urlencode($upiName)
                . '&am=' . urlencode(number_format($amount, 2, '.', ''))
                . '&cu=INR'
                . '&tn=' . urlencode('Event Plan Upgrade #' . $event->id);
            $qrSvg = QrCode::size(260)->generate($upiUri);
        }

        return view('client.events.plan-payment', compact('event', 'amount', 'upiId', 'upiName', 'upiUri', 'qrSvg', 'keyId'));
    }

    public function createRazorpayOrder(Request $request, $eventId)
    {
        $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($eventId);

        if ($event->pricing_plan === 'unlimited' || $event->unlimited_purchased_at) {
            return response()->json(['message' => 'This event is already on the Unlimited plan.'], 400);
        }

        $key = config('services.razorpay.key_id');
        $secret = config('services.razorpay.key_secret');
        if (empty($key) || empty($secret)) {
            return response()->json(['message' => 'Razorpay is not configured. Set RAZORPAY_KEY_ID and RAZORPAY_KEY_SECRET.'], 503);
        }

        $amount = (float) ($event->unlimited_price ?? 500);
        $amountPaise = RazorpayTestAmount::resolve((int) round($amount * 100));

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
            'order_id' => $order['id'],
            'amount' => $amountPaise,
            'key_id' => $key,
        ]);
    }

    public function verifyRazorpay(Request $request, $eventId)
    {
        $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($eventId);

        $validated = $request->validate([
            'razorpay_order_id' => 'required|string|max:64',
            'razorpay_payment_id' => 'required|string|max:64',
            'razorpay_signature' => 'required|string|max:255',
        ]);

        $key = config('services.razorpay.key_id');
        $secret = config('services.razorpay.key_secret');
        if (empty($secret)) {
            return back()->withErrors(['payment' => 'Razorpay verification is not configured.']);
        }

        $api = new Api($key, $secret);
        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature' => $validated['razorpay_signature'],
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['payment' => 'Payment verification failed. Contact support with your payment id.']);
        }

        $fetched = $api->order->fetch($validated['razorpay_order_id']);
        $expectedPaise = RazorpayTestAmount::resolve((int) round((float) ($event->unlimited_price ?? 500) * 100));
        if (isset($fetched['amount']) && (int) $fetched['amount'] !== $expectedPaise) {
            return back()->withErrors(['payment' => 'Amount does not match this event.']);
        }

        $notes = $fetched['notes'] ?? [];
        if (($notes['chandla_type'] ?? '') !== 'event_unlimited' || (int) ($notes['event_id'] ?? 0) !== (int) $event->id
            || (int) ($notes['user_id'] ?? 0) !== (int) $event->user_id) {
            return back()->withErrors(['payment' => 'This payment does not belong to this event order.']);
        }

        $ok = EventUnlimitedRazorpayCompletion::applyIfNeeded(
            $event,
            $event->user_id,
            $validated['razorpay_payment_id'],
            $validated['razorpay_order_id']
        );
        if (! $ok) {
            return back()->withErrors(['payment' => 'Could not complete upgrade. Try again or contact support.']);
        }

        return redirect()
            ->route('client.events.show', $event->id)
            ->with('success', 'Payment successful. Your event is now on the Unlimited plan.');
    }

    public function store(Request $request, $eventId)
    {
        $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($eventId);

        $validated = $request->validate([
            'transaction_id' => 'required|string|max:255|unique:upi_transactions,transaction_id',
        ]);

        $amount = (float) ($event->unlimited_price ?? 500);

        UPITransaction::create([
            'user_id' => $event->user_id,
            'event_id' => $event->id,
            'transaction_id' => $validated['transaction_id'],
            'amount' => $amount,
            'status' => 'pending',
            'payment_method' => 'upi',
            'description' => 'Event plan upgrade - Unlimited',
            'metadata' => [
                'type' => 'plan',
                'plan' => 'unlimited',
                'event_id' => $event->id,
                'expected_amount' => $amount,
            ],
        ]);

        return redirect()
            ->route('client.events.show', $event->id)
            ->with('success', 'Payment submitted. Admin verification is pending.');
    }

    /**
     * Test-mode helper: redirect to the configured Razorpay Payment Page, appending
     * the user's email so Razorpay pre-fills it. The hosted page handles UPI QR + cards.
     * Stores the payment intent in session so the thanks-page callback can auto-unlock.
     */
    private function redirectToTestPaymentLink()
    {
        $url = trim(RazorpayTestAmount::testPaymentLink());
        $email = Auth::user()?->email;
        if (is_string($email) && $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $url .= (str_contains($url, '?') ? '&' : '?') . 'email=' . rawurlencode($email);
        }
        return redirect()->away($url);
    }

    /**
     * Stash the plan-unlock intent before redirecting to the test Payment Page,
     * so the thanks callback can auto-unlock without a webhook.
     */
    private function stashTestPaymentIntent($event): void
    {
        session([
            'rzp_test_intent' => [
                'kind' => 'event_unlimited',
                'event_id' => $event->id,
                'user_id' => $event->user_id,
                'created_at' => now()->timestamp,
            ],
        ]);
    }
}
