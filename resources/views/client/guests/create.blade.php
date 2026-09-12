@extends('layouts.client')

@section('title', 'Add Guest')

@section('content')
<div class="max-w-2xl mx-auto w-full min-w-0">
    <div class="mb-6 sm:mb-8">
        <a href="{{ route('client.guests.index') }}"
           class="cb-link text-sm inline-flex items-center gap-2 mb-4 sm:mb-5 touch-manipulation">
            <i class="fas fa-arrow-left text-xs opacity-80" aria-hidden="true"></i>
            <span>Back to guests</span>
        </a>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:gap-5">
            <div class="flex h-12 w-12 sm:h-14 sm:w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-500/15 to-purple-600/10 text-violet-700 ring-1 ring-violet-200/60 shadow-sm"
                 aria-hidden="true">
                <i class="fas fa-user-plus text-lg sm:text-xl"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="cb-page-title text-2xl sm:text-3xl leading-tight">Add guest</h1>
                <p class="cb-subtitle mt-1.5 max-w-xl text-sm sm:text-base leading-relaxed">
                    Add a guest with their name, number, address and relationship.
                </p>
            </div>
        </div>
    </div>

    <div class="cb-card overflow-hidden border border-slate-200/80 shadow-sm rounded-2xl">
        <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50/90 via-white to-violet-50/20">
            <h2 class="text-sm font-bold text-cb-navy tracking-wide">New guest</h2>
            <p class="text-xs sm:text-sm text-slate-600 mt-1">Only <strong class="font-semibold text-slate-800">name</strong> is required. Everything else is optional.</p>
        </div>

        <form method="POST" action="{{ route('client.guests.store') }}" class="divide-y divide-slate-100">
            @csrf

            {{-- Basic Info --}}
            <section class="space-y-5 p-4 sm:p-6 lg:p-8 pb-8 sm:pb-10" aria-labelledby="guest-basic-heading">
                <h3 id="guest-basic-heading" class="flex items-center gap-2.5 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 ring-1 ring-slate-200/80">
                        <i class="fas fa-address-card text-[0.7rem]" aria-hidden="true"></i>
                    </span>
                    Basic info
                </h3>

                <div class="space-y-1.5">
                    <label for="guest-name" class="block text-sm font-semibold text-slate-800">
                        Full name <span class="text-red-600 font-bold">*</span>
                    </label>
                    <input id="guest-name"
                           type="text"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           autocomplete="name"
                           maxlength="255"
                           class="cb-field w-full min-h-[2.75rem] text-base sm:text-sm @error('name') border-red-300 ring-2 ring-red-100 @enderror"
                           placeholder="e.g. Ramesh Kumar">
                    @error('name')
                        <p class="text-sm text-red-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6">
                    <div class="space-y-1.5 min-w-0">
                        <label for="guest-phone" class="block text-sm font-semibold text-slate-800">Phone</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm" aria-hidden="true">
                                <i class="fas fa-phone"></i>
                            </span>
                            <input id="guest-phone"
                                   type="tel"
                                   inputmode="tel"
                                   name="phone"
                                   value="{{ old('phone') }}"
                                   autocomplete="tel"
                                   class="cb-field w-full min-h-[2.75rem] !pl-10 text-base sm:text-sm @error('phone') border-red-300 ring-2 ring-red-100 @enderror"
                                   placeholder="+91 …">
                        </div>
                        @error('phone')
                            <p class="text-sm text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-1.5 min-w-0">
                        <label for="guest-email" class="block text-sm font-semibold text-slate-800">Email</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm" aria-hidden="true">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input id="guest-email"
                                   type="email"
                                   inputmode="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   autocomplete="email"
                                   class="cb-field w-full min-h-[2.75rem] !pl-10 text-base sm:text-sm @error('email') border-red-300 ring-2 ring-red-100 @enderror"
                                   placeholder="name@example.com">
                        </div>
                        @error('email')
                            <p class="text-sm text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Address --}}
            <section class="space-y-5 p-4 sm:p-6 lg:p-8 py-8 sm:py-10" aria-labelledby="guest-address-heading">
                <h3 id="guest-address-heading" class="flex items-center gap-2.5 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 ring-1 ring-slate-200/80">
                        <i class="fas fa-location-dot text-[0.7rem]" aria-hidden="true"></i>
                    </span>
                    Address
                </h3>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6">
                    <div class="sm:col-span-2 space-y-1.5">
                        <label for="guest-address" class="block text-sm font-semibold text-slate-800">Street / area</label>
                        <textarea id="guest-address"
                                  name="address"
                                  rows="3"
                                  class="cb-field w-full resize-y min-h-[5.5rem] text-base sm:text-sm leading-relaxed @error('address') border-red-300 ring-2 ring-red-100 @enderror"
                                  placeholder="Flat, landmark, street…">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="text-sm text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label for="guest-city" class="block text-sm font-semibold text-slate-800">City</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm" aria-hidden="true">
                                <i class="fas fa-city"></i>
                            </span>
                            <input id="guest-city"
                                   type="text"
                                   name="city"
                                   value="{{ old('city') }}"
                                   maxlength="100"
                                   class="cb-field w-full min-h-[2.75rem] !pl-10 text-base sm:text-sm @error('city') border-red-300 ring-2 ring-red-100 @enderror"
                                   placeholder="e.g. Ahmedabad">
                        </div>
                        @error('city')
                            <p class="text-sm text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Relationship & Notes --}}
            <section class="space-y-5 p-4 sm:p-6 lg:p-8 py-8 sm:py-10" aria-labelledby="guest-extra-heading">
                <h3 id="guest-extra-heading" class="flex items-center gap-2.5 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 ring-1 ring-slate-200/80">
                        <i class="fas fa-note-sticky text-[0.7rem]" aria-hidden="true"></i>
                    </span>
                    Notes &amp; relationship
                </h3>

                <div class="space-y-1.5">
                    <label for="guest-relationship" class="block text-sm font-semibold text-slate-800">Relationship</label>
                    <input id="guest-relationship"
                           type="text"
                           name="relationship"
                           value="{{ old('relationship') }}"
                           maxlength="100"
                           list="relationship-suggestions"
                           class="cb-field w-full min-h-[2.75rem] text-base sm:text-sm @error('relationship') border-red-300 ring-2 ring-red-100 @enderror"
                           placeholder="Friend, cousin, relative, colleague…">
                    <datalist id="relationship-suggestions">
                        <option value="Friend">
                        <option value="Cousin">
                        <option value="Uncle">
                        <option value="Aunt">
                        <option value="Brother">
                        <option value="Sister">
                        <option value="Colleague">
                        <option value="Neighbour">
                        <option value="Family">
                        <option value="Relative">
                    </datalist>
                    @error('relationship')
                        <p class="text-sm text-red-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="guest-notes" class="block text-sm font-semibold text-slate-800">Notes</label>
                    <textarea id="guest-notes"
                              name="notes"
                              rows="3"
                              class="cb-field w-full resize-y min-h-[5.5rem] text-base sm:text-sm leading-relaxed @error('notes') border-red-300 ring-2 ring-red-100 @enderror"
                              placeholder="Any additional notes about this guest…">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-sm text-red-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end p-4 sm:p-6 bg-slate-50/60">
                <a href="{{ route('client.guests.index') }}"
                   class="cb-btn cb-btn--ghost w-full sm:w-auto justify-center min-h-[48px] px-6">
                    Cancel
                </a>
                <button type="submit" id="guest-save-btn"
                        class="cb-btn cb-btn--gold w-full sm:w-auto justify-center min-h-[48px] px-8 font-bold">
                    <i class="fas fa-user-plus text-sm" aria-hidden="true"></i>
                    Save guest
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
