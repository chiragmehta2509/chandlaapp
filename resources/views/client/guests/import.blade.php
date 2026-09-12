@extends('layouts.client')

@section('title', 'Import Guests')

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
                <i class="fas fa-file-import text-lg sm:text-xl"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="cb-page-title text-2xl sm:text-3xl leading-tight">Import guests</h1>
                <p class="cb-subtitle mt-1.5 max-w-xl text-sm sm:text-base leading-relaxed">
                    Upload a <strong>.vcf</strong> (vCard) or <strong>.csv</strong> file exported from your phone to bulk-import guests.
                </p>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <i class="fas fa-exclamation-circle mr-1"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="cb-card overflow-hidden border border-slate-200/80 shadow-sm rounded-2xl">
        <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50/90 via-white to-violet-50/20">
            <h2 class="text-sm font-bold text-cb-navy tracking-wide">Upload contact file</h2>
            <p class="text-xs sm:text-sm text-slate-600 mt-1">Duplicates (same phone or email) will be skipped automatically.</p>
        </div>

        <form method="POST" action="{{ route('client.guests.import.store') }}" enctype="multipart/form-data" class="p-4 sm:p-6 space-y-6">
            @csrf

            <div class="space-y-2">
                <label for="guests-file" class="block text-sm font-semibold text-slate-800">
                    Choose file <span class="text-red-600 font-bold">*</span>
                </label>
                <div class="relative">
                    <input id="guests-file"
                           type="file"
                           name="guests_file"
                           accept=".vcf,.vcard,.txt,.csv"
                           required
                           class="cb-field w-full min-h-[2.75rem] text-base sm:text-sm @error('guests_file') border-red-300 ring-2 ring-red-100 @enderror
                                  file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                  file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700
                                  hover:file:bg-violet-100 cursor-pointer">
                </div>
                @error('guests_file')
                    <p class="text-sm text-red-600 mt-1.5">{{ $message }}</p>
                @enderror
                <p class="text-xs text-slate-500">Accepted formats: <code class="bg-slate-100 px-1 rounded">.vcf</code>, <code class="bg-slate-100 px-1 rounded">.csv</code>. Max size: 5 MB.</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-2">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">How to export contacts from your phone</p>
                <ul class="space-y-1.5 text-xs text-slate-600">
                    <li class="flex items-start gap-2">
                        <i class="fab fa-android text-emerald-600 mt-0.5 shrink-0"></i>
                        <span><strong>Android:</strong> Open Contacts app → Menu → Export → Choose .vcf format</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fab fa-apple text-slate-600 mt-0.5 shrink-0"></i>
                        <span><strong>iPhone:</strong> Use <em>iCloud → Contacts → Export vCard</em> on your computer</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fab fa-google text-blue-600 mt-0.5 shrink-0"></i>
                        <span><strong>Google Contacts:</strong> contacts.google.com → Export → Google CSV or vCard</span>
                    </li>
                </ul>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                <a href="{{ route('client.guests.index') }}" class="cb-btn cb-btn--ghost w-full sm:w-auto min-h-[48px] px-6 justify-center">Cancel</a>
                <button type="submit" id="guests-import-btn" class="cb-btn cb-btn--gold w-full sm:w-auto min-h-[48px] px-8 font-bold justify-center">
                    <i class="fas fa-file-import" aria-hidden="true"></i>
                    Import guests
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
