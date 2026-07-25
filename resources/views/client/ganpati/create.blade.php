@extends('layouts.client')

@section('title', 'Create Ganpati Event')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('client.ganpati.index') }}" class="gp-back-btn">
            <i class="fas fa-arrow-left text-sm" aria-hidden="true"></i>
        </a>
        <div>
            <p class="gp-page-label">🪔 Ganpati Special</p>
            <h1 class="gp-page-title">New Ganpati Event</h1>
            <p class="text-xs cb-subtitle mt-0.5">Free &amp; unlimited — no plan required.</p>
        </div>
    </div>

    <div class="gp-form-card">
        <form method="POST" action="{{ route('client.ganpati.store') }}" id="ganpati-create-form">
            @csrf

            <div class="mb-4">
                <label for="title" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Event Title <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title') }}"
                       placeholder="e.g. Ganpati Utsav 2025 — Sharma Family"
                       required maxlength="255"
                       class="cb-field w-full @error('title') border-red-400 @enderror">
                @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="event_date" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Event Date <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="date" id="event_date" name="event_date" value="{{ old('event_date') }}"
                       required
                       class="cb-field w-full @error('event_date') border-red-400 @enderror">
                @error('event_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="venue" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Venue <span class="text-slate-400 font-normal normal-case">(optional)</span>
                </label>
                <input type="text" id="venue" name="venue" value="{{ old('venue') }}"
                       placeholder="e.g. Main Hall, Ward No. 5" maxlength="255"
                       class="cb-field w-full @error('venue') border-red-400 @enderror">
                @error('venue') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="upi_id" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    UPI ID <span class="text-slate-400 font-normal normal-case">(optional — for scanner QR)</span>
                </label>
                <input type="text" id="upi_id" name="upi_id" value="{{ old('upi_id') }}"
                       placeholder="e.g. yourname@upi" maxlength="255"
                       class="cb-field w-full @error('upi_id') border-red-400 @enderror">
                @error('upi_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label for="description" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Notes <span class="text-slate-400 font-normal normal-case">(optional)</span>
                </label>
                <textarea id="description" name="description" rows="2" placeholder="Any additional notes…"
                          class="cb-field w-full resize-none @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="gp-btn w-full py-3">
                <i class="fas fa-check" aria-hidden="true"></i>
                Create Ganpati Event
            </button>
        </form>
    </div>
</div>
@endsection
