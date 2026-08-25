<?php $__env->startSection('title', 'UPI Scanner — ' . $event->title); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-xl mx-auto">
    <div class="mb-6 flex items-center gap-3">
        <a href="<?php echo e(route('client.ganpati.show', $event->id)); ?>" class="gp-back-btn">
            <i class="fas fa-arrow-left text-sm" aria-hidden="true"></i>
        </a>
        <div class="min-w-0">
            <p class="gp-page-label">🪔 Ganpati Special</p>
            <h1 class="gp-page-title">UPI Scanner / QR Code</h1>
            <p class="text-xs cb-subtitle truncate"><?php echo e($event->title); ?></p>
        </div>
    </div>

    
    <?php if($event->upi_id || $event->gpay_qr_image): ?>
    <div class="gp-qr-box mb-5">
        <?php if($event->gpay_qr_image): ?>
        <div class="shrink-0">
            <img src="<?php echo e(asset('storage/' . $event->gpay_qr_image)); ?>" alt="UPI QR Scanner"
                 class="h-36 w-36 rounded-xl object-contain bg-white dark:bg-slate-900 shadow"
                 style="border:2px solid var(--gp-border);">
        </div>
        <?php endif; ?>
        <div class="min-w-0 text-center sm:text-left">
            <p class="gp-qr-box__label">Current Scanner</p>
            <?php if($event->upi_id): ?>
                <p class="gp-qr-box__value"><?php echo e($event->upi_id); ?></p>
                <a href="<?php echo e(route('client.ganpati.qr', $event->id)); ?>" target="_blank" class="gp-qr-box__link">
                    <i class="fas fa-qrcode" aria-hidden="true"></i> View UPI QR Code
                </a>
            <?php else: ?>
                <p class="text-sm cb-subtitle">Scanner image uploaded — add a UPI ID to generate QR.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="gp-form-card">
        <h2 class="text-sm font-bold text-cb-navy mb-4">
            <?php echo e(($event->upi_id || $event->gpay_qr_image) ? 'Update Scanner Details' : 'Add UPI Scanner'); ?>

        </h2>

        <form method="POST" action="<?php echo e(route('client.ganpati.scanner.save', $event->id)); ?>"
              enctype="multipart/form-data">
            <?php echo csrf_field(); ?>

            <div class="mb-4">
                <label for="upi_id" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    UPI ID <span class="text-slate-400 font-normal normal-case">(generates QR automatically)</span>
                </label>
                <input type="text" id="upi_id" name="upi_id"
                       value="<?php echo e(old('upi_id', $event->upi_id)); ?>"
                       placeholder="e.g. ganpatifund@ybl" maxlength="255"
                       class="cb-field w-full <?php $__errorArgs = ['upi_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['upi_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                <?php $__errorArgs = ['scanner_qr'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <button type="submit" class="gp-btn w-full py-3">
                <i class="fas fa-save" aria-hidden="true"></i>
                Save Scanner Details
            </button>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/ganpati/scanner.blade.php ENDPATH**/ ?>