@extends('layouts.admin')

@section('title', 'Add New Subscription Plan')

@section('content')
<div class="p-6 max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.plans.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-gray-800 transition-colors mb-2">
                <i class="fas fa-arrow-left text-[10px]"></i> Back to Plans
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Add New Subscription Plan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Create a new subscription plan/pack. It will immediately appear on the site pricing page and client portal.</p>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.plans.store') }}">
            @csrf

            <div class="space-y-6">

                {{-- Name & Slug --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Plan Display Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. VIP Host Pack" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Plan Slug (Unique Key) *</label>
                        <input type="text" name="slug" value="{{ old('slug') }}" placeholder="e.g. vip_host" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Price, Level & Badge --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Price (₹ INR) *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 font-bold text-sm">₹</span>
                            <input type="number" step="1" min="0" name="amount_inr" value="{{ old('amount_inr', 0) }}" required
                                class="w-full rounded-xl border border-gray-300 pl-8 pr-4 py-2.5 text-sm font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Min Plan Level *</label>
                        <input type="number" min="0" max="10" name="min_level" value="{{ old('min_level', 1) }}" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Badge Tag</label>
                        <input type="text" name="badge" value="{{ old('badge', 'New Plan') }}"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="e.g. Best Value, VIP Choice">
                    </div>
                </div>

                {{-- Checkbox --}}
                <div>
                    <label class="inline-flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_popular" value="1" {{ old('is_popular') ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-800">Highlight as "Most Popular" Card</span>
                    </label>
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Description / Tagline</label>
                    <textarea name="description" rows="2" placeholder="Short summary of what this plan unlocks..."
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
                </div>

                {{-- Bullet Point Features --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                        Plan Features (One feature per line)
                    </label>
                    <p class="text-xs text-gray-500 mb-2">These bullet points appear on the website pricing cards and checkout pages.</p>
                    <textarea name="features" rows="5" placeholder="Up to 5 Events&#10;Unlimited Entries&#10;Priority 24/7 Support"
                        class="w-full rounded-xl border border-gray-300 p-4 text-sm font-mono leading-relaxed focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('features') }}</textarea>
                </div>

                {{-- Payment URLs --}}
                <div class="border-t border-gray-100 pt-6">
                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wide mb-3">Custom Razorpay Payment URLs (Optional)</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Live Payment URL</label>
                            <input type="url" name="live_payment_url" value="{{ old('live_payment_url') }}"
                                class="w-full rounded-xl border border-gray-300 px-4 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="https://rzp.io/rzp/...">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Test Payment URL</label>
                            <input type="url" name="test_payment_url" value="{{ old('test_payment_url') }}"
                                class="w-full rounded-xl border border-gray-300 px-4 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="https://rzp.io/rzp/...">
                        </div>
                    </div>
                </div>

                {{-- Submit Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.plans.index') }}"
                        class="px-5 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-sm transition-colors">
                        <i class="fas fa-plus mr-1.5"></i> Create Plan
                    </button>
                </div>

            </div>
        </form>
    </div>

</div>
@endsection
