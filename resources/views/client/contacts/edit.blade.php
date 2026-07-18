@extends('layouts.client')

@section('title', 'Edit Contact')

@section('content')
<div class="max-w-2xl mx-auto w-full min-w-0">
    <div class="mb-6 sm:mb-8">
        <a href="{{ route('client.contacts.show', $contact->id) }}"
           class="cb-link text-sm inline-flex items-center gap-2 mb-4 sm:mb-5 touch-manipulation">
            <i class="fas fa-arrow-left text-xs opacity-80" aria-hidden="true"></i>
            <span>Back to contact</span>
        </a>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:gap-5">
            <div class="flex h-12 w-12 sm:h-14 sm:w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500/15 to-violet-600/10 text-indigo-700 ring-1 ring-indigo-200/60 shadow-sm"
                 aria-hidden="true">
                <i class="fas fa-user-edit text-lg sm:text-xl"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="cb-page-title text-2xl sm:text-3xl leading-tight">Edit contact</h1>
                <p class="cb-subtitle mt-1.5 max-w-xl text-sm sm:text-base leading-relaxed">
                    Update the details for this contact.
                </p>
            </div>
        </div>
    </div>

    <div class="cb-card overflow-hidden border border-slate-200/80 shadow-sm rounded-2xl">
        <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50/90 via-white to-amber-50/20">
            <h2 class="text-sm font-bold text-cb-navy tracking-wide">Contact details</h2>
            <p class="text-xs sm:text-sm text-slate-600 mt-1">Only <strong class="font-semibold text-slate-800">name</strong> is required. Everything else is optional.</p>
        </div>

        <form method="POST" action="{{ route('client.contacts.update', $contact->id) }}" class="divide-y divide-slate-100">
            @csrf
            @method('PUT')

            {{-- Basic --}}
            <section class="space-y-5 p-4 sm:p-6 lg:p-8 pb-8 sm:pb-10" aria-labelledby="contact-basic-heading">
                <h3 id="contact-basic-heading" class="flex items-center gap-2.5 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 ring-1 ring-slate-200/80">
                        <i class="fas fa-address-card text-[0.7rem]" aria-hidden="true"></i>
                    </span>
                    Basic info
                </h3>

                <div class="space-y-1.5">
                    <label for="contact-name" class="block text-sm font-semibold text-slate-800">
                        Full name <span class="text-red-600 font-bold">*</span>
                    </label>
                    <input id="contact-name"
                           type="text"
                           name="name"
                           value="{{ old('name', $contact->name) }}"
                           required
                           autocomplete="name"
                           maxlength="255"
                           class="cb-field w-full min-h-[2.75rem] text-base sm:text-sm @error('name') border-red-300 ring-2 ring-red-100 @enderror"
                           placeholder="e.g. Priya Sharma">
                    @error('name')
                        <p class="text-sm text-red-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6">
                    <div class="space-y-1.5 min-w-0">
                        <label for="contact-phone" class="block text-sm font-semibold text-slate-800">Phone</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm" aria-hidden="true">
                                <i class="fas fa-phone"></i>
                            </span>
                            @php
                                $firstPhone = isset($contact->phone) ? explode(',', $contact->phone)[0] : '';
                            @endphp
                            <input id="contact-phone"
                                   type="tel"
                                   inputmode="tel"
                                   name="phone"
                                   value="{{ old('phone', $firstPhone) }}"
                                   autocomplete="tel"
                                   class="cb-field w-full min-h-[2.75rem] !pl-10 text-base sm:text-sm @error('phone') border-red-300 ring-2 ring-red-100 @enderror"
                                   placeholder="+91 …">
                        </div>
                        @error('phone')
                            <p class="text-sm text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-1.5 min-w-0">
                        <label for="contact-email" class="block text-sm font-semibold text-slate-800">Email</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm" aria-hidden="true">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input id="contact-email"
                                   type="email"
                                   inputmode="email"
                                   name="email"
                                   value="{{ old('email', $contact->email) }}"
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
            <section class="space-y-5 p-4 sm:p-6 lg:p-8 py-8 sm:py-10" aria-labelledby="contact-address-heading">
                <h3 id="contact-address-heading" class="flex items-center gap-2.5 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 ring-1 ring-slate-200/80">
                        <i class="fas fa-location-dot text-[0.7rem]" aria-hidden="true"></i>
                    </span>
                    Address
                </h3>
                <div class="space-y-1.5">
                    <label for="contact-address" class="block text-sm font-semibold text-slate-800">Street / area</label>
                    <textarea id="contact-address"
                              name="address"
                              rows="3"
                              class="cb-field w-full resize-y min-h-[5.5rem] text-base sm:text-sm leading-relaxed @error('address') border-red-300 ring-2 ring-red-100 @enderror"
                              placeholder="Flat, landmark, city…">{{ old('address', $contact->address) }}</textarea>
                    @error('address')
                        <p class="text-sm text-red-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            {{-- Extra --}}
            <section class="space-y-5 p-4 sm:p-6 lg:p-8 py-8 sm:py-10" aria-labelledby="contact-extra-heading">
                <h3 id="contact-extra-heading" class="flex items-center gap-2.5 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 ring-1 ring-slate-200/80">
                        <i class="fas fa-note-sticky text-[0.7rem]" aria-hidden="true"></i>
                    </span>
                    Notes &amp; relationship
                </h3>

                <div class="space-y-1.5">
                    <label for="contact-relationship" class="block text-sm font-semibold text-slate-800">Relationship</label>
                    <input id="contact-relationship"
                           type="text"
                           name="relationship"
                           value="{{ old('relationship', $contact->relationship) }}"
                           class="cb-field w-full min-h-[2.75rem] text-base sm:text-sm @error('relationship') border-red-300 ring-2 ring-red-100 @enderror"
                           placeholder="Friend, cousin, decorator…">
                    @error('relationship')
                        <p class="text-sm text-red-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="contact-notes" class="block text-sm font-semibold text-slate-800">Notes</label>
                    <textarea id="contact-notes"
                              name="notes"
                              rows="3"
                              class="cb-field w-full resize-y min-h-[5.5rem] text-base sm:text-sm leading-relaxed @error('notes') border-red-300 ring-2 ring-red-100 @enderror"
                              placeholder="Anything you want to remember">{{ old('notes', $contact->notes) }}</textarea>
                    @error('notes')
                        <p class="text-sm text-red-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            <div class="flex flex-col-reverse gap-3 p-4 sm:p-6 lg:p-8 pt-6 sm:pt-8 bg-slate-50/40 sm:flex-row sm:justify-end sm:items-center sm:gap-3">
                <a href="{{ route('client.contacts.show', $contact->id) }}"
                   class="cb-btn cb-btn-ghost w-full sm:w-auto justify-center min-h-[2.75rem] touch-manipulation">
                    Cancel
                </a>
                <button type="submit"
                        class="cb-btn cb-btn-gold inline-flex items-center gap-2 w-full sm:w-auto justify-center min-h-[2.75rem] shadow-md touch-manipulation">
                    <i class="fas fa-check text-sm opacity-90" aria-hidden="true"></i>
                    Update contact
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
