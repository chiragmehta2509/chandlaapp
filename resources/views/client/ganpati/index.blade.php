@extends('layouts.client')

@section('title', 'Ganpati Special')

@section('content')
<div class="mb-6 sm:mb-8 flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between lg:gap-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:gap-5 min-w-0 flex-1">
        <div class="gp-event-card__icon shrink-0 h-12 w-12 sm:h-14 sm:w-14 rounded-2xl shadow-sm" aria-hidden="true">
            <span style="font-size:1.6rem;">🪔</span>
        </div>
        <div class="min-w-0">
            <h1 class="cb-page-title text-2xl sm:text-3xl leading-tight gp-page-title">Ganpati Special</h1>
            <p class="cb-subtitle mt-1.5 max-w-xl text-sm sm:text-base leading-relaxed">
                Collect Ganpati Utsav chanda entries — <strong class="gp-page-label" style="text-transform:none; letter-spacing:normal; font-size:inherit;">free &amp; unlimited</strong> for all users.
            </p>
        </div>
    </div>
    @canEdit
    @if($events->isEmpty())
    <a href="{{ route('client.ganpati.create') }}" class="gp-btn w-full lg:w-auto shrink-0 min-h-[2.75rem] px-5 touch-manipulation">
        <i class="fas fa-plus text-sm" aria-hidden="true"></i>
        <span>New Ganpati Event</span>
    </a>
    @endif
    @endcanEdit
</div>

{{-- Festive banner --}}
<div class="gp-banner">
    <div class="gp-banner__inner">
        <div class="flex-1 min-w-0">
            <p class="gp-banner__title">🙏 Ganpati Bappa Morya!</p>
            <p class="gp-banner__body">
                This section is dedicated to Ganpati Utsav chanda collection. Create your single dedicated event for Ganpati, add your UPI scanner, and record all chanda entries. (Use the main Events module for other events).
                <a href="{{ route('client.ganpati.index') }}" class="gp-qr-box__link" style="display:inline; margin-left:4px;">Download the PDF</a>
                at any time — completely free.
            </p>
        </div>
        <div class="gp-banner__badge">
            <i class="fas fa-infinity" aria-hidden="true"></i>
            Unlimited entries
        </div>
    </div>
</div>

{{-- Search --}}
<form method="GET" action="{{ route('client.ganpati.index') }}" class="mb-6">
    <div class="relative max-w-sm">
        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm" aria-hidden="true">
            <i class="fas fa-search"></i>
        </span>
        <input type="search" name="search" value="{{ request('search') }}"
               placeholder="Search events…"
               class="cb-field w-full min-h-[2.75rem] pl-10 pr-3 text-sm">
    </div>
</form>

@if($events->isEmpty())
    <div class="gp-empty">
        <div class="gp-empty__icon">
            <span style="font-size:2rem;">🪔</span>
        </div>
        <h2 class="text-base font-bold text-cb-navy mb-2">No Ganpati events yet</h2>
        <p class="text-sm cb-subtitle mb-5 max-w-xs mx-auto">Create your first Ganpati event to start collecting chanda entries.</p>
        @canEdit
        <a href="{{ route('client.ganpati.create') }}" class="gp-btn">
            <i class="fas fa-plus" aria-hidden="true"></i> Create First Event
        </a>
        @endcanEdit
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5">
        @foreach($events as $event)
        <a href="{{ route('client.ganpati.show', $event->id) }}" class="gp-event-card">
            <div class="gp-event-card__accent"></div>
            <div class="flex items-start justify-between gap-3 mb-3">
                <div class="gp-event-card__icon">
                    <span style="font-size:1.25rem;">🪔</span>
                </div>
                <span class="gp-event-card__badge ml-auto">{{ $event->chandlas_count ?? 0 }} entries</span>
            </div>
            <h2 class="gp-event-card__title">{{ $event->title }}</h2>
            <p class="gp-event-card__meta">
                <i class="fas fa-calendar-day mr-1" aria-hidden="true"></i>
                {{ optional($event->event_date)->format('d M Y') ?? '—' }}
                @if($event->venue)
                    · <i class="fas fa-map-marker-alt mr-1" aria-hidden="true"></i>{{ $event->venue }}
                @endif
            </p>
            <div class="gp-event-card__footer">
                <span class="gp-event-card__footer-text">View entries &amp; download PDF</span>
                <i class="fas fa-arrow-right gp-event-card__arrow" aria-hidden="true"></i>
            </div>
        </a>
        @endforeach
    </div>
@endif
@endsection
