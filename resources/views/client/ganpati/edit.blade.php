@extends('layouts.client')

@section('title', 'Edit Ganpati Event')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('client.ganpati.show', $event->id) }}" class="gp-back-btn">
            <i class="fas fa-arrow-left text-sm" aria-hidden="true"></i>
        </a>
        <div class="min-w-0">
            <p class="gp-page-label">🪔 Ganpati Special</p>
            <h1 class="gp-page-title">Edit Ganpati Event</h1>
            <p class="text-xs cb-subtitle truncate max-w-xs">{{ $event->title }}</p>
        </div>
    </div>

    <div class="gp-form-card">
        <form method="POST" action="{{ route('client.ganpati.update', $event->id) }}" id="ganpati-edit-form">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="title" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Event Title <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title', $event->title) }}"
                       required maxlength="255"
                       class="cb-field w-full @error('title') border-red-400 @enderror">
                @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="event_date" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Event Date <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="date" id="event_date" name="event_date"
                       value="{{ old('event_date', optional($event->event_date)->format('Y-m-d')) }}"
                       required class="cb-field w-full @error('event_date') border-red-400 @enderror">
                @error('event_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="venue" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Venue <span class="text-slate-400 font-normal normal-case">(optional)</span>
                </label>
                <input type="text" id="venue" name="venue" value="{{ old('venue', $event->venue) }}"
                       maxlength="255" class="cb-field w-full @error('venue') border-red-400 @enderror">
                @error('venue') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="upi_id" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    UPI ID <span class="text-slate-400 font-normal normal-case">(optional)</span>
                </label>
                <input type="text" id="upi_id" name="upi_id" value="{{ old('upi_id', $event->upi_id) }}"
                       maxlength="255" class="cb-field w-full @error('upi_id') border-red-400 @enderror">
                @error('upi_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label for="description" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Notes <span class="text-slate-400 font-normal normal-case">(optional)</span>
                </label>
                <textarea id="description" name="description" rows="2"
                          class="cb-field w-full resize-none @error('description') border-red-400 @enderror">{{ old('description', $event->description) }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <a href="{{ route('client.ganpati.show', $event->id) }}"
                   class="flex-1 flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    Cancel
                </a>
                <button type="submit" class="gp-btn flex-1 py-2.5">
                    <i class="fas fa-check" aria-hidden="true"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
