<?php $__env->startSection('title', 'Edit Ganpati Event'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-xl mx-auto">
    <div class="mb-6 flex items-center gap-3">
        <a href="<?php echo e(route('client.ganpati.show', $event->id)); ?>" class="gp-back-btn">
            <i class="fas fa-arrow-left text-sm" aria-hidden="true"></i>
        </a>
        <div class="min-w-0">
            <p class="gp-page-label">🪔 Ganpati Special</p>
            <h1 class="gp-page-title">Edit Ganpati Event</h1>
            <p class="text-xs cb-subtitle truncate max-w-xs"><?php echo e($event->title); ?></p>
        </div>
    </div>

    <div class="gp-form-card">
        <form method="POST" action="<?php echo e(route('client.ganpati.update', $event->id)); ?>" id="ganpati-edit-form">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="mb-4">
                <label for="title" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Event Title <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="text" id="title" name="title" value="<?php echo e(old('title', $event->title)); ?>"
                       required maxlength="255"
                       class="cb-field w-full <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="mb-4">
                <label for="event_date" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Event Date <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="date" id="event_date" name="event_date"
                       value="<?php echo e(old('event_date', optional($event->event_date)->format('Y-m-d'))); ?>"
                       required class="cb-field w-full <?php $__errorArgs = ['event_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['event_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="mb-4">
                <label for="venue" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Venue <span class="text-slate-400 font-normal normal-case">(optional)</span>
                </label>
                <input type="text" id="venue" name="venue" value="<?php echo e(old('venue', $event->venue)); ?>"
                       maxlength="255" class="cb-field w-full <?php $__errorArgs = ['venue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['venue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="mb-4">
                <label for="upi_id" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    UPI ID <span class="text-slate-400 font-normal normal-case">(optional)</span>
                </label>
                <input type="text" id="upi_id" name="upi_id" value="<?php echo e(old('upi_id', $event->upi_id)); ?>"
                       maxlength="255" class="cb-field w-full <?php $__errorArgs = ['upi_id'];
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
            </div>

            <div class="mb-5">
                <label for="description" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Notes <span class="text-slate-400 font-normal normal-case">(optional)</span>
                </label>
                <textarea id="description" name="description" rows="2"
                          class="cb-field w-full resize-none <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('description', $event->description)); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="flex gap-3">
                <a href="<?php echo e(route('client.ganpati.show', $event->id)); ?>"
                   class="flex-1 flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    Cancel
                </a>
                <button type="submit" class="gp-btn flex-1 py-2.5">
                    <i class="fas fa-check" aria-hidden="true"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/ganpati/edit.blade.php ENDPATH**/ ?>