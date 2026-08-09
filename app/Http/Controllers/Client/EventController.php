<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventType;
use App\Models\UPITransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EventController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        $ownerId  = $authUser->dataOwnerId(); // parent's id (shared events)
        $selfId   = $authUser->id;            // family member's own events

        // Family members see parent's events AND their own events
        if ($authUser->isFamilyMember() && $selfId !== $ownerId) {
            $query = Event::where(function ($q) use ($ownerId, $selfId) {
                $q->where('user_id', $ownerId)->orWhere('user_id', $selfId);
            });
        } else {
            $query = Event::where('user_id', $ownerId);
        }

        if ($request->status === 'upcoming') {
            $query->where('event_date', '>=', now()->toDateString())->where('is_archived', false);
        } elseif ($request->status === 'past') {
            $query->where('event_date', '<', now()->toDateString());
        } elseif ($request->status === 'archived') {
            $query->where('is_archived', true);
        }

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $events = $query->with('eventType')->orderBy('event_date', 'desc')->paginate(12);

        return view('client.events.index', compact('events', 'authUser'));
    }

    public function create(Request $request)
    {
        $eventTypes = EventType::active()->ordered()->get();
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        // Free event credits always come from the data owner (parent's account)
        $buyer = User::find($authUser->dataOwnerId());
        $freeEventCredits = $buyer ? $buyer->free_event_credits : 0;
        $autoRedeem = $request->query('redeem') === 'true';
        return view('client.events.create', compact('eventTypes', 'freeEventCredits', 'autoRedeem', 'authUser'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        // Family members own their events themselves; main users use dataOwnerId.
        $eventOwnerId = $authUser->isFamilyMember() ? $authUser->id : $authUser->dataOwnerId();

        // Plan limits and credits always come from the data owner (main account).
        $buyer = $authUser->isFamilyMember()
            ? (User::find($authUser->dataOwnerId()) ?? $authUser)
            : $authUser;

        $maxEventsAllowed = $authUser->isFamilyMember()
            ? 1  // family members always get 1 free event of their own
            : $buyer->maxEventsAllowed();

        // Count only this user's own events for the limit check.
        // Ganpati Special events are free for all users and don't count against the limit.
        $ganpatiTypeId = \App\Models\EventType::where('slug', 'ganpati_special')->value('id');
        $currentEvents = Event::where('user_id', $eventOwnerId)
            ->when($ganpatiTypeId, fn($q) => $q->where('event_type_id', '!=', $ganpatiTypeId))
            ->count();

        $redeemingCoin = $request->has('redeem_coin') && $buyer && $buyer->free_event_credits > 0;

        if ($currentEvents >= $maxEventsAllowed && !$redeemingCoin) {
            return back()->withErrors([
                'title' => 'Event limit reached for your current plan. Please purchase/upgrade to add more events or redeem a free event credit.',
            ])->withInput();
        }

        if ($redeemingCoin) {
            $buyer->decrement('free_event_credits');
        }

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'event_date'    => ['required', 'date', 'after_or_equal:today'],
            'event_time'    => 'nullable|date_format:H:i',
            'venue'         => 'nullable|string',
            'event_type_id' => 'nullable|exists:event_types,id',
        ], [
            'event_date.after_or_equal' => 'Event date must be today or a future date.',
        ]);

        $validated['user_id']         = $eventOwnerId;
        $validated['free_entry_limit'] = 50;
        $event = Event::create($validated);

        return redirect()->route('client.events.show', $event->id)->with('success', 'Event created successfully');
    }

    public function show($id)
    {
        $event = Event::with(['entries', 'invitations', 'collaborators', 'eventType', 'chandlas'])
            ->whereIn('user_id', Auth::user()->allowedUserIds())
            ->findOrFail($id);

        $pendingPlanPayment = UPITransaction::where('event_id', $event->id)
            ->where('user_id', Auth::user()->dataOwnerId())
            ->where('status', 'pending')
            ->where('metadata->type', 'plan')
            ->latest()
            ->first();

        return view('client.events.show', compact('event', 'pendingPlanPayment'));
    }

    public function edit($id)
    {
        $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);
        $eventTypes = EventType::active()->ordered()->get();
        return view('client.events.edit', compact('event', 'eventTypes'));
    }

    public function update(Request $request, $id)
    {
        $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'event_time' => 'nullable|date_format:H:i',
            'venue' => 'nullable|string',
            'event_type_id' => 'nullable|exists:event_types,id',
        ]);

        $event->update($validated);

        return redirect()->route('client.events.show', $event->id)->with('success', 'Event updated successfully');
    }

    public function destroy($id)
    {
        $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);
        $event->delete();

        return redirect()->route('client.events.index')->with('success', 'Event deleted successfully');
    }

    public function updatePlan(Request $request, $id)
    {
        $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);

        $validated = $request->validate([
            'pricing_plan' => 'required|in:free,payg,unlimited',
            'use_credit' => 'nullable|boolean',
        ]);

        $chandlaCount = $event->chandlas()->count();
        $freeLimit = min((int) ($event->free_entry_limit ?? 50), 50);

        if ($validated['pricing_plan'] === 'free' && $chandlaCount > $freeLimit) {
            return back()->withErrors([
                'pricing_plan' => 'Cannot switch to Free after exceeding the free limit.',
            ]);
        }

        $event->pricing_plan = $validated['pricing_plan'];
        if ($validated['pricing_plan'] === 'unlimited') {
            if (!empty($validated['use_credit'])) {
                $user = Auth::user();
                if ($user->free_event_credits <= 0) {
                    return back()->withErrors([
                        'pricing_plan' => 'No free event credits available.',
                    ]);
                }
                $user->free_event_credits = max(0, (int) $user->free_event_credits - 1);
                $user->save();
            }
            $event->unlimited_purchased_at = now();
        } else {
            $event->unlimited_purchased_at = null;
        }

        $event->save();

        return back()->with('success', 'Plan updated successfully.');
    }

    public function saveDirectGpayUpi(Request $request, int $id)
    {
        $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);

        if (!$event->hasDirectGpayQrUnlocked()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Pay ₹' . number_format((float) config('services.direct_gpay_unlock.amount', 400), 0) . ' to unlock Direct GPay QR for this event.',
                ], 403);
            }

            return redirect()
                ->route('client.events.direct-gpay-unlock.show', $event)
                ->withErrors(['unlock' => 'Unlock Direct GPay QR for this event first.']);
        }

        $validated = $request->validate([
            'upi_id' => 'required|string|max:255',
        ]);

        $event->upi_id = trim($validated['upi_id']);
        $event->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'UPI ID saved.',
                'qr_url' => route('client.events.direct-gpay.qr', $event->id),
                'pay_url' => route('public.direct-gpay', $event->id),
            ]);
        }

        return back()->with('success', 'UPI ID saved for direct GPay QR.');
    }

    /**
     * SVG QR (no Imagick required). PNG format would need the imagick PHP extension.
     */
    public function directGpayQrPng(int $id)
    {
        $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($id);

        if (!$event->hasDirectGpayQrUnlocked()) {
            abort(403);
        }

        $payUrl = route('public.direct-gpay', ['event' => $event->id], true);

        $svg = QrCode::size(420)->generate($payUrl);

        return response($svg)
            ->header('Content-Type', 'image/svg+xml; charset=utf-8')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}
