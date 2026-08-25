<?php $__env->startSection('title', 'GPay QR — ' . $event->title); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <a href="<?php echo e(route('client.events.show', $event->id)); ?>" class="cb-link mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Back to event
    </a>
    <h1 class="cb-page-title">GPay &amp; UPI for guests</h1>
    <p class="cb-subtitle"><?php echo e($event->title); ?> — set how you want to be paid, then show or print the right QR for guests</p>
</div>

<div class="mb-6 cb-card p-5 sm:p-6">
    <h2 class="text-lg font-bold text-cb-navy mb-2">1. Set your UPI (required) or upload your own QR (optional)</h2>
    <p class="text-sm text-slate-600 mb-4">Your UPI ID is used to generate a scan-to-pay code. You can also upload a screenshot of the QR from GPay, PhonePe, or Paytm. Both are stored on this event only.</p>

    <form method="POST" action="<?php echo e(route('client.gpay.details')); ?>" enctype="multipart/form-data" class="space-y-4 max-w-lg">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="event_id" value="<?php echo e($event->id); ?>">

        <div>
            <label class="cb-label" for="upi_id">UPI ID <span class="text-red-500">*</span></label>
            <input type="text" name="upi_id" id="upi_id" value="<?php echo e(old('upi_id', $event->upi_id ?? '')); ?>"
                   class="cb-field w-full" required placeholder="name@ybl, name@paytm, etc." autocomplete="off">
        </div>

        <div>
            <label class="cb-label" for="gpay_qr_image">Custom QR image (optional)</label>
            <input type="file" name="gpay_qr_image" id="gpay_qr_image" accept="image/jpeg,image/png,image/gif,image/jpg" class="cb-field w-full file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-slate-800 file:text-white hover:file:bg-slate-700 dark:file:bg-indigo-600 dark:file:text-white dark:hover:file:bg-indigo-500 cursor-pointer">
            <p class="text-xs text-slate-500 mt-1">If you don’t upload one, a QR is generated from your UPI ID.</p>
        </div>

        <?php if($event->gpay_qr_image): ?>
            <p class="text-sm text-slate-600">Current custom QR:</p>
            <img src="<?php echo e(asset('storage/' . $event->gpay_qr_image)); ?>" alt="Saved GPay QR" class="max-w-[200px] rounded-lg border border-slate-200">
        <?php endif; ?>

        <button type="submit" class="cb-btn cb-btn-navy w-full sm:w-auto justify-center">
            <i class="fas fa-save"></i> Save UPI &amp; QR
        </button>
    </form>
</div>

<?php
    $hasUpi = !empty($event->upi_id);
    $hasCustomQr = !empty($event->gpay_qr_image);
?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="print-root">
    <div class="cb-card p-5 sm:p-6 bg-white" id="guest-qr-section">
        <h2 class="text-xl font-bold text-cb-navy mb-2">2. Main QR (show to guests)</h2>
        <p class="text-sm text-slate-600 mb-4">Guests scan to pay you via UPI. This is the code you want on a screen or printout.</p>

        <div class="flex flex-col items-center justify-center p-6 sm:p-8 bg-cb-cream/80 rounded-xl mb-4 border border-slate-200 min-h-[280px]">
            <?php if($hasCustomQr): ?>
                <img id="main-qr"
                     src="<?php echo e(asset('storage/' . $event->gpay_qr_image)); ?>?v=<?php echo e($event->updated_at?->timestamp ?? $event->id); ?>"
                     alt="GPay UPI QR" class="w-64 h-64 sm:w-72 sm:h-72 object-contain border-4 border-white shadow-lg rounded-lg bg-white p-1">
                <p class="text-xs text-slate-500 mt-3 text-center">Custom uploaded QR</p>
            <?php elseif($hasUpi): ?>
                <img id="main-qr"
                     src="<?php echo e(route('client.gpay.upi-qr', $event->id)); ?>?t=<?php echo e($event->updated_at?->timestamp ?? $event->id); ?>"
                     alt="UPI QR" class="w-64 h-64 sm:w-72 sm:h-72 object-contain border-4 border-white shadow-lg rounded-lg bg-white p-1">
                <p class="text-xs text-slate-600 mt-3 font-mono">UPI: <?php echo e($event->upi_id); ?></p>
            <?php else: ?>
                <div class="text-center text-slate-500 max-w-sm">
                    <i class="fas fa-qrcode text-4xl text-slate-300 mb-2"></i>
                    <p class="text-sm">Enter your <strong>UPI ID</strong> above and save. We’ll show your scan-to-pay QR here.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="space-y-3">
            <?php if($hasUpi): ?>
                <div class="flex flex-col sm:flex-row gap-2">
                    <?php if($hasCustomQr): ?>
                        <a href="<?php echo e(asset('storage/' . $event->gpay_qr_image)); ?>"
                           download="gpay-qr-event-<?php echo e($event->id); ?>.png"
                           class="cb-btn cb-btn-gold w-full py-3 rounded-2xl text-center justify-center">
                            <i class="fas fa-download mr-2"></i>Download custom QR
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('client.gpay.upi-qr', $event->id)); ?>?t=<?php echo e($event->updated_at?->timestamp ?? 0); ?>"
                           download="upi-qr-event-<?php echo e($event->id); ?>.svg"
                           class="cb-btn cb-btn-gold w-full py-3 rounded-2xl text-center justify-center">
                            <i class="fas fa-download mr-2"></i>Download UPI QR (SVG)
                        </a>
                    <?php endif; ?>
                    <button type="button" onclick="window.print()"
                            class="w-full sm:flex-1 bg-emerald-600 text-white px-6 py-3 rounded-xl hover:bg-emerald-700 font-medium">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="cb-card p-5 sm:p-6 bg-white">
        <h2 class="text-xl font-bold text-cb-navy mb-2">3. Or: “Pay on website” QR (form + screenshot)</h2>
        <p class="text-sm text-slate-600 mb-4">This code opens the Chandla payment form where guests can enter name, amount, and upload a GPay screenshot. Use this if you prefer that flow instead of a raw UPI scan.</p>

        <div class="flex flex-col items-center p-4 bg-slate-50 rounded-xl border border-slate-200">
            <img src="<?php echo e(route('client.qrcode.generate', $event->id)); ?>"
                 alt="Payment page QR" class="w-56 h-56 sm:w-64 sm:h-64 object-contain border-4 border-white shadow rounded-lg">
        </div>

        <div class="mt-4 space-y-2">
            <a href="<?php echo e(route('client.qrcode.download', $event->id)); ?>"
               class="cb-btn cb-btn-navy w-full py-2.5 text-center justify-center text-sm">
                <i class="fas fa-download mr-2"></i>Download this QR (PNG)
            </a>
        </div>

        <div class="mt-4 p-3 bg-sky-50 border border-sky-200 rounded-lg text-sm text-sky-900 break-all">
            <strong>Link in QR:</strong>
            <code class="text-xs block mt-1"><?php echo e($paymentUrl); ?></code>
        </div>
    </div>
</div>

<div class="mt-6 cb-card p-5 sm:p-6">
    <h2 class="text-lg font-bold text-cb-navy mb-4">How to use on the day</h2>
    <div class="space-y-4 text-sm text-slate-600">
        <p><span class="font-bold text-amber-800">UPI / GPay</span> — use section 2. Guests open any UPI app, scan, and pay you. You can still record the entry in the ledger (Chandla) later if you want a paper trail.</p>
        <p><span class="font-bold text-cb-navy">Website form</span> — use section 3. Guests use your hosted form, upload proof, and you review entries. Good when you need names and amounts in one place.</p>
    </div>
    <div class="mt-4 flex flex-wrap gap-4 text-sm text-slate-500">
        <div class="bg-slate-50 p-3 rounded-lg flex-1 min-w-[120px]">
            <p class="text-xs text-slate-500">Chandlas (this event)</p>
            <p class="text-xl font-bold text-cb-navy"><?php echo e($event->chandlas->count()); ?></p>
        </div>
        <div class="bg-slate-50 p-3 rounded-lg flex-1 min-w-[120px]">
            <p class="text-xs text-slate-500">Total amount</p>
            <p class="text-xl font-bold text-cb-gold">₹<?php echo e(number_format($event->chandlas->sum('amount'), 2)); ?></p>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #print-root, #print-root *,
    #print-root *::before, #print-root *::after { visibility: visible; }
    #print-root { position: absolute; left: 0; top: 0; width: 100%; }
    #guest-qr-section { break-inside: avoid; }
    .cb-topnav, .cb-bottom-nav, .cb-fab, .cb-btn, a[href] { display: none !important; }
    @page { margin: 1.5cm; }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/qrcode/show.blade.php ENDPATH**/ ?>