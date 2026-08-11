@extends('layouts.admin')

@section('title', 'Chandlas Management')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Chandlas Management</h1>
    <p class="text-gray-600 mt-1">Manage all chandlas in the system</p>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-medium">Total Records</p>
        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-medium">Total Amount</p>
        <p class="text-3xl font-bold text-indigo-600 mt-2">₹{{ number_format($stats['total_amount'], 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-medium">Cash Count</p>
        <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['chandla_count'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-medium">Cover + Gift</p>
        <p class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['cover_count'] + $stats['gift_count'] }}</p>
    </div>
</div>



<!-- Chandlas Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="data-table min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giver</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($chandlas as $chandla)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $chandla->received_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $chandla->event->title }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $chandla->giver_name }}</div>
                            <div class="text-sm text-gray-500">{{ $chandla->giver_phone }}</div>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $chandla->user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.chandlas.show', $chandla->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(!$chandla->is_verified)
                                <form action="{{ route('admin.chandlas.verify', $chandla->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900 mr-3" title="Verify">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('admin.chandlas.destroy', $chandla->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No chandlas found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
