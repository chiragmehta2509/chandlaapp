<?php $__env->startSection('title', 'Edit Event'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto w-full">
    <div class="mb-6">
        <a href="<?php echo e(route('client.events.show', $event->id)); ?>" class="cb-link mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Event
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Edit Event</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-6 sm:p-8">
        <form method="POST" action="<?php echo e(route('client.events.update', $event->id)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Event Title *</label>
                <input type="text" name="title" value="<?php echo e(old('title', $event->title)); ?>" required
                       class="cb-field w-full">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="4"
                          class="cb-field w-full"><?php echo e(old('description', $event->description)); ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Date *</label>
                    <input type="date" name="event_date" value="<?php echo e(old('event_date', $event->event_date->format('Y-m-d'))); ?>" required
                           class="cb-field w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Time</label>
                    <input type="time" name="event_time" value="<?php echo e(old('event_time', $event->event_time ? $event->event_time->format('H:i') : '')); ?>"
                           class="cb-field w-full">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Venue</label>
                <input type="text" name="venue" value="<?php echo e(old('venue', $event->venue)); ?>"
                       class="cb-field w-full">
            </div>

            <div class="mb-6">
                <label class="cb-label cb-label--classic" id="event-type-label" for="event-type-trigger">Event type</label>
                <?php echo $__env->make('client.events.partials.event-type-select', ['selectedId' => $event->event_type_id], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="<?php echo e(route('client.events.show', $event->id)); ?>" class="cb-btn cb-btn-ghost">
                    Cancel
                </a>
                <button type="submit" class="cb-btn cb-btn-gold">
                    <i class="fas fa-check text-sm opacity-90" aria-hidden="true"></i>
                    Update Event
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/events/edit.blade.php ENDPATH**/ ?>