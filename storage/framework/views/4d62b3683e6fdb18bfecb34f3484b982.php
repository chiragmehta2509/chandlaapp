<?php $__env->startSection('title', 'Upload GPay Screenshot'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <a href="<?php echo e(route('client.chandlas.index')); ?>" class="cb-link mb-4 inline-block">
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

        <form method="POST" action="<?php echo e(route('client.gpay.details')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Event *</label>
                <select name="event_id" required 
                        class="cb-field">
                    <option value="">Select Event</option>
                    <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $evt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($evt->id); ?>" <?php echo e(old('event_id', $eventId) == $evt->id ? 'selected' : ''); ?>>
                            <?php echo e($evt->title); ?> - <?php echo e($evt->event_date->format('d/m/Y')); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">UPI ID *</label>
                <input type="text" name="upi_id" value="<?php echo e(old('upi_id', $event->upi_id ?? '')); ?>"
                       class="cb-field"
                       placeholder="example@upi">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">GPay QR Screenshot (Optional)</label>
                <input type="file" name="gpay_qr_image" accept="image/*"
                       class="cb-field">
                <?php if($event && $event->gpay_qr_image): ?>
                    <div class="mt-3">
                        <p class="text-xs text-gray-500 mb-2">Current QR (uploaded):</p>
                        <img src="<?php echo e(asset('storage/' . $event->gpay_qr_image)); ?>" alt="GPay QR" class="max-w-xs h-auto rounded-lg border border-gray-300">
                    </div>
                <?php elseif(!empty($upiQrSvg)): ?>
                    <div class="mt-3">
                        <p class="text-xs text-gray-500 mb-2">QR generated from UPI ID:</p>
                        <div class="inline-block p-3 border border-gray-300 rounded-lg bg-gray-50">
                            <?php echo $upiQrSvg; ?>

                        </div>
                    </div>
                <?php endif; ?>
                <p class="text-xs text-gray-500 mt-2">If you don't upload a QR, we'll generate one from your UPI ID.</p>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                Save GPay Details
            </button>
        </form>
    </div>

    

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
            qrImage.src = `<?php echo e(url('/client/gpay/upi-qr')); ?>/${eventSelect.value}`;
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/gpay/upload.blade.php ENDPATH**/ ?>