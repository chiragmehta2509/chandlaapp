@extends('layouts.client')

@section('title', 'Upload GPay Screenshot')

@section('content')
<div class="mb-6">
    <a href="{{ route('client.chandlas.index') }}" class="cb-link mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Back to Chandlas
    </a>
    <h1 class="text-3xl font-bold text-gray-800">Upload GPay Screenshot</h1>
    <p class="text-gray-600 mt-1">Upload a screenshot of your GPay transaction to quickly create a chandla record</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- GPay Receiving Details -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-2">Set Your GPay Receiving Details</h2>
        <p class="text-sm text-gray-600 mb-4">Upload your GPay QR screenshot and UPI ID so guests can pay you.</p>

        <form method="POST" action="{{ route('client.gpay.details') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Event *</label>
                <select name="event_id" required 
                        class="cb-field">
                    <option value="">Select Event</option>
                    @foreach($events as $evt)
                        <option value="{{ $evt->id }}" {{ old('event_id', $eventId) == $evt->id ? 'selected' : '' }}>
                            {{ $evt->title }} - {{ $evt->event_date->format('d/m/Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">UPI ID *</label>
                <input type="text" name="upi_id" value="{{ old('upi_id', $event->upi_id ?? '') }}"
                       class="cb-field"
                       placeholder="example@upi">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">GPay QR Screenshot (Optional)</label>
                <input type="file" name="gpay_qr_image" accept="image/*"
                       class="cb-field">
                @if($event && $event->gpay_qr_image)
                    <div class="mt-3">
                        <p class="text-xs text-gray-500 mb-2">Current QR (uploaded):</p>
                        <img src="{{ asset('storage/' . $event->gpay_qr_image) }}" alt="GPay QR" class="max-w-xs h-auto rounded-lg border border-gray-300">
                    </div>
                @elseif(!empty($upiQrSvg))
                    <div class="mt-3">
                        <p class="text-xs text-gray-500 mb-2">QR generated from UPI ID:</p>
                        <div class="inline-block p-3 border border-gray-300 rounded-lg bg-gray-50">
                            {!! $upiQrSvg !!}
                        </div>
                    </div>
                @endif
                <p class="text-xs text-gray-500 mt-2">If you don't upload a QR, we'll generate one from your UPI ID.</p>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                Save GPay Details
            </button>
        </form>
    </div>

    {{--
    <!-- Upload Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Upload GPay Image</h2>
        
        <form method="POST" action="{{ route('client.gpay.upload') }}" enctype="multipart/form-data" id="gpayUploadForm">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Event *</label>
                <select name="event_id" id="event_id" required 
                        class="cb-field">
                    <option value="">Select Event</option>
                    @foreach($events as $evt)
                        <option value="{{ $evt->id }}"
                                data-upi-id="{{ $evt->upi_id }}"
                                data-gpay-qr="{{ $evt->gpay_qr_image ? asset('storage/' . $evt->gpay_qr_image) : '' }}"
                                {{ old('event_id', $eventId) == $evt->id ? 'selected' : '' }}>
                            {{ $evt->title }} - {{ $evt->event_date->format('d/m/Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="upload_gpay_qr_section" class="mb-6 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pay to this UPI (Scanner)</label>
                <div class="inline-block p-3 border border-gray-300 rounded-lg bg-gray-50">
                    <img id="upload_gpay_qr_image" src="" alt="UPI QR" class="h-48 w-48 object-contain">
                </div>
                <p class="text-xs text-gray-500 mt-2">UPI ID: <span id="upload_gpay_upi_id_text">-</span></p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">GPay Screenshot *</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition-colors" id="dropZone">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="gpay_image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                <span>Upload a file</span>
                                <input id="gpay_image" name="gpay_image" type="file" accept="image/*" class="sr-only" required>
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                        <p class="text-xs text-gray-500" id="fileName"></p>
                    </div>
                </div>
                <div id="imagePreview" class="mt-4 hidden">
                    <img id="previewImg" src="" alt="Preview" class="max-w-full h-auto rounded-lg border border-gray-300">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Giver Name</label>
                <input type="text" name="giver_name" value="{{ old('giver_name') }}"
                       class="cb-field"
                       placeholder="Enter giver name (optional)">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="text" name="giver_phone" value="{{ old('giver_phone') }}"
                           class="cb-field"
                           placeholder="Phone number">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                    <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0"
                           class="cb-field"
                           placeholder="0.00">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Transaction ID</label>
                <input type="text" name="gpay_transaction_id" value="{{ old('gpay_transaction_id') }}"
                       class="cb-field"
                       placeholder="GPay transaction ID (optional)">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Received Date</label>
                <input type="date" name="received_date" value="{{ old('received_date', date('Y-m-d')) }}"
                       class="cb-field">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" rows="3"
                          class="cb-field"
                          placeholder="Additional notes (optional)">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('client.chandlas.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="cb-btn cb-btn-gold">
                    <i class="fas fa-upload mr-2"></i>Upload & Create Chandla
                </button>
            </div>
        </form>
    </div>
    --}}

    <!-- Instructions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">How to Use</h2>
        <div class="space-y-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 font-bold">1</div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900">Select Event</h3>
                    <p class="text-sm text-gray-500 mt-1">Choose the event for which you received the GPay payment</p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 font-bold">2</div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900">Take Screenshot</h3>
                    <p class="text-sm text-gray-500 mt-1">Open your GPay app and take a screenshot of the transaction details</p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 font-bold">3</div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900">Upload Image</h3>
                    <p class="text-sm text-gray-500 mt-1">Upload the screenshot by clicking or dragging and dropping</p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 font-bold">4</div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900">Fill Details</h3>
                    <p class="text-sm text-gray-500 mt-1">Optionally fill in the giver name, amount, and transaction ID from the screenshot</p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 font-bold">5</div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900">Submit</h3>
                    <p class="text-sm text-gray-500 mt-1">Click upload to create the chandla record. You can edit it later if needed.</p>
                </div>
            </div>
        </div>

        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-800">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Tip:</strong> After uploading, you'll be redirected to the chandla details page where you can review and update any information extracted from the screenshot.
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const eventSelect = document.getElementById('event_id');
    const qrSection = document.getElementById('upload_gpay_qr_section');
    const qrImage = document.getElementById('upload_gpay_qr_image');
    const upiIdText = document.getElementById('upload_gpay_upi_id_text');

    function updateQrDisplay() {
        if (!eventSelect || !qrSection) return;
        
        const selected = eventSelect.options[eventSelect.selectedIndex];
        const upiId = selected?.dataset?.upiId || '';
        const qrPath = selected?.dataset?.gpayQr || '';

        if (!upiId && !qrPath) {
            qrSection.classList.add('hidden');
            return;
        }

        if (upiIdText) upiIdText.textContent = upiId || 'N/A';

        if (qrPath) {
            qrImage.src = qrPath;
        } else if (upiId) {
            qrImage.src = `{{ url('/client/gpay/upi-qr') }}/${eventSelect.value}`;
        }

        qrSection.classList.remove('hidden');
    }

    if (eventSelect) {
        eventSelect.addEventListener('change', updateQrDisplay);
        if (eventSelect.value) updateQrDisplay();
    }

    const fileInput = document.getElementById('gpay_image');
    const dropZone = document.getElementById('dropZone');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const fileName = document.getElementById('fileName');

    // File input change
    fileInput.addEventListener('change', function(e) {
        handleFile(e.target.files[0]);
    });

    // Drag and drop
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
});
</script>
@endsection
