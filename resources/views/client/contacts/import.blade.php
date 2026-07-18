@extends('layouts.client')

@section('title', 'Import contacts')

@section('content')
<div class="w-full max-w-2xl mx-auto">
    <header class="mb-6 sm:mb-8">
        <a href="{{ route('client.contacts.index') }}"
           class="cb-link text-sm inline-flex items-center gap-2 py-2 -ml-0.5 sm:py-1 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--cb-gold)]/40 focus-visible:ring-offset-2 focus-visible:ring-offset-[var(--cb-cream-2)]">
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>
            <span>Back to contacts</span>
        </a>
        <h1 class="cb-page-title mt-3 sm:mt-4">Import contacts</h1>
        <p class="cb-subtitle max-w-prose">Upload a vCard (.vcf) or CSV file exported from your phone.</p>
    </header>

    <div class="cb-card">
        <form method="POST" action="{{ route('client.contacts.import.store') }}" enctype="multipart/form-data" class="p-4 sm:p-6 lg:p-8 space-y-5">
            @csrf

            @if($errors->any())
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label class="cb-label cb-label--classic" for="contacts_file">Contacts file *</label>
                <input
                    id="contacts_file"
                    type="file"
                    name="contacts_file"
                    accept=".vcf,.vcard,.csv,text/vcard,text/x-vcard,text/csv"
                    required
                    class="cb-field min-h-[48px] w-full file:mr-3 file:rounded-lg file:border-0 file:bg-cb-navy file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white file:cursor-pointer hover:file:bg-[#142a35]"
                >
                <p class="mt-2 text-xs text-slate-500">Accepted: .vcf (vCard) or .csv. Max 5 MB.</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 text-sm text-slate-700 space-y-3">
                <p class="font-semibold text-cb-navy">How to export contacts from your phone</p>
                <div>
                    <p class="font-medium mt-1">iPhone</p>
                    <ol class="list-decimal list-inside text-xs text-slate-600 space-y-0.5 mt-1">
                        <li>Open <em>Contacts</em>, select all, then Share &rarr; AirDrop / Mail to yourself as a vCard.</li>
                        <li>Or use iCloud.com &rarr; Contacts &rarr; gear icon &rarr; <em>Export vCard</em>.</li>
                    </ol>
                </div>
                <div>
                    <p class="font-medium">Android</p>
                    <ol class="list-decimal list-inside text-xs text-slate-600 space-y-0.5 mt-1">
                        <li>Open <em>Contacts</em> app &rarr; menu &rarr; <em>Settings</em> &rarr; <em>Export</em>.</li>
                        <li>Choose <em>Export to .vcf file</em> and save it, then upload it here.</li>
                    </ol>
                </div>
                <p class="text-xs text-slate-500 mt-2">Duplicates (same phone or email) are skipped automatically.</p>
            </div>

            <div class="pt-4 border-t border-slate-200/80 flex flex-col-reverse sm:flex-row gap-3 sm:justify-end">
                <a href="{{ route('client.contacts.index') }}" class="cb-btn cb-btn--ghost w-full sm:w-auto min-h-[48px] px-6 justify-center">Cancel</a>
                <button type="submit" class="cb-btn cb-btn--gold w-full sm:w-auto min-h-[48px] px-6 justify-center shadow-sm">
                    <i class="fas fa-file-import text-sm" aria-hidden="true"></i>
                    Import contacts
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
