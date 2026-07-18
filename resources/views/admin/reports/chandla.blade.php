@extends('layouts.admin')

@section('title', 'Chandla Report')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Chandla Report</h1>
    <p class="text-gray-600 mt-1">Generate detailed reports for chandlas</p>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form method="GET" action="{{ route('admin.reports.chandla') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <select name="event_id" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Events</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                        {{ $event->title }}
                    </option>
                @endforeach
            </select>
            <input type="date" name="date" value="{{ request('date') }}" placeholder="Specific Date"
                   class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <div class="grid grid-cols-2 gap-2">
                <input type="date" name="start_date" value="{{ request('start_date') }}" placeholder="Start Date"
                       class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <input type="date" name="end_date" value="{{ request('end_date') }}" placeholder="End Date"
                       class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-search mr-2"></i>Generate
                </button>
                <a href="{{ route('admin.reports.chandla.export', request()->all()) }}" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                    <i class="fas fa-download mr-2"></i>Export
                </a>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <select name="category" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Categories</option>
                <option value="chandla" {{ request('category') === 'chandla' ? 'selected' : '' }}>Cash</option>
                <option value="cover" {{ request('category') === 'cover' ? 'selected' : '' }}>Cover</option>
                <option value="gift" {{ request('category') === 'gift' ? 'selected' : '' }}>Gift</option>
            </select>
            <select name="payment_method" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Payment Methods</option>
                <option value="gpay" {{ request('payment_method') === 'gpay' ? 'selected' : '' }}>GPay</option>
                <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="other" {{ request('payment_method') === 'other' ? 'selected' : '' }}>N/A</option>
            </select>
        </div>
    </form>
</div>

<!-- Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <p class="text-gray-600 text-sm font-medium">Total Records</p>
        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_count'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <p class="text-gray-600 text-sm font-medium">Total Amount</p>
        <p class="text-3xl font-bold text-gray-800 mt-2">₹{{ number_format($stats['total_amount'], 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
        <p class="text-gray-600 text-sm font-medium">By Category</p>
        <div class="mt-2 space-y-1">
            @foreach($stats['by_category'] as $category => $data)
                <p class="text-sm text-gray-700">{{ $category === 'chandla' ? 'Cash' : ucfirst($category) }}: ₹{{ number_format($data['amount'], 2) }} ({{ $data['count'] }})</p>
            @endforeach
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <p class="text-gray-600 text-sm font-medium">By Payment Method</p>
        <div class="mt-2 space-y-1">
            @foreach($stats['by_payment_method'] as $method => $data)
                <p class="text-sm text-gray-700">{{ $method === 'other' ? 'N/A' : ucfirst(str_replace('_', ' ', $method)) }}: ₹{{ number_format($data['amount'], 2) }}</p>
            @endforeach
        </div>
    </div>
</div>

<!-- Report Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Report Details</h2>
        <a href="{{ route('admin.reports.chandla.export', request()->all()) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            <i class="fas fa-download mr-2"></i>Export CSV
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giver Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($chandlas as $chandla)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $chandla->received_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $chandla->event->title }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $chandla->giver_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $chandla->giver_phone ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $chandla->category_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $chandla->payment_method_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                            ₹{{ number_format($chandla->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $chandla->receipt_number ?? 'N/A' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No records found for the selected criteria</td>
                    </tr>
                @endforelse
            </tbody>
            @if($chandlas->count() > 0)
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="6" class="px-6 py-4 text-right text-sm font-bold text-gray-900">Total:</td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-900">₹{{ number_format($stats['total_amount'], 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
