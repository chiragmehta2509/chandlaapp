@extends('layouts.client')

@section('title', 'UPI Scanner — ' . $event->title)

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('client.ganpati.show', $event->id) }}" class="gp-back-btn">
            <i class="fas fa-arrow-left text-sm" aria-hidden="true"></i>
        </a>
        <div class="min-w-0">
            <p class="gp-page-label">🪔 Ganpati Special</p>
            <h1 class="gp-page-title">UPI Scanner / QR Code</h1>
            <p class="text-xs cb-subtitle truncate">{{ $event->title }}</p>
        </div>
    </div>

    {{-- Current scanner --}}
    @if($event->upi_id || $event->gpay_qr_image)
    <div class="gp-qr-box mb-5">
        @if($event->gpay_qr_image)
        <div class="shrink-0">
            <img src="{{ asset('storage/' . $event->gpay_qr_image) }}" alt="UPI QR Scanner"
                 class="h-36 w-36 rounded-xl object-contain bg-white dark:bg-slate-900 shadow"
                 style="border:2px solid var(--gp-border);">
        </div>
        @endif
        <div class="min-w-0 text-center sm:text-left">
            <p class="gp-qr-box__label">Current Scanner</p>
            @if($event->upi_id)
                <p class="gp-qr-box__value">{{ $event->upi_id }}</p>
                <a href="{{ route('client.ganpati.qr', $event->id) }}" target="_blank" class="gp-qr-box__link">
                    <i class="fas fa-qrcode" aria-hidden="true"></i> View UPI QR Code
                </a>
            @else
                <p class="text-sm cb-subtitle">Scanner image uploaded — add a UPI ID to generate QR.</p>
            @endif
        </div>
    </div>
    @endif

    {{-- Upload form --}}
    <div class="gp-form-card">
        <h2 class="text-sm font-bold text-cb-navy mb-4">
            {{ ($event->upi_id || $event->gpay_qr_image) ? 'Update Scanner Details' : 'Add UPI Scanner' }}
        </h2>

        <form method="POST" action="{{ route('client.ganpati.scanner.save', $event->id) }}"
              enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="upi_id" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    UPI ID <span class="text-slate-400 font-normal normal-case">(generates QR automatically)</span>
                </label>
                <input type="text" id="upi_id" name="upi_id"
                       value="{{ old('upi_id', $event->upi_id) }}"
                       placeholder="e.g. ganpatifund@ybl" maxlength="255"
                       class="cb-field w-full @error('upi_id') border-red-400 @enderror">
                @error('upi_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs cb-subtitle">Entering your UPI ID lets you generate a scannable QR code.</p>
            </div>

            <div class="mb-5">
                <label for="scanner_qr" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Upload Scanner Image <span class="text-slate-400 font-normal normal-case">(optional)</span>
                </label>
                <div class="gp-dropzone" onclick="document.getElementById('scanner_qr').click()">
                    <div class="gp-dropzone__icon">
                        <i class="fas fa-cloud-arrow-up" aria-hidden="true"></i>
                    </div>
                    <p class="gp-dropzone__text">Click to upload scanner image</p>
                    <p class="gp-dropzone__sub">JPEG, PNG, GIF · Max 5 MB</p>
                    <input type="file" id="scanner_qr" name="scanner_qr" accept="image/*"
                           class="sr-only" onchange="previewScanner(this)">
                </div>
                <div id="scanner-preview" class="mt-3 hidden">
                    <img id="scanner-preview-img" src="" alt="Preview"
                         class="h-32 w-32 rounded-xl object-contain bg-white dark:bg-slate-900 shadow"
                         style="border:1.5px solid var(--gp-border);">
                </div>
                @error('scanner_qr') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="gp-btn w-full py-3">
                <i class="fas fa-save" aria-hidden="true"></i>
                Save Scanner Details
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function previewScanner(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('scanner-preview-img').src = e.target.result;
            document.getElementById('scanner-preview').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
