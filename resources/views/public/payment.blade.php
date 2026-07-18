@extends('layouts.public-guest')

@php
    $seoTitle = 'Submit payment — ' . $event->title;
    $seoDesc = 'Submit your Google Pay payment details for ' . $event->title . ' — Chandla Book.';
    $seoRobots = 'noindex, nofollow';
@endphp

@section('content')
<div class="max-w-2xl mx-auto pt-2">
    @php
        $organizerLabel = optional($event->user)->name ?? 'the organiser';
        $eventWhen = $event->event_date ? $event->event_date->format('d/m/Y') : null;
    @endphp

    <div class="rounded-2xl bg-white text-slate-800 shadow-2xl shadow-slate-950/40 ring-1 ring-white/60 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600/10 via-violet-500/10 to-sky-500/10 px-6 pt-6 pb-4 border-b border-slate-100">
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 mb-1">Event payment</p>
            <h1 class="text-2xl font-bold text-slate-900 leading-tight">{{ $event->title }}</h1>
            <p class="text-sm text-slate-600 mt-2">
                You’re submitting a payment record for this event on Chandla Book.
                Organiser: <strong class="text-slate-800">{{ $organizerLabel }}</strong>
                @if ($eventWhen)
                    <span class="text-slate-500"> · {{ $eventWhen }}</span>
                @endif
                @if ($event->venue)
                    <span class="block mt-1 text-slate-500"><i class="fas fa-location-dot text-indigo-400 mr-1" aria-hidden="true"></i>{{ $event->venue }}</span>
                @endif
            </p>
        </div>

        <div class="p-6 md:p-8">
            <!-- Payment Form — routes & fields unchanged -->
            <form method="POST" action="{{ route('public.payment.submit', ['event' => $event->id, 'token' => request('token')]) }}"
                  enctype="multipart/form-data" id="paymentForm">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Your Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="giver_name" required
                               class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white"
                               placeholder="Enter your full name">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Phone Number</label>
                            <input type="text" name="giver_phone"
                                   class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white"
                                   placeholder="10-digit mobile number">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                            <input type="email" name="giver_email"
                                   class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white"
                                   placeholder="your@email.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Amount (₹) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="amount" required step="0.01" min="0"
                               class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white tabular-nums"
                               placeholder="0.00">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            GPay Payment Screenshot <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-200 border-dashed rounded-xl hover:border-indigo-400 transition-colors bg-slate-50/80" id="dropZone">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-cloud-upload-alt text-4xl text-slate-400"></i>
                                <div class="flex flex-wrap justify-center text-sm text-slate-600 gap-x-1">
                                    <label for="gpay_image" class="relative cursor-pointer rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                        <span>Upload screenshot</span>
                                        <input id="gpay_image" name="gpay_image" type="file" accept="image/*" class="sr-only" required>
                                    </label>
                                    <span>or drag and drop</span>
                                </div>
                                <p class="text-xs text-slate-500">PNG, JPG, GIF up to 5MB</p>
                                <p class="text-xs text-slate-500" id="fileName"></p>
                            </div>
                        </div>
                        <div id="imagePreview" class="mt-4 hidden">
                            <img id="previewImg" src="" alt="Preview" class="max-w-full h-auto rounded-xl border border-slate-200">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">GPay Transaction ID (Optional)</label>
                        <input type="text" name="gpay_transaction_id"
                               class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white"
                               placeholder="Transaction ID from GPay">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Additional Notes (Optional)</label>
                        <textarea name="notes" rows="3"
                                  class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white"
                                  placeholder="Any additional information..."></textarea>
                    </div>

                    <button type="submit"
                            class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 text-white px-6 py-4 rounded-xl hover:from-indigo-500 hover:to-violet-500 font-semibold text-lg shadow-lg shadow-indigo-900/20 touch-manipulation">
                        <i class="fas fa-paper-plane mr-2"></i>Submit Payment
                    </button>
                </div>
            </form>

            <div class="mt-6 p-4 bg-indigo-50 border border-indigo-100 rounded-xl">
                <h3 class="text-sm font-medium text-indigo-950 mb-2">
                    <i class="fas fa-info-circle mr-2 text-indigo-600"></i>How to submit payment
                </h3>
                <ol class="text-sm text-indigo-900/90 space-y-1 list-decimal list-inside">
                    <li>Make payment via GPay to the number given by your host</li>
                    <li>Take a screenshot of the payment confirmation</li>
                    <li>Fill in your details above</li>
                    <li>Upload the GPay screenshot</li>
                    <li>Tap Submit Payment</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('gpay_image');
    const dropZone = document.getElementById('dropZone');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const fileName = document.getElementById('fileName');

    fileInput.addEventListener('change', function(e) {
        handleFile(e.target.files[0]);
    });

    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.classList.add('border-indigo-400', 'bg-indigo-50');
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-indigo-400', 'bg-indigo-50');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-indigo-400', 'bg-indigo-50');

        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            handleFile(e.dataTransfer.files[0]);
        }
    });

    function handleFile(file) {
        if (file && file.type.startsWith('image/')) {
            fileName.textContent = file.name;

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    document.getElementById('paymentForm').addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting…';
    });
});
</script>
@endpush
