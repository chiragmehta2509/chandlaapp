@extends('layouts.admin')

@section('title', 'Manage Vendors')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Manage Vendors</h1>
    <p class="text-gray-600 mt-1">Review pending submissions, manage active profiles, and check customer leads.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="space-y-8">
    
    {{-- 1. Pending Approvals --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h2 class="font-bold text-gray-800 text-base">Pending Approvals ({{ $pendingVendors->count() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider text-left">
                    <tr>
                        <th class="px-6 py-3">Business Name</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">City</th>
                        <th class="px-6 py-3">Price</th>
                        <th class="px-6 py-3">Contact</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($pendingVendors as $v)
                        <tr>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-900">{{ $v->business_name }}</span>
                                @if($v->description)
                                    <p class="text-xs text-gray-400 mt-1 max-w-sm truncate">{{ $v->description }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $v->category->name }}</td>
                            <td class="px-6 py-4">{{ $v->city }}</td>
                            <td class="px-6 py-4 uppercase font-bold text-xs text-gray-500">{{ $v->price_tier }}</td>
                            <td class="px-6 py-4">
                                <div>Call: <span class="font-medium text-gray-950">{{ $v->phone }}</span></div>
                                @if($v->whatsapp)
                                    <div class="text-xs text-green-600">WA: {{ $v->whatsapp }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('admin.vendors.approve', $v->id) }}">
                                        @csrf
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-1 px-3 rounded text-xs">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.vendors.reject', $v->id) }}">
                                        @csrf
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs" onsubmit="return confirm('Reject this vendor?');">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400 bg-gray-50/50">No pending vendor approvals.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 2. Active Vendors --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="font-bold text-gray-800 text-base">Active Vendors ({{ $activeVendors->count() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider text-left">
                    <tr>
                        <th class="px-6 py-3">Business Name</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">City</th>
                        <th class="px-6 py-3">Price</th>
                        <th class="px-6 py-3">Contact</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($activeVendors as $v)
                        <tr>
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $v->business_name }}</td>
                            <td class="px-6 py-4">{{ $v->category->name }}</td>
                            <td class="px-6 py-4">{{ $v->city }}</td>
                            <td class="px-6 py-4 uppercase font-bold text-xs text-gray-500">{{ $v->price_tier }}</td>
                            <td class="px-6 py-4">{{ $v->phone }}</td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('admin.vendors.reject', $v->id) }}">
                                    @csrf
                                    <button type="submit" class="border border-red-200 hover:bg-red-50 text-red-600 font-bold py-1 px-3 rounded text-xs" onsubmit="return confirm('Deactivate this vendor?');">
                                        Deactivate
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400 bg-gray-50/50">No active vendors found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 3. Leads Log --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="font-bold text-gray-800 text-base">Customer Leads Log</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider text-left">
                    <tr>
                        <th class="px-6 py-3">Vendor</th>
                        <th class="px-6 py-3">Host Contact</th>
                        <th class="px-6 py-3">Event Context</th>
                        <th class="px-6 py-3">Message</th>
                        <th class="px-6 py-3">Created At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($leads as $l)
                        <tr>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-900">{{ $l->vendor->business_name }}</span>
                                <p class="text-xs text-gray-400">{{ $l->vendor->category->name }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900">{{ $l->host_name }}</span>
                                <p class="text-xs text-gray-500">{{ $l->host_phone }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($l->event)
                                    <span class="font-medium text-blue-600">{{ $l->event->title }}</span>
                                @else
                                    <span class="text-xs text-gray-400">None</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs max-w-xs truncate" title="{{ $l->message }}">
                                {{ $l->message ?: 'No message text provided' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">{{ $l->created_at->format('d/m/Y h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400 bg-gray-50/50">No leads recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leads->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $leads->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
