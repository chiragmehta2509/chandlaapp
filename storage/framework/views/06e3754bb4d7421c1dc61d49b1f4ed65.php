<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="sr-only">Dashboard</h1>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
    <div class="cb-card cb-card--hero relative overflow-hidden p-5 sm:p-6 lg:col-span-2">
        <div class="pointer-events-none absolute -right-20 -top-20 h-60 w-60 rounded-full bg-fuchsia-500/15 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-16 -left-10 h-48 w-48 rounded-full bg-cyan-500/10 blur-3xl" aria-hidden="true"></div>
        <div class="relative">
            <p class="text-xs sm:text-sm font-medium leading-relaxed text-sky-100/95 max-w-2xl">
                Welcome back, <span class="font-semibold text-white"><?php echo e(Auth::user()->name); ?></span><span class="text-indigo-100/80"> — here's a snapshot of your account.</span>
            </p>

            <div class="mt-3 min-w-0 sm:mt-4">
                <h2 class="text-lg font-bold tracking-tight text-white sm:text-xl lg:text-[1.4rem] leading-tight">Your activity</h2>
                <p class="mt-1 max-w-xl text-xs sm:text-sm leading-relaxed text-indigo-100/78">Events, ledger entries, and contacts in one place — tap a card to jump there.</p>
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <a href="<?php echo e(route('client.plans')); ?>" class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white shadow-sm backdrop-blur-sm transition hover:bg-white/18 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-300/60">
                        <i class="fa-solid fa-tags text-amber-200 text-[0.75rem]" aria-hidden="true"></i>
                        Plans &amp; pricing
                    </a>
                    <a href="<?php echo e(route('client.faq')); ?>" class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-transparent px-3 py-1.5 text-xs font-semibold text-indigo-50/95 transition hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/25">
                        <i class="fa-regular fa-circle-question text-indigo-100/90 text-[0.75rem]" aria-hidden="true"></i>
                        FAQ
                    </a>
                    <a href="<?php echo e(config('chandlabook.play_store_url')); ?>" target="_blank" class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-emerald-600/30 px-3 py-1.5 text-xs font-semibold text-white shadow-sm backdrop-blur-sm transition hover:bg-emerald-600/50">
                        <i class="fab fa-google-play text-green-400 text-[0.75rem]" aria-hidden="true"></i>
                        Android App
                    </a>
                    <a href="<?php echo e(config('chandlabook.app_store_url')); ?>" target="_blank" class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-slate-600/30 px-3 py-1.5 text-xs font-semibold text-white shadow-sm backdrop-blur-sm transition hover:bg-slate-600/50">
                        <i class="fab fa-apple text-slate-300 text-[0.75rem]" aria-hidden="true"></i>
                        iOS App
                    </a>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-3 gap-2 sm:gap-3">
                <a href="<?php echo e(route('client.events.index')); ?>" class="group relative flex min-h-[7.5rem] flex-col rounded-2xl border border-white/12 bg-slate-950/50 p-3 text-center shadow-sm ring-1 ring-white/[0.06] transition hover:border-amber-300/35 hover:bg-slate-950/70 hover:shadow-lg hover:shadow-black/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400/55 sm:min-h-[8rem] sm:p-4">
                    <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-xl bg-sky-500/25 text-sky-200 ring-1 ring-sky-400/20 transition group-hover:bg-sky-500/35 group-hover:text-white">
                        <i class="fas fa-calendar-day text-base" aria-hidden="true"></i>
                    </div>
                    <p class="mt-2 text-2xl font-bold tabular-nums leading-none tracking-tight text-white sm:text-[1.85rem]"><?php echo e($stats['total_events']); ?></p>
                    <p class="mt-1.5 text-[0.6rem] font-bold uppercase tracking-[0.14em] text-indigo-100/85">Events</p>
                </a>
                <a href="<?php echo e(route('client.chandlas.index')); ?>" class="group relative flex min-h-[7.5rem] flex-col rounded-2xl border border-white/12 bg-slate-950/50 p-3 text-center shadow-sm ring-1 ring-white/[0.06] transition hover:border-emerald-300/35 hover:bg-slate-950/70 hover:shadow-lg hover:shadow-black/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400/55 sm:min-h-[8rem] sm:p-4">
                    <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-200 ring-1 ring-emerald-400/25 transition group-hover:bg-emerald-500/35 group-hover:text-white">
                        <i class="fas fa-book-open text-base" aria-hidden="true"></i>
                    </div>
                    <p class="mt-2 text-2xl font-bold tabular-nums leading-none tracking-tight text-emerald-100 sm:text-[1.85rem]"><?php echo e($stats['total_entries']); ?></p>
                    <p class="mt-1.5 text-[0.6rem] font-bold uppercase tracking-[0.14em] text-indigo-100/85">Entries</p>
                </a>
                <a href="<?php echo e(route('client.contacts.index')); ?>" class="group relative flex min-h-[7.5rem] flex-col rounded-2xl border border-white/12 bg-slate-950/50 p-3 text-center shadow-sm ring-1 ring-white/[0.06] transition hover:border-violet-300/35 hover:bg-slate-950/70 hover:shadow-lg hover:shadow-black/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-400/55 sm:min-h-[8rem] sm:p-4">
                    <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-xl bg-violet-500/20 text-violet-200 ring-1 ring-violet-400/25 transition group-hover:bg-violet-500/35 group-hover:text-white">
                        <i class="fas fa-address-book text-base" aria-hidden="true"></i>
                    </div>
                    <p class="mt-2 text-2xl font-bold tabular-nums leading-none tracking-tight text-violet-100 sm:text-[1.85rem]"><?php echo e($stats['total_contacts']); ?></p>
                    <p class="mt-1.5 text-[0.6rem] font-bold uppercase tracking-[0.14em] text-indigo-100/85">Contacts</p>
                </a>
            </div>
        </div>
    </div>

    
    <div class="lg:col-span-1 flex flex-col gap-3">
        <div>
            <h2 class="text-base font-bold text-cb-navy sm:text-lg">Full breakdown</h2>
            <p class="text-xs text-slate-500">Deeper counts — events, contacts, ledger, and more</p>
        </div>
        <div class="grid grid-cols-2 gap-2.5 sm:gap-3">
            <div class="cb-card p-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-slate-500 text-[0.7rem] font-medium leading-tight">Total events</p>
                        <p class="text-lg font-bold text-cb-navy mt-0.5"><?php echo e($stats['total_events']); ?></p>
                    </div>
                    <div class="bg-sky-100/90 p-1.5 rounded-lg shrink-0">
                        <i class="fas fa-calendar text-sky-600 text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="cb-card p-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-slate-500 text-[0.7rem] font-medium leading-tight">Upcoming events</p>
                        <p class="text-lg font-bold text-cb-navy mt-0.5"><?php echo e($stats['upcoming_events']); ?></p>
                    </div>
                    <div class="bg-emerald-100/90 p-1.5 rounded-lg shrink-0">
                        <i class="fas fa-calendar-check text-emerald-600 text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="cb-card p-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-slate-500 text-[0.7rem] font-medium leading-tight">Total contacts</p>
                        <p class="text-lg font-bold text-cb-navy mt-0.5"><?php echo e($stats['total_contacts']); ?></p>
                    </div>
                    <div class="bg-violet-100/90 p-1.5 rounded-lg shrink-0">
                        <i class="fas fa-address-book text-violet-600 text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="cb-card p-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-slate-500 text-[0.7rem] font-medium leading-tight">Favorite contacts</p>
                        <p class="text-lg font-bold text-cb-navy mt-0.5"><?php echo e($stats['favorite_contacts']); ?></p>
                    </div>
                    <div class="bg-amber-100/90 p-1.5 rounded-lg shrink-0">
                        <i class="fas fa-star text-amber-600 text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="cb-card p-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-slate-500 text-[0.7rem] font-medium leading-tight">Total entries</p>
                        <p class="text-lg font-bold text-cb-navy mt-0.5"><?php echo e($stats['total_entries']); ?></p>
                    </div>
                    <div class="bg-slate-200/90 p-1.5 rounded-lg shrink-0">
                        <i class="fas fa-list text-cb-navy text-sm"></i>
                    </div>
                </div>
            </div>

            <div class="cb-card p-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-slate-500 text-[0.7rem] font-medium leading-tight">Total invitations</p>
                        <p class="text-lg font-bold text-cb-navy mt-0.5"><?php echo e($stats['total_invitations']); ?></p>
                    </div>
                    <div class="bg-rose-100/90 p-1.5 rounded-lg shrink-0">
                        <i class="fas fa-envelope text-rose-600 text-sm"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if($stats['show_global_free_limit']): ?>
<div class="mb-6">
    <div class="cb-card p-5 sm:p-6 border-l-4 border-amber-500">
        <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Free Plan Usage (All Events)</h2>
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
            Total free quota is <span class="font-semibold"><?php echo e($stats['global_free_limit_total']); ?></span> entries across all events until a paid plan is purchased.
        </p>
        <div class="flex flex-wrap gap-6 text-sm">
            <div>Used: <span class="font-semibold text-gray-900 dark:text-white"><?php echo e($stats['global_free_limit_used']); ?></span></div>
            <div>Remaining: <span class="font-semibold text-gray-900 dark:text-white"><?php echo e($stats['global_free_limit_remaining']); ?></span></div>
        </div>
        <div class="mt-4 flex flex-wrap items-center gap-2 sm:gap-3">
            <?php if($stats['show_free_limit_download']): ?>
                <a href="<?php echo e(route('client.chandlas.free-limit.download')); ?>" class="cb-btn cb-btn-navy cb-btn--sm inline-flex items-center">
                    <i class="fas fa-download mr-2"></i>Download First 50 Entries
                </a>
            <?php endif; ?>
            <?php if($upgradeEvent): ?>
                <a href="<?php echo e(route('client.events.plan.payment', ['id' => $upgradeEvent->id, 'plan' => 'unlimited'])); ?>" class="inline-flex items-center px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-sm font-semibold">
                    <i class="fas fa-arrow-up mr-2"></i>Upgrade Plan
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('client.events.create')); ?>" class="inline-flex items-center px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-sm font-semibold">
                    <i class="fas fa-plus mr-2"></i>Create Event to Upgrade Plan
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
<?php if($dashboardQuickEvents->isNotEmpty()): ?>
<div class="mb-6 sm:mb-8">
    <div class="cb-card p-5 sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between sm:gap-4 mb-5 sm:mb-6">
            <div class="flex items-start gap-3 min-w-0">
                <span class="hidden sm:inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-100 to-amber-200/70 text-amber-800 ring-1 ring-amber-200/80" aria-hidden="true">
                    <i class="fas fa-hand-holding-dollar"></i>
                </span>
                <div class="min-w-0">
                    <h2 class="text-lg sm:text-xl font-bold text-cb-navy">Add chandla</h2>
                    <p class="text-sm text-slate-500 mt-1 max-w-xl leading-relaxed">Pick an event — you skip choosing it again on the next screen.</p>
                </div>
            </div>
            <a href="<?php echo e(route('client.events.index')); ?>" class="cb-link text-sm font-medium whitespace-nowrap shrink-0 self-start sm:self-auto inline-flex items-center gap-1">
                All events <i class="fas fa-arrow-right text-[0.65rem]" aria-hidden="true"></i>
            </a>
        </div>
        <?php
            $quickEventCount = $dashboardQuickEvents->count();
            $quickListScrollable = $quickEventCount > 6;
            $showCreateTile = !$quickListScrollable && $quickEventCount < 3;
        ?>
        <?php if($quickListScrollable): ?>
            <p class="text-xs text-slate-500 mb-2 sm:hidden">Scroll the list to see more events.</p>
        <?php endif; ?>
        <div
            <?php if($quickListScrollable): ?>
                role="region"
                aria-label="Events — add chandla"
            <?php endif; ?>
            class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4 min-h-0 <?php echo e($quickListScrollable ? 'max-h-[min(70vh,32rem)] overflow-y-auto overscroll-y-contain touch-pan-y rounded-xl border border-slate-200/80 bg-slate-50/50 p-2 sm:p-3 [-webkit-overflow-scrolling:touch]' : ''); ?>"
        >
            <?php $__currentLoopData = $dashboardQuickEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="group flex flex-col gap-3 rounded-2xl border border-slate-200/80 bg-gradient-to-br from-white to-slate-50/70 p-4 min-w-0 shadow-sm hover:shadow-md hover:border-amber-300/70 transition-all duration-200">
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-cb-navy leading-snug break-words"><?php echo e($event->title); ?></p>
                        <div class="mt-2 flex flex-col gap-1.5 text-xs text-slate-600 sm:flex-row sm:flex-wrap sm:items-center sm:gap-x-3 sm:gap-y-1">
                            <span class="inline-flex items-start gap-1 shrink-0"><i class="fas fa-calendar-day text-cb-gold/90 mt-0.5 shrink-0" aria-hidden="true"></i><?php echo e($event->event_date->format('d/m/Y')); ?></span>
                            <?php if($event->venue): ?>
                                <span class="inline-flex items-start gap-1 break-words"><i class="fas fa-map-marker-alt text-cb-gold/90 mt-0.5 shrink-0" aria-hidden="true"></i><?php echo e($event->venue); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex flex-col min-[380px]:flex-row gap-2 pt-2 border-t border-slate-100">
                        <a href="<?php echo e(route('client.chandlas.create', ['event_id' => $event->id])); ?>"
                           class="inline-flex flex-1 items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl text-xs font-semibold bg-violet-100 text-violet-800 hover:bg-violet-600 hover:text-white transition-colors min-h-[44px]">
                            <i class="fas fa-plus" aria-hidden="true"></i>Chandla
                        </a>
                        <a href="<?php echo e(route('client.chandlas.create', ['event_id' => $event->id, 'lock_cash' => 1])); ?>"
                           class="inline-flex flex-1 items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl text-xs font-semibold bg-sky-100 text-sky-800 hover:bg-sky-600 hover:text-white transition-colors min-h-[44px]">
                            <i class="fas fa-file-invoice" aria-hidden="true"></i>Cover
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($showCreateTile): ?>
                <a href="<?php echo e(route('client.events.create')); ?>"
                   class="group flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-slate-300/90 bg-slate-50/50 p-4 min-h-[7.5rem] text-center hover:border-cb-gold hover:bg-amber-50/60 transition-colors">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-cb-gold ring-1 ring-amber-200/80 group-hover:bg-cb-gold group-hover:text-white transition-colors" aria-hidden="true">
                        <i class="fas fa-calendar-plus"></i>
                    </span>
                    <span class="text-sm font-semibold text-cb-navy">Create event</span>
                    <span class="text-xs text-slate-500 leading-snug max-w-[14rem]">Add another to record chandla against it.</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php else: ?>
<div class="mb-6 sm:mb-8">
    <div class="cb-card p-5 sm:p-6 border border-dashed border-slate-300/90 bg-slate-50/80">
        <h2 class="text-lg font-bold text-cb-navy">Add chandla</h2>
        <p class="text-sm text-slate-600 mt-1">Create an event first — then you can record chandla entries here.</p>
        <a href="<?php echo e(route('client.events.create')); ?>" class="inline-flex items-center mt-4 cb-btn cb-btn-gold cb-btn--sm">
            <i class="fas fa-calendar-plus mr-2" aria-hidden="true"></i>Create event
        </a>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
    
    <div class="cb-card p-5 sm:p-6 lg:col-span-2">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-cb-navy">Event-wise collections</h2>
                <p class="text-sm text-slate-500">Cash vs Cover vs Gift</p>
            </div>
            <div class="min-w-[220px]">
                <label class="block text-xs text-gray-500 mb-1">Select Event</label>
                <select id="eventChartSelect" class="cb-field text-sm">
                    <option value="all">All Events</option>
                    <?php $__currentLoopData = $eventBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eventRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($eventRow['id']); ?>"><?php echo e($eventRow['title']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <div class="h-80">
            <canvas id="eventCollectionsChart"></canvas>
        </div>
    </div>

    
    <div class="cb-card p-5 sm:p-6 lg:col-span-1">
        <h2 class="text-lg sm:text-xl font-bold text-cb-navy mb-4">Quick actions</h2>
        <div class="grid grid-cols-2 gap-3 sm:gap-4">
            <?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
            <a href="<?php echo e(route('client.chandlas.create')); ?>" class="cb-qaction cb-qaction--gold">
                <i class="fas fa-plus"></i>
                <span>Entry</span>
            </a>
            <a href="<?php echo e(route('client.events.create')); ?>" class="cb-qaction cb-qaction--blue">
                <i class="fas fa-calendar-plus"></i>
                <span>Event</span>
            </a>
            <a href="<?php echo e(route('client.contacts.create')); ?>" class="cb-qaction cb-qaction--teal">
                <i class="fas fa-user-plus"></i>
                <span>Contact</span>
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('client.events.index')); ?>" class="cb-qaction cb-qaction--violet">
                <i class="fas fa-calendar"></i>
                <span>All events</span>
            </a>
            <a href="<?php echo e(route('client.chandlas.index')); ?>" class="cb-qaction cb-qaction--violet">
                <i class="fas fa-list"></i>
                <span>Ledger</span>
            </a>
            <a href="<?php echo e(route('client.contacts.index')); ?>" class="cb-qaction cb-qaction--violet">
                <i class="fas fa-address-book"></i>
                <span>Contacts</span>
            </a>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Upcoming Events -->
    <div class="cb-card overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-slate-100">
            <h2 class="text-lg font-bold text-cb-navy">Upcoming events</h2>
        </div>
        <div class="p-4 sm:p-6">
            <div class="space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $upcoming_events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="border-b border-gray-200 pb-4 last:border-0">
                        <h3 class="font-semibold text-gray-900"><?php echo e($event->title); ?></h3>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-calendar mr-2"></i><?php echo e($event->event_date->format('d/m/Y')); ?>

                            <?php if($event->venue): ?>
                                <span class="ml-4"><i class="fas fa-map-marker-alt mr-2"></i><?php echo e($event->venue); ?></span>
                            <?php endif; ?>
                        </p>
                        <a href="<?php echo e(route('client.events.show', $event->id)); ?>" class="cb-link text-sm mt-2 inline-block">
                            View Details →
                        </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500 text-center py-4">No upcoming events</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Contacts -->
    <div class="cb-card overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-slate-100">
            <h2 class="text-lg font-bold text-cb-navy">Recent contacts</h2>
        </div>
        <div class="p-4 sm:p-6">
            <div class="space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $recent_contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-amber-100 flex items-center justify-center ring-2 ring-amber-200/60">
                                <span class="text-amber-800 font-semibold"><?php echo e(substr($contact->name, 0, 1)); ?></span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate"><?php echo e($contact->name); ?></p>
                            <p class="text-sm text-gray-500 truncate"><?php echo e($contact->phone ?? $contact->email ?? 'N/A'); ?></p>
                        </div>
                        <div class="flex-shrink-0">
                            <?php if($contact->is_favorite): ?>
                                <i class="fas fa-star text-yellow-500"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500 text-center py-4">No contacts yet</p>
                <?php endif; ?>
            </div>
            <div class="mt-4">
                <a href="<?php echo e(route('client.contacts.index')); ?>" class="cb-link text-sm font-medium">View all contacts →</a>
            </div>
        </div>
    </div>
</div>

<?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
<!-- Refer a friend — full width at end, left-aligned -->
<div class="mt-8 w-full text-left">
    <div class="cb-card p-5 sm:p-6 w-full max-w-none">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-5">
            <div>
                <h2 class="text-lg font-bold text-cb-navy mb-1 text-left">Refer a friend</h2>
                <p class="text-sm text-slate-600 text-left max-w-3xl">Share your referral code. When your friend pays for an event plan, you get 1 free event.</p>
            </div>
            <div class="shrink-0 text-left sm:text-right">
                <span class="cb-label !normal-case !tracking-normal !text-xs !text-slate-500 !mb-1 block sm:text-right">Free event credits (1 point = 1 free event)</span>
                <div class="inline-flex items-center gap-2 rounded-xl border border-amber-200/80 bg-gradient-to-br from-amber-50 to-amber-100/50 px-3 py-2.5 min-h-[2.75rem]">
                    <i class="fas fa-ticket text-cb-gold text-sm" aria-hidden="true"></i>
                    <span class="text-lg font-bold text-cb-gold leading-none"><?php echo e($stats['free_event_credits']); ?></span>
                    <span class="text-xs text-slate-600">available</span>
                </div>
                <?php if($stats['free_event_credits'] > 0): ?>
                <div class="mt-2 text-left sm:text-right">
                    <a href="<?php echo e(route('client.events.create', ['redeem' => 'true'])); ?>" class="text-xs font-semibold text-cb-gold hover:text-amber-700 underline decoration-amber-300 underline-offset-2">Redeem for Event →</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="flex flex-col lg:flex-row gap-4 lg:items-end w-full">
            <div class="w-full lg:w-[35%] min-w-0">
                <label class="cb-label !normal-case !tracking-normal !text-xs !text-slate-500 !mb-1 block text-left">Your referral code</label>
                <div class="flex items-stretch gap-2">
                    <div id="referralCodeText" class="flex-1 min-w-0 rounded-xl px-3 py-2.5 font-mono text-sm text-cb-navy bg-[#F0EDE6] border border-cb-navy/10 break-all text-left flex items-center dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700"><?php echo e($user->referral_code); ?></div>
                    <button type="button"
                            class="js-copy-btn shrink-0 inline-flex items-center justify-center gap-1.5 rounded-xl border border-cb-navy/15 bg-white text-cb-navy px-4 text-xs font-semibold hover:bg-cb-navy hover:text-white hover:border-cb-navy transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cb-gold/40"
                            data-copy-target="#referralCodeText"
                            aria-label="Copy referral code">
                        <i class="fas fa-copy text-sm" aria-hidden="true"></i>
                        <span class="js-copy-label hidden sm:inline">Copy</span>
                    </button>
                </div>
            </div>
            <div class="w-full lg:w-[65%] min-w-0">
                <label class="cb-label !normal-case !tracking-normal !text-xs !text-slate-500 !mb-1 block text-left">Referral link</label>
                <div class="flex items-stretch gap-2">
                    <div id="referralLinkText" class="flex-1 min-w-0 rounded-xl px-3 py-2.5 font-mono text-xs sm:text-sm text-cb-navy bg-[#F0EDE6] border border-cb-navy/10 break-all text-left flex items-center dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700"><?php echo e(route('client.register', ['ref' => $user->referral_code])); ?></div>
                    <button type="button"
                            class="js-copy-btn shrink-0 inline-flex items-center justify-center gap-1.5 rounded-xl border border-cb-navy/15 bg-white text-cb-navy px-4 text-xs font-semibold hover:bg-cb-navy hover:text-white hover:border-cb-navy transition focus:outline-none focus-visible:ring-2 focus-visible:ring-cb-gold/40"
                            data-copy-target="#referralLinkText"
                            aria-label="Copy referral link">
                        <i class="fas fa-copy text-sm" aria-hidden="true"></i>
                        <span class="js-copy-label hidden sm:inline">Copy</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const chartData = <?php echo json_encode($chartData, 15, 512) ?>;
    const eventBreakdown = <?php echo json_encode($eventBreakdown, 15, 512) ?>;
    const allTotals = <?php echo json_encode($allTotals, 15, 512) ?>;
    const ctx = document.getElementById('eventCollectionsChart');
    if (ctx) {
        let viewMode = 'count'; // 'count' or 'amount'
        const shadowPlugin = {
            id: 'shadow',
            beforeDatasetsDraw(chart) {
                const { ctx } = chart;
                ctx.save();
                ctx.shadowColor = 'rgba(0, 0, 0, 0.18)';
                ctx.shadowBlur = 14;
                ctx.shadowOffsetX = 4;
                ctx.shadowOffsetY = 6;
            },
            afterDatasetsDraw(chart) {
                chart.ctx.restore();
            }
        };

        const getTotalsFor = (value) => {
            if (value === 'all') return allTotals;
            return eventBreakdown.find((r) => String(r.id) === String(value))
                || { cash: 0, cover: 0, gift: 0, cash_count: 0, cover_count: 0, gift_count: 0 };
        };

        const buildDatasets = (row) => ({
            counts:  [row.cash_count  ?? 0, row.cover_count  ?? 0, row.gift_count  ?? 0],
            amounts: [row.cash        ?? 0, row.cover        ?? 0, row.gift        ?? 0],
        });

        const initial = getTotalsFor('all');
        const ds = buildDatasets(initial);

        const getThemeColor = () => document.documentElement.classList.contains('dark') ? '#cbd5e1' : '#6b7280';

        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Cash', 'Cover', 'Gift'],
                datasets: [{
                    data:   ds.counts,
                    amounts: ds.amounts,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.9)',
                        'rgba(16, 185, 129, 0.9)',
                        'rgba(245, 158, 11, 0.9)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 16, usePointStyle: true, pointStyle: 'circle', font: { size: 12 }, color: getThemeColor() }
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const count  = context.raw ?? 0;
                                const amount = context.dataset.amounts?.[context.dataIndex] ?? 0;
                                const lbl    = context.label ?? '';
                                const entry  = `${count} entr${count === 1 ? 'y' : 'ies'}`;
                                return ` ${lbl}: ${entry} · ₹${Number(amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
                            }
                        }
                    }
                }
            },
            plugins: [shadowPlugin]
        });

        window.eventCollectionsChartInstance = chart;

        const selector = document.getElementById('eventChartSelect');
        const viewToggle = document.getElementById('viewToggle');

        const updateChart = () => {
            const row = getTotalsFor(selector.value);
            const ds = buildDatasets(row);
            chart.data.datasets[0].data    = (viewMode === 'count') ? ds.counts : ds.amounts;
            chart.data.datasets[0].amounts = ds.amounts;
            chart.update();
        };

        if (selector) selector.addEventListener('change', updateChart);
        if (viewToggle) {
            viewToggle.addEventListener('click', () => {
                viewMode = (viewMode === 'count') ? 'amount' : 'count';
                viewToggle.textContent = (viewMode === 'count') ? 'Count' : 'Amount';
                updateChart();
            });
        }
    }

    document.querySelectorAll('.js-copy-btn').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const target = document.querySelector(btn.dataset.copyTarget);
            if (!target) return;
            const text = target.textContent.trim();
            const label = btn.querySelector('.js-copy-label');
            const icon = btn.querySelector('i');
            const originalLabel = label ? label.textContent : '';
            const originalIconClass = icon ? icon.className : '';

            const showCopied = () => {
                if (label) label.textContent = 'Copied';
                if (icon) icon.className = 'fas fa-check text-sm';
                btn.classList.add('!bg-emerald-600', '!text-white', '!border-emerald-600');
                setTimeout(() => {
                    if (label) label.textContent = originalLabel;
                    if (icon) icon.className = originalIconClass;
                    btn.classList.remove('!bg-emerald-600', '!text-white', '!border-emerald-600');
                }, 1500);
            };

            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(text);
                } else {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                }
                showCopied();
            } catch (err) {
                console.error('Copy failed', err);
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/dashboard.blade.php ENDPATH**/ ?>