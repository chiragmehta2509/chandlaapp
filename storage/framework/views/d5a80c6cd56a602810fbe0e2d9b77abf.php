<?php $__env->startSection('title', 'New invitation'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-5 sm:mb-6 max-w-4xl mx-auto">
    <a href="<?php echo e(route('client.marriage-invitations.index')); ?>" class="cb-link text-sm inline-flex items-center gap-2 mb-3">
        <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to invitations
    </a>
    <h1 class="cb-page-title"><?php echo e($meta['name']); ?></h1>
    <?php if(!empty($meta['subtitle'])): ?>
        <p class="cb-subtitle max-w-2xl"><?php echo e($meta['subtitle']); ?></p>
    <?php else: ?>
        <p class="cb-subtitle max-w-2xl">Fill in the details for your card. You can still edit them after saving.</p>
    <?php endif; ?>
</div>

<?php
    $F = $meta['fields'];
?>

<div class="max-w-4xl mx-auto px-0">
    <form method="POST" action="<?php echo e(route('client.marriage-invitations.store')); ?>" enctype="multipart/form-data" class="space-y-4 sm:space-y-5">
        <?php echo csrf_field(); ?>

        <?php if($errors->any()): ?>
            <div class="mb-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                <p class="font-semibold">Please check the form</p>
                <ul class="mt-2 list-disc list-inside space-y-0.5">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($err); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        
        <section class="cb-card p-4 sm:p-6 lg:p-7 border border-slate-200/90 shadow-sm" aria-labelledby="sec-couple">
            <h2 id="sec-couple" class="text-base sm:text-lg font-bold text-cb-navy border-b border-slate-100 pb-3 mb-4 sm:mb-5">The couple</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                <?php echo $__env->renderWhen(isset($F['groom_name']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'groom_name', 'field' => $F['groom_name']], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
                <?php echo $__env->renderWhen(isset($F['bride_name']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'bride_name', 'field' => $F['bride_name']], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 mt-4 sm:mt-5">
                <?php echo $__env->renderWhen(isset($F['parent_groom']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'parent_groom', 'field' => $F['parent_groom']], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
                <?php echo $__env->renderWhen(isset($F['parent_bride']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'parent_bride', 'field' => $F['parent_bride']], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
            </div>
            <div class="mt-4 sm:mt-5 max-w-full">
                <?php echo $__env->renderWhen(isset($F['couple_image']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'couple_image', 'field' => $F['couple_image']], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
            </div>
        </section>

        
        <section class="cb-card p-4 sm:p-6 lg:p-7 border border-slate-200/90 shadow-sm" aria-labelledby="sec-venue">
            <h2 id="sec-venue" class="text-base sm:text-lg font-bold text-cb-navy border-b border-slate-100 pb-3 mb-4 sm:mb-5">Wedding day &amp; venue</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 sm:max-w-2xl">
                <?php echo $__env->renderWhen(isset($F['wedding_date']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'wedding_date', 'field' => $F['wedding_date']], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
                <?php echo $__env->renderWhen(isset($F['wedding_time']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'wedding_time', 'field' => $F['wedding_time']], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
            </div>
            <div class="mt-4 sm:mt-5">
                <?php echo $__env->renderWhen(isset($F['venue_name']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'venue_name', 'field' => $F['venue_name']], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
            </div>
            <div class="mt-4 sm:mt-5">
                <?php echo $__env->renderWhen(isset($F['venue_address']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'venue_address', 'field' => $F['venue_address']], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
            </div>
        </section>

        
        <section class="cb-card p-4 sm:p-6 lg:p-7 border border-slate-200/90 shadow-sm" aria-labelledby="sec-text">
            <h2 id="sec-text" class="text-base sm:text-lg font-bold text-cb-navy border-b border-slate-100 pb-3 mb-4 sm:mb-5">Wording on the card</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                <?php echo $__env->renderWhen(isset($F['rsvp_contact']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'rsvp_contact', 'field' => $F['rsvp_contact']], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
                <?php echo $__env->renderWhen(isset($F['tagline']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'tagline', 'field' => $F['tagline']], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
            </div>
        </section>

        
        <section class="cb-card p-4 sm:p-6 lg:p-7 border border-slate-200/90 shadow-sm" aria-labelledby="sec-schedule">
            <h2 id="sec-schedule" class="text-base sm:text-lg font-bold text-cb-navy border-b border-slate-100 pb-3 mb-1">Schedule of events <span class="text-slate-400 font-normal text-sm">(optional)</span></h2>
            <p class="text-xs text-slate-500 mb-3 sm:mb-4">Up to 8 items. Skip rows you don’t need.</p>
            <?php echo $__env->renderWhen(isset($F['schedule_events']), 'client.marriage-invitations.partials.form-field', ['fieldKey' => 'schedule_events', 'field' => $F['schedule_events']], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
        </section>

        
        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-2 pb-6 sm:pb-8">
            <a href="<?php echo e(route('client.marriage-invitations.index')); ?>" class="cb-btn cb-btn--ghost w-full sm:w-auto min-h-[2.75rem] px-6 justify-center">
                Cancel
            </a>
            <button type="submit" class="cb-btn cb-btn-gold w-full sm:w-auto min-h-[2.75rem] px-8 sm:px-10 justify-center text-base font-bold shadow-md">
                <i class="fas fa-check"></i> Save &amp; continue
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/marriage-invitations/create.blade.php ENDPATH**/ ?>