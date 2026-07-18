<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Chandla;
use App\Models\Event;
use App\Models\EventCashInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashInventoryController extends Controller
{
    public function show($eventId)
    {
        $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($eventId);

        $inventory = EventCashInventory::firstOrCreate(
            ['event_id' => $event->id],
            [
                'note_1' => 0,
                'note_2' => 0,
                'note_5' => 0,
                'note_10' => 0,
                'note_20' => 0,
                'note_50' => 0,
                'note_100' => 0,
                'note_200' => 0,
                'note_500' => 0,
            ]
        );

        $pendingChanges = Chandla::where('event_id', $event->id)
            ->where('category', 'chandla')
            ->where('payment_method', 'cash')
            ->where('change_status', 'pending')
            ->orderBy('received_date', 'desc')
            ->get();

        return view('client.cash-inventory.show', compact('event', 'inventory', 'pendingChanges'));
    }

    public function update(Request $request, $eventId)
    {
        $event = Event::whereIn('user_id', Auth::user()->allowedUserIds())->findOrFail($eventId);

        $validated = $request->validate([
            'note_1' => 'required|integer|min:0',
            'note_2' => 'required|integer|min:0',
            'note_5' => 'required|integer|min:0',
            'note_10' => 'required|integer|min:0',
            'note_20' => 'required|integer|min:0',
            'note_50' => 'required|integer|min:0',
            'note_100' => 'required|integer|min:0',
            'note_200' => 'required|integer|min:0',
            'note_500' => 'required|integer|min:0',
        ]);

        $inventory = EventCashInventory::firstOrCreate(['event_id' => $event->id]);
        $inventory->update($validated);

        return redirect()->route('client.cash-inventory.show', $event->id)
            ->with('success', 'Cash inventory updated successfully.');
    }
}
