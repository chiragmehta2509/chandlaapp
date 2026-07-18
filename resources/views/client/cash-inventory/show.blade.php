@extends('layouts.client')

@section('title', 'Cash Inventory')

@section('content')
<div class="mb-6">
    <a href="{{ route('client.events.show', $event->id) }}" class="cb-link mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Back to Event
    </a>
    <h1 class="text-3xl font-bold text-gray-800">Cash Inventory - {{ $event->title }}</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Current Notes & Coins</h2>
            <form method="POST" action="{{ route('client.cash-inventory.update', $event->id) }}">
                @csrf
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach([1,2,5,10,20,50,100,200,500] as $denomination)
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">₹{{ $denomination }}</label>
                            <input type="number" name="note_{{ $denomination }}" min="0"
                                   value="{{ old('note_' . $denomination, $inventory->{'note_' . $denomination}) }}"
                                   class="cb-field">
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="cb-btn cb-btn-gold">
                        Update Inventory
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Pending Change</h2>
            @if($pendingChanges->count() > 0)
                <div class="space-y-4">
                    @foreach($pendingChanges as $chandla)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="text-sm text-gray-500">{{ $chandla->received_date->format('d/m/Y') }}</div>
                            <div class="text-gray-900 font-medium">{{ $chandla->giver_name }}</div>
                            <div class="text-sm text-gray-700">
                                Change Due: ₹{{ number_format($chandla->change_amount, 2) }}
                            </div>
                            <a href="{{ route('client.chandlas.show', $chandla->id) }}" class="cb-link text-sm">
                                View Chandla
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No pending change for this event.</p>
            @endif
        </div>
    </div>
</div>
@endsection
