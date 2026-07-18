@extends('layouts.client')

@section('title', 'New invitation')

@section('content')
<div class="mb-5 sm:mb-6 max-w-4xl mx-auto">
    <a href="{{ route('client.marriage-invitations.index') }}" class="cb-link text-sm inline-flex items-center gap-2 mb-3">
        <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to invitations
    </a>
    <h1 class="cb-page-title">{{ $meta['name'] }}</h1>
    @if(!empty($meta['subtitle']))
        <p class="cb-subtitle max-w-2xl">{{ $meta['subtitle'] }}</p>
    @else
        <p class="cb-subtitle max-w-2xl">Fill in the details for your card. You can still edit them after saving.</p>
    @endif
</div>

@php
    $F = $meta['fields'];
@endphp

<div class="max-w-4xl mx-auto px-0">
    <form method="POST" action="{{ route('client.marriage-invitations.store') }}" enctype="multipart/form-data" class="space-y-4 sm:space-y-5">
        @csrf

        @if($errors->any())
            <div class="mb-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                <p class="font-semibold">Please check the form</p>
                <ul class="mt-2 list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- The couple --}}
        <section class="cb-card p-4 sm:p-6 lg:p-7 border border-slate-200/90 shadow-sm" aria-labelledby="sec-couple">
            <h2 id="sec-couple" class="text-base sm:text-lg font-bold text-cb-navy border-b border-slate-100 pb-3 mb-4 sm:mb-5">The couple</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                @includeWhen(isset($F['groom_name']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'groom_name', 'field' => $F['groom_name']])
                @includeWhen(isset($F['bride_name']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'bride_name', 'field' => $F['bride_name']])
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 mt-4 sm:mt-5">
                @includeWhen(isset($F['parent_groom']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'parent_groom', 'field' => $F['parent_groom']])
                @includeWhen(isset($F['parent_bride']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'parent_bride', 'field' => $F['parent_bride']])
            </div>
            <div class="mt-4 sm:mt-5 max-w-full">
                @includeWhen(isset($F['couple_image']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'couple_image', 'field' => $F['couple_image']])
            </div>
        </section>

        {{-- Date & venue --}}
        <section class="cb-card p-4 sm:p-6 lg:p-7 border border-slate-200/90 shadow-sm" aria-labelledby="sec-venue">
            <h2 id="sec-venue" class="text-base sm:text-lg font-bold text-cb-navy border-b border-slate-100 pb-3 mb-4 sm:mb-5">Wedding day &amp; venue</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 sm:max-w-2xl">
                @includeWhen(isset($F['wedding_date']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'wedding_date', 'field' => $F['wedding_date']])
                @includeWhen(isset($F['wedding_time']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'wedding_time', 'field' => $F['wedding_time']])
            </div>
            <div class="mt-4 sm:mt-5">
                @includeWhen(isset($F['venue_name']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'venue_name', 'field' => $F['venue_name']])
            </div>
            <div class="mt-4 sm:mt-5">
                @includeWhen(isset($F['venue_address']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'venue_address', 'field' => $F['venue_address']])
            </div>
        </section>

        {{-- Text on card --}}
        <section class="cb-card p-4 sm:p-6 lg:p-7 border border-slate-200/90 shadow-sm" aria-labelledby="sec-text">
            <h2 id="sec-text" class="text-base sm:text-lg font-bold text-cb-navy border-b border-slate-100 pb-3 mb-4 sm:mb-5">Wording on the card</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                @includeWhen(isset($F['rsvp_contact']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'rsvp_contact', 'field' => $F['rsvp_contact']])
                @includeWhen(isset($F['tagline']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'tagline', 'field' => $F['tagline']])
            </div>
        </section>

        {{-- Schedule (full width) --}}
        <section class="cb-card p-4 sm:p-6 lg:p-7 border border-slate-200/90 shadow-sm" aria-labelledby="sec-schedule">
            <h2 id="sec-schedule" class="text-base sm:text-lg font-bold text-cb-navy border-b border-slate-100 pb-3 mb-1">Schedule of events <span class="text-slate-400 font-normal text-sm">(optional)</span></h2>
            <p class="text-xs text-slate-500 mb-3 sm:mb-4">Up to 8 items. Skip rows you don’t need.</p>
            @includeWhen(isset($F['schedule_events']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'schedule_events', 'field' => $F['schedule_events']])
        </section>

        {{-- Actions: sticky on small screens is optional; use clear full-width button --}}
        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-2 pb-6 sm:pb-8">
            <a href="{{ route('client.marriage-invitations.index') }}" class="cb-btn cb-btn--ghost w-full sm:w-auto min-h-[2.75rem] px-6 justify-center">
                Cancel
            </a>
            <button type="submit" class="cb-btn cb-btn-gold w-full sm:w-auto min-h-[2.75rem] px-8 sm:px-10 justify-center text-base font-bold shadow-md">
                <i class="fas fa-check"></i> Save &amp; continue
            </button>
        </div>
    </form>
</div>
@endsection
