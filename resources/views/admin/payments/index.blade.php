@extends('layouts.admin')

@section('title', 'Payments Management')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-800 font-display">Payments Management</h1>
        <p class="text-gray-600 mt-1">Manage all payments and subscription checkouts</p>
    </div>
    <div>
        <a href="{{ route('admin.payments.export', ['tab' => $tab, 'status' => request('status'), 'search' => request('search')]) }}" 
           class="inline-flex items-center justify-center rounded-xl bg-amber-500 hover:bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors">
            <i class="fas fa-file-export mr-2 text-xs"></i> Export CSV
        </a>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Revenue</p>
        <p class="text-3xl font-black text-slate-800 mt-2 font-display">₹{{ number_format($stats['total_revenue'], 0) }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Razorpay Success</p>
        <p class="text-3xl font-black text-green-600 mt-2 font-display">{{ $stats['success_razorpay'] }} <span class="text-xs text-slate-400 font-normal">/ {{ $stats['total_razorpay'] }}</span></p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Manual UPI Pending</p>
        <p class="text-3xl font-black text-amber-600 mt-2 font-display">{{ $stats['pending_upi'] }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Manual UPI</p>
        <p class="text-3xl font-black text-slate-800 mt-2 font-display">{{ $stats['total_upi'] }} <span class="text-xs text-slate-400 font-normal">/ completed</span></p>
    </div>
</div>

<!-- Tabs -->
<div class="border-b border-gray-200 mb-6">
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <a href="{{ route('admin.payments.index', ['tab' => 'razorpay']) }}" 
           class="border-b-2 py-4 px-1 text-sm font-semibold transition-colors {{ $tab === 'razorpay' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            <i class="fas fa-credit-card mr-2 text-xs"></i> Razorpay Transactions
        </a>
        <a href="{{ route('admin.payments.index', ['tab' => 'manual']) }}" 
           class="border-b-2 py-4 px-1 text-sm font-semibold transition-colors {{ $tab === 'manual' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            <i class="fas fa-qrcode mr-2 text-xs"></i> Manual UPI Submissions
        </a>
    </nav>
</div>



<!-- Payments Table -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="data-table min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50/75">
                @if($tab === 'razorpay')
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Transaction Number</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Package</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                @else
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Reference ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                @endif
            </thead>
            <tbody class="bg-white divide-y divide-gray-150">
                @forelse($payments as $payment)
                    @if($tab === 'razorpay')
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-slate-800 font-mono">{{ $payment->transaction_number }}</div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $payment->razorpay_order_id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-slate-800">{{ $payment->user->name ?? 'Deleted User' }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $payment->user->phone ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-700 font-medium">{{ $payment->package_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-extrabold text-slate-900">₹{{ number_format($payment->amount_inr, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $payment->statusBadgeClass() }}">
                                    {{ $payment->statusLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-medium text-slate-500">
                                {{ $payment->created_at->format('M d, Y · h:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.payments.show', $payment->transaction_number) }}?type=razorpay" class="text-amber-500 hover:text-amber-700 font-bold transition-colors">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                            </td>
                        </tr>
                    @else
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-slate-800 font-mono">{{ $payment->transaction_id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-slate-800">{{ $payment->user->name ?? 'Deleted User' }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $payment->user->phone ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-extrabold text-slate-900">₹{{ number_format($payment->amount, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider 
                                    {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($payment->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-medium text-slate-500">
                                {{ $payment->created_at->format('M d, Y · h:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.payments.show', $payment->id) }}" class="text-amber-500 hover:text-amber-700 font-bold transition-colors">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                            <i class="fas fa-receipt text-3xl mb-2.5 block text-slate-300"></i>
                            No payments found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
