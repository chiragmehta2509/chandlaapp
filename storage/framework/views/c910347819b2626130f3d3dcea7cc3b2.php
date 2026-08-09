

<?php $__env->startSection('title', 'Create Event'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-2xl mx-auto">
    <header class="mb-6 sm:mb-8">
        <a
            href="<?php echo e(route('client.events.index')); ?>"
            class="cb-link text-sm inline-flex items-center gap-2 py-2 -ml-0.5 sm:py-1 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--cb-gold)]/40 focus-visible:ring-offset-2 focus-visible:ring-offset-[var(--cb-cream-2)]"
        >
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>
            <span>Back to events</span>
        </a>
        <h1 class="cb-page-title mt-3 sm:mt-4">New event</h1>
        <p class="cb-subtitle max-w-prose">Add the basics now—you can update details anytime.</p>
    </header>

    <div class="cb-card">
        <form method="POST" action="<?php echo e(route('client.events.store')); ?>" class="p-4 sm:p-6 lg:p-8">
            <?php echo csrf_field(); ?>

            <?php if(isset($freeEventCredits) && $freeEventCredits > 0): ?>
                <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 flex items-start gap-3">
                    <i class="fas fa-ticket text-amber-500 mt-1" aria-hidden="true"></i>
                    <div>
                        <h3 class="text-sm font-bold text-amber-900">You have <?php echo e($freeEventCredits); ?> Free Event Credit(s)!</h3>
                        <p class="text-sm text-amber-800 mt-1 mb-3">You can redeem 1 point to create an event without using your plan limits.</p>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="redeem_coin" value="1" class="rounded border-amber-300 text-amber-600 focus:ring-amber-500" <?php echo e((isset($autoRedeem) && $autoRedeem) ? 'checked' : ''); ?>>
                            <span class="text-sm font-semibold text-amber-900">Redeem 1 Credit for this event</span>
                        </label>
                    </div>
                </div>
            <?php endif; ?>

            <div class="space-y-5 sm:space-y-6">
                <section aria-labelledby="create-event-details-heading">
                    <h2 id="create-event-details-heading" class="cb-section-label">Event details</h2>
                    <div class="space-y-4 sm:space-y-5">
                        <div>
                            <label class="cb-label cb-label--classic" for="event-title">Event title *</label>
                            <input
                                id="event-title"
                                type="text"
                                name="title"
                                value="<?php echo e(old('title')); ?>"
                                required
                                autocomplete="off"
                                class="cb-field min-h-[48px]"
                                placeholder="e.g. Wedding reception, community dinner…"
                            >
                        </div>
                        <div>
                            <label class="cb-label cb-label--classic" for="event-description">Description <span class="font-normal text-slate-400">(optional)</span></label>
                            <textarea
                                id="event-description"
                                name="description"
                                rows="4"
                                class="cb-field min-h-[7.5rem] resize-y"
                                placeholder="Notes for guests, dress code, or schedule…"
                            ><?php echo e(old('description')); ?></textarea>
                        </div>
                    </div>
                </section>

                <section aria-labelledby="create-event-when-heading" class="pt-2 sm:pt-4 border-t border-slate-200/80">
                    <h2 id="create-event-when-heading" class="cb-section-label">When &amp; where</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        <div class="sm:col-span-1">
                            <label class="cb-label cb-label--classic" for="event-date">Event date *</label>
                            <?php
                                $minEventDate = now()->toDateString();
                            ?>
                            <input
                                id="event-date"
                                type="date"
                                name="event_date"
                                value="<?php echo e(old('event_date', $minEventDate)); ?>"
                                min="<?php echo e($minEventDate); ?>"
                                required
                                class="cb-field min-h-[48px] w-full"
                            >
                            <p class="mt-1.5 text-xs text-slate-500">You can choose today or any future date.</p>
                        </div>
                        <div class="sm:col-span-1">
                            <label class="cb-label cb-label--classic" for="event-time">Event time <span class="font-normal text-slate-400">(optional)</span></label>
                            <input
                                id="event-time"
                                type="time"
                                name="event_time"
                                value="<?php echo e(old('event_time')); ?>"
                                class="cb-field min-h-[48px] w-full"
                            >
                        </div>
                        <div class="sm:col-span-2">
                            <label class="cb-label cb-label--classic" for="event-venue">Venue <span class="font-normal text-slate-400">(optional)</span></label>
                            <input
                                id="event-venue"
                                type="text"
                                name="venue"
                                value="<?php echo e(old('venue')); ?>"
                                class="cb-field min-h-[48px]"
                                placeholder="Hall name, city, or address"
                            >
                        </div>
                        <div class="sm:col-span-2">
                            <label class="cb-label cb-label--classic" id="event-type-label" for="event-type-trigger">Event type <span class="font-normal text-slate-400">(optional)</span></label>
                            <?php echo $__env->make('client.events.partials.event-type-select', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                    </div>
                </section>
            </div>

            <div class="mt-8 sm:mt-10 pt-5 sm:pt-6 border-t border-slate-200/80 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end sm:items-center">
                <a
                    href="<?php echo e(route('client.events.index')); ?>"
                    class="cb-btn cb-btn--ghost w-full sm:w-auto min-h-[48px] px-6 justify-center"
                >
                    Cancel
                </a>
                <button type="submit" class="cb-btn cb-btn--gold w-full sm:w-auto min-h-[48px] px-6 justify-center shadow-sm">
                    <i class="fas fa-calendar-plus text-sm" aria-hidden="true"></i>
                    Create event
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/events/create.blade.php ENDPATH**/ ?>