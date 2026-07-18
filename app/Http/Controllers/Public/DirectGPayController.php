<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Chandla;
use App\Models\Event;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DirectGPayController extends Controller
{
    private const SESSION_KEY = 'direct_gpay_contribution';

    public function show(int $eventId)
    {
        $event = $this->resolvePayableEvent($eventId);

        return view('public.direct-gpay-form', compact('event'));
    }

    public function prepare(Request $request, int $eventId)
    {
        $event = $this->resolvePayableEvent($eventId);

        $limitMessage = $this->organizerEntryLimitMessage($event);
        if ($limitMessage) {
            return back()->withErrors(['amount' => $limitMessage])->withInput();
        }

        $validated = $request->validate([
            'giver_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1|max:9999999',
            'village' => 'required|string|max:255',
            'giver_phone' => 'required|string|max:20',
        ]);

        if (empty($event->upi_id)) {
            return back()->withErrors([
                'amount' => 'The organizer has not set a UPI ID for this event yet. Please contact them.',
            ])->withInput();
        }

        session([self::SESSION_KEY => [
            'event_id' => $event->id,
            'giver_name' => $validated['giver_name'],
            'amount' => round((float) $validated['amount'], 2),
            'village' => $validated['village'],
            'giver_phone' => $validated['giver_phone'],
            'started_at' => now()->timestamp,
        ]]);

        return redirect()->route('public.direct-gpay.pay', ['event' => $event->id]);
    }

    public function pay(int $eventId)
    {
        $event = $this->resolvePayableEvent($eventId);
        $data = session(self::SESSION_KEY);

        if (!$data || (int) ($data['event_id'] ?? 0) !== $event->id) {
            return redirect()
                ->route('public.direct-gpay', ['event' => $event->id])
                ->withErrors(['amount' => 'Please fill in your details again to continue.']);
        }

        if (empty($event->upi_id)) {
            session()->forget(self::SESSION_KEY);

            return redirect()
                ->route('public.direct-gpay', ['event' => $event->id])
                ->withErrors(['amount' => 'UPI is not configured for this event.']);
        }

        if ($this->sessionExpired($data)) {
            session()->forget(self::SESSION_KEY);

            return redirect()
                ->route('public.direct-gpay', ['event' => $event->id])
                ->withErrors(['amount' => 'This session expired. Please start again.']);
        }

        $amount = number_format((float) $data['amount'], 2, '.', '');
        $txnNote = 'CB-' . $event->id . '-' . substr(sha1(json_encode($data) . config('app.key')), 0, 10);

        $upiUri = 'upi://pay?pa=' . rawurlencode($event->upi_id)
            . '&pn=' . rawurlencode($event->title ?? 'Event')
            . '&am=' . rawurlencode($amount)
            . '&cu=INR'
            . '&tn=' . rawurlencode($txnNote);

        $qrSvg = QrCode::size(260)->generate($upiUri);

        return view('public.direct-gpay-pay', [
            'event' => $event,
            'data' => $data,
            'upiUri' => $upiUri,
            'qrSvg' => $qrSvg,
            'amount' => $amount,
        ]);
    }

    public function complete(int $eventId)
    {
        $event = $this->resolvePayableEvent($eventId);
        $data = session()->pull(self::SESSION_KEY);

        if (!$data || (int) ($data['event_id'] ?? 0) !== $event->id) {
            return redirect()
                ->route('public.direct-gpay', ['event' => $event->id])
                ->withErrors(['amount' => 'Nothing to complete. Please start from the form.']);
        }

        if ($this->sessionExpired($data)) {
            return redirect()
                ->route('public.direct-gpay', ['event' => $event->id])
                ->withErrors(['amount' => 'This session expired. Please start again.']);
        }

        $limitMessage = $this->organizerEntryLimitMessage($event);
        if ($limitMessage) {
            return redirect()
                ->route('public.direct-gpay', ['event' => $event->id])
                ->withErrors(['amount' => $limitMessage]);
        }

        $event->loadMissing('user');

        $chandla = Chandla::create([
            'event_id' => $event->id,
            'user_id' => $event->user_id,
            'giver_name' => $data['giver_name'],
            'giver_phone' => $data['giver_phone'],
            'giver_address' => $data['village'],
            'category' => 'GPAY GPAY',
            'payment_method' => 'gpay',
            'gpay_image' => null,
            'gpay_transaction_id' => null,
            'amount' => $data['amount'],
            'received_date' => now()->toDateString(),
            'notes' => 'Direct GPay QR — paid via UPI to organizer',
        ]);

        $emailStatus = $this->notifyOrganizer($event, $chandla);

        return view('public.direct-gpay-success', compact('event', 'chandla', 'emailStatus'));
    }

    private function resolvePayableEvent(int $eventId): Event
    {
        $event = Event::query()
            ->whereKey($eventId)
            ->where('is_archived', false)
            ->with('user')
            ->firstOrFail();

        if (!$event->user || !$event->hasDirectGpayQrUnlocked()) {
            throw new HttpResponseException(
                response()->view('public.direct-gpay-unavailable', [
                    'eventTitle' => $event->title,
                ], 403)
            );
        }

        return $event;
    }

    private function sessionExpired(array $data): bool
    {
        $started = (int) ($data['started_at'] ?? 0);

        return $started === 0 || now()->timestamp - $started > 3600;
    }

    private function organizerEntryLimitMessage(Event $event): ?string
    {
        $hasPaidPlan = Event::where('user_id', $event->user_id)
            ->whereIn('pricing_plan', ['payg', 'unlimited'])
            ->exists();
        $globalFreeUsedEntries = Chandla::where('user_id', $event->user_id)->count();
        if (!$hasPaidPlan && $globalFreeUsedEntries >= 50) {
            return 'This organizer has reached the free plan entry limit. New payments cannot be recorded until they upgrade.';
        }

        return null;
    }

    private function notifyOrganizer(Event $event, Chandla $chandla): array
    {
        $status = ['organizer_sent' => false];

        if (empty($event->user?->email)) {
            return $status;
        }

        $emailData = ['event' => $event, 'chandla' => $chandla];

        try {
            Mail::send('emails.payment-submitted', $emailData, function ($message) use ($event, $chandla) {
                $message->to($event->user->email, $event->user->name)
                    ->subject('Direct GPay recorded: ' . $chandla->giver_name . ' — ' . $event->title);
            });
            $status['organizer_sent'] = true;
        } catch (\Throwable $e) {
            Log::error('Failed to send direct GPay organizer email', [
                'event_id' => $event->id,
                'chandla_id' => $chandla->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $status;
    }
}
