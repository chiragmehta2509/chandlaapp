<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Chandla;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function showPaymentForm($eventId, $token = null)
    {
        $event = Event::with('user')->findOrFail($eventId);

        // Optional: Add token validation for security
        // if ($event->payment_token && $event->payment_token !== $token) {
        //     abort(404);
        // }

        return view('public.payment', compact('event'));
    }

    public function submitPayment(Request $request, $eventId, $token = null)
    {
        $event = Event::with('user')->findOrFail($eventId);

        // Optional: Add token validation for security
        // if ($event->payment_token && $event->payment_token !== $token) {
        //     abort(404);
        // }

        $validated = $request->validate([
            'giver_name' => 'required|string|max:255',
            'giver_phone' => 'nullable|string|max:20',
            'giver_email' => 'nullable|email|max:255',
            'amount' => 'required|numeric|min:0',
            'gpay_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'gpay_transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $hasPaidPlan = Event::where('user_id', $event->user_id)
            ->whereIn('pricing_plan', ['payg', 'unlimited'])
            ->exists();
        $globalFreeUsedEntries = Chandla::where('user_id', $event->user_id)->count();
        if (!$hasPaidPlan && $globalFreeUsedEntries >= 50) {
            return back()->withErrors([
                'amount' => 'Free plan limit reached for this account. New entries are disabled until plan upgrade.',
            ])->withInput();
        }

        // Store the GPay image
        $imagePath = $request->file('gpay_image')->store('gpay_images', 'public');
        
        // Optimize image if Intervention Image is available
        if (class_exists('Intervention\Image\Facades\Image')) {
            try {
                $image = \Intervention\Image\Facades\Image::make(storage_path('app/public/' . $imagePath));
                if ($image->width() > 1920) {
                    $image->resize(1920, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }
                $image->save(storage_path('app/public/' . $imagePath), 85);
            } catch (\Exception $e) {
                // Continue if image processing fails
            }
        }

        // Create chandla record
        $chandla = Chandla::create([
            'event_id' => $event->id,
            'user_id' => $event->user_id,
            'giver_name' => $validated['giver_name'],
            'giver_phone' => $validated['giver_phone'] ?? null,
            'giver_email' => $validated['giver_email'] ?? null,
            'category' => 'chandla',
            'payment_method' => 'gpay',
            'gpay_image' => $imagePath,
            'gpay_transaction_id' => $validated['gpay_transaction_id'] ?? null,
            'amount' => $validated['amount'],
            'received_date' => now()->toDateString(),
            'notes' => $validated['notes'] ?? 'Payment submitted via QR code scan',
        ]);

        $emailStatus = $this->sendPaymentEmails($event, $chandla);

        return view('public.payment-success', compact('event', 'chandla', 'emailStatus'));
    }

    private function sendPaymentEmails(Event $event, Chandla $chandla): array
    {
        $status = [
            'giver_sent' => false,
            'organizer_sent' => false,
        ];

        $emailData = [
            'event' => $event,
            'chandla' => $chandla,
        ];

        if (!empty($chandla->giver_email)) {
            try {
                Mail::send('emails.payment-submitted', $emailData, function ($message) use ($chandla, $event) {
                    $message->to($chandla->giver_email, $chandla->giver_name)
                        ->subject('Payment submitted for ' . $event->title);
                });
                $status['giver_sent'] = true;
            } catch (\Throwable $e) {
                Log::error('Failed to send giver payment email', [
                    'event_id' => $event->id,
                    'chandla_id' => $chandla->id,
                    'email' => $chandla->giver_email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (!empty($event->user?->email)) {
            try {
                Mail::send('emails.payment-submitted', $emailData, function ($message) use ($event, $chandla) {
                    $message->to($event->user->email, $event->user->name)
                        ->subject('New payment submitted by ' . $chandla->giver_name);
                });
                $status['organizer_sent'] = true;
            } catch (\Throwable $e) {
                Log::error('Failed to send organizer payment email', [
                    'event_id' => $event->id,
                    'chandla_id' => $chandla->id,
                    'email' => $event->user?->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $status;
    }
}
