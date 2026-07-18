@extends('layouts.admin')

@section('title', 'Chandla Details')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.chandlas.index') }}" class="text-indigo-600 hover:text-indigo-800 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Back to Chandlas
    </a>
    <h1 class="text-3xl font-bold text-gray-800">Chandla Details</h1>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <div class="grid grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Event</label>
            <p class="text-gray-900">{{ $chandla->event->title }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
            <p class="text-gray-900">{{ $chandla->user->name }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Giver Name</label>
            <p class="text-gray-900">{{ $chandla->giver_name }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <p class="text-gray-900">{{ $chandla->giver_phone ?? 'N/A' }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <p class="text-gray-900">{{ $chandla->giver_email ?? 'N/A' }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Received Date</label>
            <p class="text-gray-900">{{ $chandla->received_date->format('M d, Y') }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ $chandla->category_label }}
            </span>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                {{ $chandla->payment_method_label }}
            </span>
        </div>
        @if($chandla->category === 'chandla')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
            <p class="text-gray-900 text-2xl font-bold">₹{{ number_format($chandla->amount, 2) }}</p>
        </div>
        @endif
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Receipt Number</label>
            <p class="text-gray-900">{{ $chandla->receipt_number ?? 'N/A' }}</p>
        </div>
        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <p class="text-gray-900">{{ $chandla->giver_address ?? 'N/A' }}</p>
        </div>
        @if($chandla->description)
        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <p class="text-gray-900">{{ $chandla->description }}</p>
        </div>
        @endif
        @if($chandla->category === 'gift' && $chandla->gift_item_name)
        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Gift Item</label>
            <p class="text-gray-900">{{ $chandla->gift_item_name }}</p>
        </div>
        @endif
        @if($chandla->category === 'gift')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gift Given</label>
            <p class="text-gray-900">
                {{ $chandla->gift_received === null ? 'N/A' : ($chandla->gift_received ? 'Yes' : 'No') }}
            </p>
        </div>
        @endif
        @if($chandla->notes)
        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <p class="text-gray-900">{{ $chandla->notes }}</p>
        </div>
        @endif
        @if($chandla->category === 'chandla' && $chandla->payment_method === 'cash')
        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Cash Notes</label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm text-gray-900">
                <div>₹1: {{ $chandla->cash_note_1 }}</div>
                <div>₹2: {{ $chandla->cash_note_2 }}</div>
                <div>₹5: {{ $chandla->cash_note_5 }}</div>
                <div>₹10: {{ $chandla->cash_note_10 }}</div>
                <div>₹20: {{ $chandla->cash_note_20 }}</div>
                <div>₹50: {{ $chandla->cash_note_50 }}</div>
                <div>₹100: {{ $chandla->cash_note_100 }}</div>
                <div>₹200: {{ $chandla->cash_note_200 }}</div>
                <div>₹500: {{ $chandla->cash_note_500 }}</div>
            </div>
            @php
                $receivedTotal = ($chandla->cash_note_1 * 1)
                    + ($chandla->cash_note_2 * 2)
                    + ($chandla->cash_note_5 * 5)
                    + ($chandla->cash_note_10 * 10)
                    + ($chandla->cash_note_20 * 20)
                    + ($chandla->cash_note_50 * 50)
                    + ($chandla->cash_note_100 * 100)
                    + ($chandla->cash_note_200 * 200)
                    + ($chandla->cash_note_500 * 500);
            @endphp
            <div class="mt-4 space-y-1 text-sm text-gray-900">
                <div>Received Total: ₹{{ number_format($receivedTotal, 2) }}</div>
                <div>Change Due: ₹{{ number_format($chandla->change_amount, 2) }}</div>
                <div>Change Status: {{ $chandla->change_status ? ucfirst($chandla->change_status) : 'N/A' }}</div>
            </div>
            @if($chandla->change_status === 'returned' && $chandla->change_amount > 0)
            <div class="mt-3 grid grid-cols-2 md:grid-cols-3 gap-2 text-sm text-gray-900">
                <div>₹1: {{ $chandla->change_note_1 }}</div>
                <div>₹2: {{ $chandla->change_note_2 }}</div>
                <div>₹5: {{ $chandla->change_note_5 }}</div>
                <div>₹10: {{ $chandla->change_note_10 }}</div>
                <div>₹20: {{ $chandla->change_note_20 }}</div>
                <div>₹50: {{ $chandla->change_note_50 }}</div>
                <div>₹100: {{ $chandla->change_note_100 }}</div>
                <div>₹200: {{ $chandla->change_note_200 }}</div>
                <div>₹500: {{ $chandla->change_note_500 }}</div>
            </div>
            @endif
        </div>
        @endif
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $chandla->is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{ $chandla->is_verified ? 'Verified' : 'Pending Verification' }}
            </span>
        </div>
    </div>

    @if(!$chandla->is_verified)
    <div class="mt-6">
        <form action="{{ route('admin.chandlas.verify', $chandla->id) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                <i class="fas fa-check mr-2"></i>Verify Chandla
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
