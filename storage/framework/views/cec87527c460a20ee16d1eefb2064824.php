

<?php $__env->startSection('title', 'Events'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $hasFilters = trim((string) request('search')) !== '' || trim((string) request('status')) !== '';
    $eventsStatusLabels = [
        '' => 'All events',
        'upcoming' => 'Upcoming',
        'past' => 'Past',
        'archived' => 'Archived',
    ];
    $eventsStatusValue = (string) request('status', '');
    if (! array_key_exists($eventsStatusValue, $eventsStatusLabels)) {
        $eventsStatusValue = '';
    }
?>

<div class="mb-6 sm:mb-8 flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between lg:gap-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:gap-5 min-w-0 flex-1">
        <div class="flex h-12 w-12 sm:h-14 sm:w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500/15 to-indigo-600/10 text-sky-700 ring-1 ring-sky-200/70 shadow-sm"
             aria-hidden="true">
            <i class="fas fa-calendar-day text-lg sm:text-xl"></i>
        </div>
        <div class="min-w-0">
            <h1 class="cb-page-title text-2xl sm:text-3xl leading-tight">Events</h1>
            <p class="cb-subtitle mt-1.5 max-w-xl text-sm sm:text-base leading-relaxed">Create and manage your occasions — search and filter below.</p>
        </div>
    </div>
    <?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
    <a href="<?php echo e(route('client.events.create')); ?>"
       class="cb-btn cb-btn-gold inline-flex items-center justify-center gap-2 w-full lg:w-auto shrink-0 min-h-[2.75rem] px-5 shadow-md touch-manipulation">
        <i class="fas fa-plus text-sm" aria-hidden="true"></i>
        <span>Create event</span>
    </a>
    <?php endif; ?>
</div>

<div class="cb-card relative z-10 overflow-visible border border-slate-200/80 shadow-sm rounded-2xl mb-6 sm:mb-8">
    <div class="px-4 py-3.5 sm:px-5 sm:py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50/90 via-white to-sky-50/25">
        <h2 class="text-sm font-bold text-cb-navy tracking-wide">Search &amp; filter</h2>
        <p class="text-xs sm:text-sm text-slate-600 mt-1">Find events by title or narrow by status.</p>
    </div>
    <form method="GET" action="<?php echo e(route('client.events.index')); ?>" class="p-4 sm:p-5 lg:p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-12 gap-4 xl:gap-5 xl:items-end">
            <div class="sm:col-span-2 xl:col-span-5 space-y-1.5 min-w-0">
                <label for="events-search" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Search</label>
                <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm" aria-hidden="true">
                        <i class="fas fa-search"></i>
                    </span>
                    <input id="events-search"
                           type="search"
                           name="search"
                           value="<?php echo e(request('search')); ?>"
                           placeholder="Search by title or venue…"
                           autocomplete="off"
                           class="cb-field w-full min-h-[2.75rem] pl-10 pr-3 text-base sm:text-sm">
                </div>
            </div>
            <div class="sm:col-span-1 xl:col-span-3 space-y-1.5 min-w-0">
                <span class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500" id="events-status-label-text">Status</span>
                <div class="relative z-20 isolate" id="events-status-widget">
                    <select id="events-status"
                            name="status"
                            class="sr-only"
                            tabindex="-1"
                            aria-labelledby="events-status-label-text">
                        <?php $__currentLoopData = $eventsStatusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>" <?php echo e($eventsStatusValue === $val ? 'selected' : ''); ?>><?php echo e($lbl); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <button type="button"
                            id="events-status-trigger"
                            class="events-status-trigger group mt-1.5 flex w-full items-center justify-between gap-3 min-h-[2.75rem] rounded-[var(--cb-radius)] border border-[rgba(26,54,70,0.12)] bg-[var(--cb-input-bg)] px-4 text-left text-base sm:text-sm font-semibold text-[var(--cb-navy-soft)] shadow-[inset_0_1px_0_rgba(255,255,255,0.6)] transition hover:border-[rgba(184,134,11,0.35)] hover:bg-[#faf8f4] focus:outline-none focus-visible:border-[rgba(184,134,11,0.45)] focus-visible:ring-[3px] focus-visible:ring-[rgba(184,134,11,0.2)] focus-visible:bg-white dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-700 dark:focus-visible:bg-slate-800"
                            aria-haspopup="listbox"
                            aria-expanded="false"
                            aria-controls="events-status-listbox"
                            aria-labelledby="events-status-label-text">
                        <span class="js-events-status-text min-w-0 truncate"><?php echo e($eventsStatusLabels[$eventsStatusValue]); ?></span>
                        <i class="fas fa-chevron-down inline-block shrink-0 text-[0.7rem] text-slate-500 transition-[transform,color] duration-200 group-hover:text-[var(--cb-navy)] js-events-status-chev" style="transform: rotate(0deg)" aria-hidden="true"></i>
                    </button>
                    <ul id="events-status-listbox"
                        role="listbox"
                        class="absolute left-0 right-0 top-full z-[80] mt-2 hidden max-h-60 overflow-auto rounded-xl border border-slate-200/95 bg-white py-1 shadow-xl shadow-slate-900/15 ring-1 ring-black/[0.06] dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200"
                        aria-labelledby="events-status-label-text">
                        <?php $__currentLoopData = $eventsStatusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li role="option"
                                tabindex="-1"
                                data-value="<?php echo e($val); ?>"
                                aria-selected="<?php echo e($eventsStatusValue === $val ? 'true' : 'false'); ?>"
                                class="events-status-opt">
                                <?php if($val === 'upcoming'): ?>
                                    <i class="fas fa-calendar-plus w-4 shrink-0 text-center opacity-80" aria-hidden="true"></i>
                                <?php elseif($val === 'past'): ?>
                                    <i class="fas fa-clock-rotate-left w-4 shrink-0 text-center opacity-80" aria-hidden="true"></i>
                                <?php elseif($val === 'archived'): ?>
                                    <i class="fas fa-box-archive w-4 shrink-0 text-center opacity-80" aria-hidden="true"></i>
                                <?php else: ?>
                                    <i class="fas fa-layer-group w-4 shrink-0 text-center opacity-80" aria-hidden="true"></i>
                                <?php endif; ?>
                                <span><?php echo e($lbl); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
            <div class="sm:col-span-1 xl:col-span-4 flex flex-col gap-2 sm:flex-row sm:items-end min-w-0">
                <button type="submit"
                        class="cb-btn cb-btn--navy inline-flex flex-1 items-center justify-center gap-2 min-h-[2.25rem] py-1.5 text-sm touch-manipulation shadow-sm dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-white dark:border-slate-600">
                    <i class="fas fa-search text-sm opacity-90" aria-hidden="true"></i>
                    <span>Apply</span>
                </button>
                <?php if($hasFilters): ?>
                    <a href="<?php echo e(route('client.events.index')); ?>"
                       class="cb-btn cb-btn--ghost inline-flex flex-1 items-center justify-center min-h-[2.25rem] py-1.5 text-sm touch-manipulation whitespace-nowrap">
                        Clear
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
    <?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <article class="cb-card overflow-hidden border border-slate-200/80 rounded-2xl p-5 sm:p-6 flex flex-col hover:shadow-lg hover:border-slate-200 transition-all duration-200">
            <?php if(isset($authUser) && $authUser->isFamilyMember()): ?>
                <?php if($event->user_id === $authUser->id): ?>
                    <span class="mb-3 self-start inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        <i class="fas fa-user text-[9px]"></i> My Event
                    </span>
                <?php else: ?>
                    <span class="mb-3 self-start inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-sky-100 text-sky-800 border border-sky-200">
                        <i class="fas fa-users text-[9px]"></i> Family
                    </span>
                <?php endif; ?>
            <?php endif; ?>
            <?php
                $typeName = optional($event->eventType)->name ?? ucfirst($event->event_type ?? 'Other');
                $typeSlug = optional($event->eventType)->slug ?? ($event->event_type ?? 'other');
                $typeBadge = match(true) {
                    str_contains($typeSlug, 'wedding') => ['icon' => 'fa-rings-wedding', 'style' => 'background: rgba(236, 72, 153, 0.15); color: #f472b6; border-color: rgba(236, 72, 153, 0.3);'],
                    str_contains($typeSlug, 'birthday') => ['icon' => 'fa-cake-candles', 'style' => 'background: rgba(245, 158, 11, 0.15); color: #fbbf24; border-color: rgba(245, 158, 11, 0.3);'],
                    str_contains($typeSlug, 'anniversary') => ['icon' => 'fa-heart', 'style' => 'background: rgba(225, 29, 72, 0.15); color: #fb7185; border-color: rgba(225, 29, 72, 0.3);'],
                    str_contains($typeSlug, 'ganpati') => ['icon' => 'fa-om', 'style' => 'background: linear-gradient(135deg, rgba(249, 115, 22, 0.2), rgba(234, 179, 8, 0.2)); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.4); box-shadow: 0 0 10px rgba(249, 115, 22, 0.2);'],
                    default => ['icon' => 'fa-calendar-star', 'style' => 'background: rgba(99, 102, 241, 0.15); color: #818cf8; border-color: rgba(99, 102, 241, 0.3);'],
                };
            ?>
            <span class="mb-2 self-start inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border" style="<?php echo e($typeBadge['style']); ?>">
                <i class="fas <?php echo e($typeBadge['icon']); ?> text-[10px]"></i> <?php echo e($typeName); ?>

            </span>
            <h3 class="text-lg font-bold text-cb-navy mb-2 leading-snug"><?php echo e($event->title); ?></h3>
            <p class="text-slate-600 text-sm mb-4 flex-1"><?php echo e(Str::limit($event->description, 100)); ?></p>
            <div class="space-y-2 mb-4 text-sm text-slate-600">
                <div class="flex items-center gap-2">
                    <i class="fas fa-calendar text-cb-gold w-4 shrink-0"></i>
                    <?php echo e($event->event_date->format('d/m/Y')); ?>

                </div>
                <?php if($event->event_time): ?>
                <div class="flex items-center gap-2">
                    <i class="fas fa-clock text-cb-gold w-4 shrink-0"></i>
                    <?php echo e($event->event_time->format('h:i A')); ?>

                </div>
                <?php endif; ?>
                <?php if($event->venue): ?>
                <div class="flex items-start gap-2">
                    <i class="fas fa-map-marker-alt text-cb-gold w-4 shrink-0 mt-0.5"></i>
                    <span><?php echo e($event->venue); ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php if(!$event->is_archived): ?>
            <div class="mb-4 flex flex-wrap gap-2">
                <?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
                <a href="<?php echo e(route('client.chandlas.create', ['event_id' => $event->id])); ?>"
                   class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-violet-100 text-violet-800 hover:bg-violet-200">
                    <i class="fas fa-plus mr-1.5"></i>Chandla
                </a>
                <a href="<?php echo e(route('client.chandlas.create', ['event_id' => $event->id, 'lock_cash' => 1])); ?>"
                   class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-sky-100 text-sky-800 hover:bg-sky-200">
                    <i class="fas fa-file-invoice mr-1.5"></i>Cover
                </a>
                <?php endif; ?>
                <?php if($event->hasDirectGpayQrUnlocked()): ?>
                <button type="button"
                        class="direct-gpay-open inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-900 hover:bg-emerald-200"
                        data-event-id="<?php echo e($event->id); ?>"
                        data-upi-id="<?php echo e($event->upi_id ?? ''); ?>"
                        data-save-url="<?php echo e(route('client.events.direct-gpay.upi', $event->id)); ?>"
                        data-qr-url="<?php echo e(route('client.events.direct-gpay.qr', $event->id)); ?>"
                        data-pay-url="<?php echo e(route('public.direct-gpay', $event->id)); ?>"
                        title="Direct QR for your Event to display on invitation card. User can pay using direct QR and upload screenshots.">
                    <i class="fas fa-qrcode mr-1.5"></i>Direct QR
                </button>
                <?php elseif($event->hasDirectGpayUnlockPending()): ?>
                <a href="<?php echo e(route('client.events.direct-gpay-unlock.show', $event)); ?>"
                   class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-900 hover:bg-amber-200"
                   title="Direct QR for your Event to display on invitation card. User can pay using direct QR and upload screenshots.">
                    <i class="fas fa-hourglass-half mr-1.5"></i>Direct QR — pending
                </a>
                <?php else: ?>
                <a href="<?php echo e(route('client.events.direct-gpay-unlock.show', $event)); ?>"
                   class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-900 hover:bg-emerald-200"
                   title="Direct QR for your Event to display on invitation card. User can pay using direct QR and upload screenshots.">
                    <i class="fas fa-lock-open mr-1.5"></i>Unlock Direct QR (₹<?php echo e(number_format((float) config('services.direct_gpay_unlock.amount', 400), 0)); ?>)
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <div class="flex justify-between items-center gap-3 pt-3 border-t border-slate-100 mt-auto">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold shrink-0 <?php echo e($event->is_archived ? 'bg-slate-100 text-slate-700' : 'bg-emerald-100 text-emerald-800'); ?>">
                    <?php echo e($event->is_archived ? 'Archived' : 'Active'); ?>

                </span>
                <div class="flex items-center gap-1 sm:gap-2 shrink-0">
                    <a href="<?php echo e(route('client.events.show', $event->id)); ?>" class="inline-flex min-h-[2.5rem] min-w-[2.5rem] items-center justify-center rounded-lg text-cb-gold hover:bg-amber-50 transition-colors" title="View" aria-label="View event">
                        <i class="fas fa-eye"></i>
                    </a>
                    <?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
                    <a href="<?php echo e(route('client.events.edit', $event->id)); ?>" class="inline-flex min-h-[2.5rem] min-w-[2.5rem] items-center justify-center rounded-lg text-sky-600 hover:bg-sky-50 transition-colors" title="Edit" aria-label="Edit event">
                        <i class="fas fa-edit"></i>
                    </a>
                    <?php endif; ?>
                    <?php if (\Illuminate\Support\Facades\Blade::check('canDelete')): ?>
                    <form action="<?php echo e(route('client.events.destroy', $event->id)); ?>" method="POST" class="inline-flex items-center" onsubmit="return confirm('Are you sure?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="inline-flex min-h-[2.5rem] min-w-[2.5rem] items-center justify-center rounded-lg text-red-600 hover:bg-red-50 transition-colors" title="Delete" aria-label="Delete event">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </article>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full text-center py-14 px-4 cb-card rounded-2xl border border-dashed border-slate-200 bg-slate-50/40">
            <i class="fas fa-calendar-times text-slate-300 text-5xl mb-4"></i>
            <p class="text-slate-600 text-lg">No events found</p>
            <a href="<?php echo e(route('client.events.create')); ?>" class="cb-link mt-4 inline-block">Create your first event</a>
        </div>
    <?php endif; ?>
</div>

<div class="mt-8 flex justify-center px-2 pb-2 overflow-x-auto">
    <?php echo e($events->links()); ?>

</div>

<?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
<a href="<?php echo e(route('client.events.create')); ?>" class="cb-fab" title="New event" aria-label="New event">
    <i class="fas fa-plus"></i>
</a>
<?php endif; ?>

<div id="direct-gpay-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/50" aria-hidden="true" role="dialog" aria-labelledby="direct-gpay-modal-title">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto p-5 sm:p-6">
        <div class="flex justify-between items-start gap-2 mb-4">
            <h2 id="direct-gpay-modal-title" class="text-lg font-bold text-cb-navy">Direct GPay QR</h2>
            <button type="button" class="direct-gpay-close text-slate-500 hover:text-slate-800 p-1 rounded" aria-label="Close">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <p class="text-sm text-slate-600 mb-4">Guests scan this QR to open your site, enter name, amount, village, and phone, then pay you with Google Pay. Entries are saved in the ledger as <strong>GPAY GPAY</strong> after they confirm payment.</p>
        <div class="space-y-3">
            <div>
                <label class="cb-label">Your UPI ID</label>
                <input type="text" id="direct-gpay-upi-input" class="cb-field w-full" placeholder="yourname@paytm" autocomplete="off">
            </div>
            <div id="direct-gpay-modal-error" class="hidden text-sm text-red-600"></div>
            <button type="button" id="direct-gpay-save-upi" class="cb-btn cb-btn-navy w-full justify-center">
                <i class="fas fa-save mr-2"></i>Save UPI &amp; show QR
            </button>
            <div id="direct-gpay-qr-wrap" class="hidden text-center pt-2 border-t border-slate-100">
                <p class="text-xs text-slate-500 mb-2">QR links to your payment page</p>
                <img id="direct-gpay-qr-img" src="" alt="QR code" class="mx-auto max-w-[220px] rounded-lg border border-slate-200">
                <div class="flex flex-col sm:flex-row gap-2 mt-3 justify-center">
                    <a id="direct-gpay-download-qr" href="#" download="chandla-direct-gpay-qr.svg" class="cb-btn cb-btn-gold text-sm justify-center">Download QR</a>
                    <button type="button" id="direct-gpay-copy-link" class="cb-btn cb-btn-navy text-sm justify-center">
                        <i class="fas fa-link mr-2"></i>Copy page link
                    </button>
                </div>
                <p id="direct-gpay-pay-url" class="text-xs text-slate-400 mt-2 break-all"></p>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    const modal = document.getElementById('direct-gpay-modal');
    const upiInput = document.getElementById('direct-gpay-upi-input');
    const saveBtn = document.getElementById('direct-gpay-save-upi');
    const errEl = document.getElementById('direct-gpay-modal-error');
    const qrWrap = document.getElementById('direct-gpay-qr-wrap');
    const qrImg = document.getElementById('direct-gpay-qr-img');
    const dlQr = document.getElementById('direct-gpay-download-qr');
    const copyBtn = document.getElementById('direct-gpay-copy-link');
    const payUrlEl = document.getElementById('direct-gpay-pay-url');
    const csrf = <?php echo json_encode(csrf_token(), 15, 512) ?>;
    let state = { saveUrl: '', qrUrl: '', payUrl: '' };

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
        errEl.classList.add('hidden');
        errEl.textContent = '';
    }
    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');
    }

    document.querySelectorAll('.direct-gpay-open').forEach(function (btn) {
        btn.addEventListener('click', function () {
            state.saveUrl = btn.getAttribute('data-save-url') || '';
            state.qrUrl = btn.getAttribute('data-qr-url') || '';
            state.payUrl = btn.getAttribute('data-pay-url') || '';
            upiInput.value = btn.getAttribute('data-upi-id') || '';
            qrWrap.classList.add('hidden');
            if ((upiInput.value || '').trim().length > 0) {
                showQr();
            }
            payUrlEl.textContent = state.payUrl;
            openModal();
        });
    });

    modal.querySelectorAll('.direct-gpay-close').forEach(function (b) {
        b.addEventListener('click', closeModal);
    });
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    function showQr() {
        const bust = '?t=' + Date.now();
        qrImg.src = state.qrUrl + bust;
        dlQr.href = state.qrUrl + bust;
        qrWrap.classList.remove('hidden');
    }

    saveBtn.addEventListener('click', function () {
        errEl.classList.add('hidden');
        errEl.textContent = '';
        const upi = (upiInput.value || '').trim();
        if (!upi) {
            errEl.textContent = 'Please enter your UPI ID.';
            errEl.classList.remove('hidden');
            return;
        }
        saveBtn.disabled = true;
        fetch(state.saveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ upi_id: upi }),
        })
            .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, status: r.status, body: j }; }); })
            .then(function (res) {
                saveBtn.disabled = false;
                if (!res.ok) {
                    var msg = (res.body && res.body.message) ? res.body.message : 'Could not save UPI.';
                    if (res.body && res.body.errors) {
                        var first = Object.values(res.body.errors)[0];
                        if (Array.isArray(first) && first[0]) msg = first[0];
                    }
                    errEl.textContent = msg;
                    errEl.classList.remove('hidden');
                    return;
                }
                showQr();
            })
            .catch(function () {
                saveBtn.disabled = false;
                errEl.textContent = 'Network error. Try again.';
                errEl.classList.remove('hidden');
            });
    });

    copyBtn.addEventListener('click', function () {
        if (!state.payUrl) return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(state.payUrl).then(function () {
                copyBtn.replaceChildren();
                copyBtn.appendChild(document.createTextNode('Copied!'));
                setTimeout(function () {
                    copyBtn.innerHTML = '<i class="fas fa-link mr-2"></i>Copy page link';
                }, 2000);
            });
        }
    });
})();
(function () {
    var wrap = document.getElementById('events-status-widget');
    if (!wrap) return;
    var sel = document.getElementById('events-status');
    var btn = document.getElementById('events-status-trigger');
    var list = document.getElementById('events-status-listbox');
    if (!sel || !btn || !list) return;
    var textEl = btn.querySelector('.js-events-status-text');
    var chev = btn.querySelector('.js-events-status-chev');
    var opts = list.querySelectorAll('.events-status-opt');

    function openState() {
        return !list.classList.contains('hidden');
    }
    function setOpen(open) {
        list.classList.toggle('hidden', !open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (chev) {
            chev.style.transform = open ? 'rotate(180deg)' : 'rotate(0deg)';
        }
    }
    function syncAriaSelected() {
        var val = sel.value;
        opts.forEach(function (li) {
            var v = li.getAttribute('data-value');
            if (v === null) {
                v = '';
            }
            li.setAttribute('aria-selected', v === val ? 'true' : 'false');
        });
    }
    function readLabel() {
        var opt = sel.options[sel.selectedIndex];
        return opt ? String(opt.textContent).trim() : '';
    }

    btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        setOpen(!openState());
    });
    list.addEventListener('click', function (e) {
        var li = e.target.closest('.events-status-opt');
        if (!li) return;
        e.stopPropagation();
        var v = li.getAttribute('data-value');
        if (v === null) v = '';
        sel.value = v;
        if (textEl) textEl.textContent = readLabel();
        syncAriaSelected();
        setOpen(false);
    });
    document.addEventListener('click', function () {
        setOpen(false);
    });
    wrap.addEventListener('click', function (e) {
        e.stopPropagation();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && openState()) {
            setOpen(false);
        }
    });

    syncAriaSelected();

    if (chev && !openState()) {
        chev.style.transform = 'rotate(0deg)';
    }
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/events/index.blade.php ENDPATH**/ ?>