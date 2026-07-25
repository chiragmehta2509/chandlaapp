@extends('layouts.client')

@section('title', 'Register Your Business')

@section('content')
<div class="w-full min-w-0 max-w-2xl mx-auto">
    <div class="mb-5 sm:mb-6">
        <a href="{{ route('client.vendors.index') }}" class="cb-link text-sm inline-flex items-center gap-2 mb-3">
            <i class="fas fa-arrow-left"></i> Back to Directory
        </a>
        <h1 class="cb-page-title">Register Your Business</h1>
        <p class="cb-subtitle mt-1.5 sm:mt-1 max-w-3xl leading-relaxed text-slate-600">Grow your business by receiving direct leads from Chandla Book hosts.</p>
    </div>

    @if(session('success'))
        <div class="cb-card p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl mb-5 text-sm leading-relaxed flex gap-2">
            <i class="fa-solid fa-circle-check mt-0.5 text-base text-emerald-600"></i>
            <div>
                <p class="font-bold">Success!</p>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <form method="post" action="{{ route('client.vendors.register.submit') }}" enctype="multipart/form-data" class="cb-card p-5 sm:p-6 space-y-4">
        @csrf

        {{-- Business Name --}}
        <label class="block min-w-0">
            <span class="text-xs font-semibold text-slate-600">Business / Brand Name *</span>
            <input type="text" name="business_name" required value="{{ old('business_name') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base sm:text-sm" placeholder="e.g. Royal Caterers">
            @error('business_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </label>

        {{-- Category & Price Tier Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <label class="block min-w-0">
                <span class="text-xs font-semibold text-slate-600">Service Category *</span>
                <select name="category_id" required class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base sm:text-sm bg-white">
                    <option value="">Select a category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </label>

            <label class="block min-w-0">
                <span class="text-xs font-semibold text-slate-600">Price Tier *</span>
                <select name="price_tier" required class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base sm:text-sm bg-white">
                    <option value="budget" {{ old('price_tier') == 'budget' ? 'selected' : '' }}>Budget (₹)</option>
                    <option value="mid" {{ old('price_tier', 'mid') == 'mid' ? 'selected' : '' }}>Mid-range (₹₹)</option>
                    <option value="premium" {{ old('price_tier') == 'premium' ? 'selected' : '' }}>Premium (₹₹₹)</option>
                </select>
                @error('price_tier') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </label>
        </div>

        {{-- City --}}
        <label class="block min-w-0">
            <span class="text-xs font-semibold text-slate-600">City / Service Area *</span>
            <input type="text" name="city" required value="{{ old('city') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base sm:text-sm" placeholder="e.g. Mumbai, Maharashtra">
            @error('city') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </label>

        {{-- Contact Phone & WhatsApp Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <label class="block min-w-0">
                <span class="text-xs font-semibold text-slate-600">Contact Number *</span>
                <input type="text" name="phone" required value="{{ old('phone') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base sm:text-sm" placeholder="Phone to receive calls">
                @error('phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </label>

            <label class="block min-w-0">
                <span class="text-xs font-semibold text-slate-600">WhatsApp Number (Optional)</span>
                <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base sm:text-sm" placeholder="Phone to receive WhatsApp chat">
                @error('whatsapp') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </label>
        </div>

        {{-- Description --}}
        <label class="block min-w-0">
            <span class="text-xs font-semibold text-slate-600">Business Description</span>
            <textarea name="description" rows="5" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base sm:text-sm" placeholder="Describe your services, package inclusions, and years of experience...">{{ old('description') }}</textarea>
            @error('description') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </label>

        {{-- Portfolio Images Upload --}}
        <div class="block min-w-0">
            <span class="text-xs font-semibold text-slate-600">Portfolio Photos (Max 6 images, up to 4MB each)</span>
            <div class="mt-1 p-4 bg-slate-50 border border-slate-200 rounded-lg flex flex-col items-center justify-center text-center">
                <i class="fa-regular fa-images text-2xl text-slate-400 mb-2"></i>
                <input type="file" name="images[]" multiple accept="image/*" class="text-sm text-slate-600 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cb-navy file:text-white hover:file:bg-slate-800 cursor-pointer">
                <p class="text-[0.7rem] text-slate-400 mt-1.5">You can select up to 6 files at once.</p>
            </div>
            @error('images') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        {{-- Action Button --}}
        <div class="pt-4">
            <button type="submit" class="cb-btn cb-btn-gold w-full justify-center text-base sm:text-sm min-h-[2.75rem] font-bold">
                Submit Registration
            </button>
        </div>
    </form>
</div>
@endsection
