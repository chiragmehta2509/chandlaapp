<?php $__env->startSection('title', 'Invitation card'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $d = $invitation->data ?? [];
    $cardDisplay = \App\Support\MarriageInvitationCard::mergeUserDataWithDemoDefaults($d);
    $couple = trim(($cardDisplay['groom_name'] ?? '') . ' & ' . ($cardDisplay['bride_name'] ?? ''));
    $couplePath = !empty($d['couple_image']) && is_string($d['couple_image']) ? $d['couple_image'] : null;
    if ($couplePath) {
        $couplePath = ltrim(str_replace('\\', '/', $couplePath), '/');
        if (str_starts_with($couplePath, 'storage/')) {
            $couplePath = substr($couplePath, strlen('storage/'));
        }
    } else {
        $couplePath = null;
    }
    $disk = \Illuminate\Support\Facades\Storage::disk('public');
    $coupleImageOk = $couplePath && $disk->exists($couplePath);
    $coupleImageUrl = $coupleImageOk ? $disk->url($couplePath) : null;

    $weddingDateFmt = '';
    if (!empty($cardDisplay['wedding_date'])) {
        try {
            $weddingDateFmt = \Carbon\Carbon::parse($cardDisplay['wedding_date'])->format('l, j F Y');
        } catch (\Throwable $e) {
            $weddingDateFmt = (string) $cardDisplay['wedding_date'];
        }
    }
    $venueLine = trim((string) ($cardDisplay['venue_name'] ?? ''));
    $templateCount = count($templates ?? []);
    $summaryLabels = [
        'groom_name' => 'Groom',
        'bride_name' => 'Bride',
        'parent_groom' => 'Parents of groom',
        'parent_bride' => 'Parents of bride',
        'wedding_date' => 'Wedding date',
        'wedding_time' => 'Time',
        'venue_name' => 'Venue',
        'venue_address' => 'Address',
        'rsvp_contact' => 'RSVP / contact',
        'tagline' => 'Tagline',
    ];
?>

<div class="max-w-6xl mx-auto">
    
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-br from-[#1a3646] via-[#243d4d] to-[#152830] text-white shadow-xl shadow-slate-900/20 mb-6 sm:mb-8">
        <div class="relative px-5 py-6 sm:px-8 sm:py-8">
            <a href="<?php echo e(route('client.marriage-invitations.index')); ?>" class="inline-flex items-center gap-2 text-sm font-medium text-amber-200/90 hover:text-amber-100 transition-colors mb-4">
                <i class="fas fa-arrow-left text-xs opacity-90" aria-hidden="true"></i>
                Back to invitations
            </a>
            <p class="text-amber-200/80 text-xs font-semibold uppercase tracking-[0.2em] mb-2">Marriage invitation</p>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-white font-serif leading-tight">
                <?php echo e($couple !== '' ? $couple : 'Your card'); ?>

            </h1>
            <?php if($weddingDateFmt !== '' || $venueLine !== ''): ?>
                <p class="mt-3 text-sm sm:text-base text-slate-200/95 max-w-2xl leading-relaxed">
                    <?php if($weddingDateFmt !== ''): ?>
                        <span class="inline-flex items-center gap-2">
                            <i class="fas fa-calendar-day text-amber-300/90 text-sm" aria-hidden="true"></i>
                            <?php echo e($weddingDateFmt); ?>

                        </span>
                    <?php endif; ?>
                    <?php if($weddingDateFmt !== '' && $venueLine !== ''): ?>
                        <span class="mx-2 text-slate-400 hidden sm:inline">·</span>
                        <br class="sm:hidden">
                    <?php endif; ?>
                    <?php if($venueLine !== ''): ?>
                        <span class="inline-flex items-center gap-2 mt-1 sm:mt-0">
                            <i class="fas fa-location-dot text-amber-300/90 text-sm" aria-hidden="true"></i>
                            <?php echo e($venueLine); ?>

                        </span>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
            <?php if(!empty($meta['subtitle'])): ?>
                <p class="mt-4 text-sm text-slate-300/90 max-w-xl leading-relaxed border-t border-white/10 pt-4"><?php echo e($meta['subtitle']); ?></p>
            <?php endif; ?>

            <div class="mt-6 flex flex-wrap gap-3">
                <?php if(!$invitation->exportsUnlockedForUser() && !$invitation->hasPendingPayment()): ?>
                    <a href="<?php echo e(route('client.marriage-invitations.payment', $invitation->id)); ?>" data-loader="payment" class="cb-btn cb-btn--gold cb-btn--sm sm:!py-3 sm:!px-5 shadow-lg">
                        <i class="fas fa-lock-open" aria-hidden="true"></i>
                        Pay ₹<?php echo e(number_format((float) config('marriage_invitations.amount', 300), 0)); ?> with Razorpay
                    </a>
                <?php endif; ?>
                <?php if(!$invitation->isUnlocked()): ?>
                    <a href="<?php echo e(route('client.marriage-invitations.edit', $invitation->id)); ?>" class="cb-btn cb-btn--ghost cb-btn--sm sm:!py-3 sm:!px-5 !text-white !border-white/30 hover:!bg-white/10">
                        <i class="fas fa-pen-to-square" aria-hidden="true"></i>
                        Edit details
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="mb-6 sm:mb-8 rounded-2xl border overflow-hidden
        <?php if($invitation->isUnlocked()): ?> border-emerald-200 bg-gradient-to-r from-emerald-50/90 to-white
        <?php elseif($invitation->hasPendingPayment()): ?> border-amber-200 bg-gradient-to-r from-amber-50/90 to-white
        <?php elseif($invitation->exportsUnlockedForUser()): ?> border-sky-200 bg-gradient-to-r from-sky-50/80 to-white
        <?php else: ?> border-slate-200 bg-white <?php endif; ?>" id="mi-status-banner">
        <div class="flex gap-4 p-4 sm:p-5">
            <div class="shrink-0 w-11 h-11 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center text-lg
                <?php if($invitation->isUnlocked()): ?> bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400
                <?php elseif($invitation->hasPendingPayment()): ?> bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400
                <?php elseif($invitation->exportsUnlockedForUser()): ?> bg-sky-100 dark:bg-sky-900/50 text-sky-700 dark:text-sky-400
                <?php else: ?> bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 <?php endif; ?>" aria-hidden="true">
                <?php if($invitation->isUnlocked()): ?>
                    <i class="fas fa-circle-check"></i>
                <?php elseif($invitation->hasPendingPayment()): ?>
                    <i class="fas fa-hourglass-half"></i>
                <?php elseif($invitation->exportsUnlockedForUser()): ?>
                    <i class="fas fa-unlock"></i>
                <?php else: ?>
                    <i class="fas fa-lock"></i>
                <?php endif; ?>
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Status</h2>
                <?php if($invitation->isUnlocked()): ?>
                    <p class="text-emerald-900 dark:text-emerald-300 font-semibold">Payment verified — you can save and share your card.</p>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">Paid <?php echo e($invitation->paid_at?->format('d/m/Y g:i A')); ?></p>
                <?php elseif($invitation->hasPendingPayment()): ?>
                    <p class="text-amber-900 dark:text-amber-300 font-semibold">UPI submitted — waiting for admin verification.</p>
                    <?php if($invitation->upiTransaction): ?>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">Reference: <span class="font-mono"><?php echo e($invitation->upiTransaction->transaction_id); ?></span></p>
                    <?php endif; ?>
                <?php elseif($invitation->exportsUnlockedForUser()): ?>
                    <?php if(auth()->user()->hasCelebrationPackAccess()): ?>
                        <p class="text-indigo-950 dark:text-indigo-300 font-semibold">Celebration pack — downloads are enabled for this card.</p>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">Your pack unlocks every invitation layout.</p>
                    <?php else: ?>
                        <p class="text-sky-900 dark:text-sky-300 font-semibold">Downloads enabled for your account.</p>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">Bypass email list — same checks as paid verification.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-slate-800 dark:text-slate-200 font-semibold">Unlock print &amp; PNG downloads</p>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Pay ₹<?php echo e(number_format((float) $invitation->amount, 0)); ?> (same UPI as your other plans). Your wording and photo stay saved.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="<?php if($invitation->exportsUnlockedForUser()): ?> lg:grid lg:grid-cols-12 lg:gap-8 lg:items-start <?php endif; ?>">
        <?php if($invitation->exportsUnlockedForUser()): ?>
            <div class="lg:col-span-7 space-y-6 mb-6 lg:mb-0">
                <div class="rounded-2xl border border-slate-200/90 bg-white dark:bg-slate-800 dark:border-slate-700 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-slate-100 dark:border-slate-700 bg-gradient-to-r from-slate-50/80 to-white dark:from-slate-800 dark:to-slate-800/80">
                        <h3 class="text-lg font-bold text-cb-navy dark:text-white flex items-center gap-2">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-amber-800 text-sm"><i class="fas fa-print" aria-hidden="true"></i></span>
                            View or print
                        </h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-2 leading-relaxed"><?php echo e($templateCount); ?> styles — same details on every card. Opens in a new tab; use your browser’s print or share.</p>
                    </div>
                    <div class="p-4 sm:p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <?php
                                $firstKey = array_key_first($templates ?? []);
                            ?>
                            <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="template-card rounded-xl border p-4 flex flex-col gap-3 transition-all duration-200 cursor-pointer <?php echo e($key === $firstKey ? 'border-amber-500 dark:border-amber-600 bg-amber-50/40 dark:bg-amber-900/20 ring-2 ring-amber-400/30' : 'border-slate-200 dark:border-slate-600 bg-slate-50/50 dark:bg-slate-700/50 hover:border-amber-300/60 hover:bg-amber-50/20 dark:hover:border-amber-600/60 dark:hover:bg-amber-900/20'); ?>" data-layout="<?php echo e($key); ?>" onclick="updateLivePreview('<?php echo e($key); ?>', '<?php echo e(addslashes($t['name'])); ?>')">
                                    <div class="flex items-start gap-3 min-w-0">
                                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-sm font-bold <?php echo e($t['badge_class'] ?? 'bg-slate-200 text-slate-800'); ?>"><?php echo e($t['badge'] ?? substr($key, 0, 1)); ?></span>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-semibold text-cb-navy dark:text-white text-sm leading-snug"><?php echo e($t['name']); ?></p>
                                            <p class="text-xs text-slate-500 mt-1 leading-relaxed line-clamp-2"><?php echo e($t['description'] ?? ''); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="<?php echo e($invitation->exportsUnlockedForUser() ? 'lg:col-span-5' : 'max-w-2xl mx-auto'); ?> space-y-6 <?php if(!$invitation->exportsUnlockedForUser()): ?> mb-8 <?php endif; ?>">
            <?php
                $firstKey = array_key_first($templates);
                $firstName = $templates[$firstKey]['name'] ?? 'Style';
            ?>
            <div class="rounded-2xl border border-slate-200/90 bg-white dark:bg-slate-800 dark:border-slate-700 shadow-sm overflow-hidden" id="live-preview-container">
                <div class="px-5 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/80">
                    <h2 class="text-sm font-bold text-cb-navy dark:text-white uppercase tracking-wide">Live Preview: <span id="live-preview-title"><?php echo e($firstName); ?></span></h2>
                </div>
                <div class="p-0 bg-slate-100 relative">
                    <div class="w-full aspect-[9/16] max-h-[600px]">
                        <iframe id="live-preview-iframe" src="<?php echo e(route('client.marriage-invitations.template-demo', ['layout' => $firstKey, 'invitation_id' => $invitation->id])); ?>" class="w-full h-full border-0" title="Template Preview" loading="lazy"></iframe>
                    </div>
                </div>
                <?php if($invitation->exportsUnlockedForUser()): ?>
                    <div class="p-4 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex gap-3">
                        <a id="live-preview-png-btn" href="<?php echo e(route('client.marriage-invitations.export.png', $invitation->id)); ?>?layout=<?php echo e($firstKey); ?>" target="_blank" rel="noopener" class="cb-btn cb-btn--gold flex-1 justify-center shadow-sm">
                            <i class="fas fa-download text-xs" aria-hidden="true"></i> PNG
                        </a>
                        <a id="live-preview-open-btn" href="<?php echo e(route('client.marriage-invitations.download', $invitation->id)); ?>?layout=<?php echo e($firstKey); ?>" target="_blank" rel="noopener" class="cb-btn cb-btn--navy flex-1 justify-center shadow-sm">
                            <i class="fas fa-external-link-alt text-xs" aria-hidden="true"></i> Open
                        </a>
                    </div>
                <?php else: ?>
                    <div class="p-4 border-t border-slate-100 dark:border-slate-700 bg-amber-50/50 dark:bg-amber-900/10 flex flex-col items-center justify-center gap-2 text-center">
                        <a href="<?php echo e(route('client.marriage-invitations.payment', $invitation->id)); ?>" data-loader="payment" class="cb-btn cb-btn--gold w-full justify-center shadow-sm py-2.5 text-sm">
                            <i class="fas fa-lock-open text-xs" aria-hidden="true"></i> Pay ₹<?php echo e(number_format((float) config('marriage_invitations.amount', 300), 0)); ?> to unlock downloads
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <?php
        $hasSchedule = !empty($cardDisplay['schedule_events']) && is_array($cardDisplay['schedule_events']) && count(array_filter($cardDisplay['schedule_events'], function($ev) { return !empty($ev['title']); })) > 0;
    ?>

    <?php if($hasSchedule): ?>
        <div class="mt-6 <?php echo e($invitation->exportsUnlockedForUser() ? 'w-full' : 'max-w-2xl mx-auto'); ?>">
            <div class="rounded-2xl border border-slate-200/90 bg-white dark:bg-slate-800 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/80 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-cb-navy dark:text-white uppercase tracking-wide flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-400 text-xs"><i class="fas fa-calendar-alt"></i></span>
                        Event Schedule
                    </h2>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php $__currentLoopData = $cardDisplay['schedule_events']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(empty($ev['title'])): ?> <?php continue; ?> <?php endif; ?>
                            <?php
                                $sd = '';
                                if (!empty($ev['date'])) {
                                    try { $sd = \Carbon\Carbon::parse($ev['date'])->format('d/m/Y'); } catch (\Throwable $e) { $sd = (string) $ev['date']; }
                                }
                                $st = trim((string) ($ev['time'] ?? ''));
                            ?>
                            <div class="flex items-center gap-4 p-4 rounded-xl border border-slate-100 dark:border-slate-700 bg-slate-50/30 dark:bg-slate-900/20 hover:border-amber-200/80 transition-colors">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 text-base">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-bold text-cb-navy dark:text-white text-base truncate"><?php echo e($ev['title']); ?></h4>
                                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5 flex items-center gap-1.5">
                                        <i class="fas fa-calendar-alt text-xs opacity-70"></i>
                                        <span><?php echo e($sd); ?><?php echo e($st !== '' ? ($sd !== '' ? ' · ' : '') . $st : ''); ?></span>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    
    <?php if($invitation->exportsUnlockedForUser()): ?>
    <div class="mt-6 cb-card p-5 sm:p-6 shadow-sm" id="mi-video-tips">
        <h3 class="text-base font-bold text-cb-navy dark:text-white flex items-center gap-2 mb-3">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-violet-600 text-white text-sm" aria-hidden="true"><i class="fas fa-video"></i></span>
            Video tips
        </h3>
        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed text-justify"><strong>One share-ready video</strong> uses our common story layout (9:16) filled from <strong>your invitation details</strong> — names, photo, date, and venue. The file is about <strong><?php echo e((int) config('marriage_invitations.video_export_duration_sec', 30)); ?>&nbsp;seconds</strong> (usually <strong>.webm</strong>, sometimes <strong>.mp4</strong> in Safari) with a slow, smooth zoom. Encoding can take a minute; keep the tab open. Use a recent <strong>desktop</strong> browser (Chrome, Edge, or Firefox). If download fails, use <strong>PNG</strong> or <strong>Open</strong> and screen-record.</p>
        <a href="<?php echo e(route('client.marriage-invitations.export.video', $invitation->id)); ?>" target="_blank" rel="noopener" class="cb-btn cb-btn--gold w-full justify-center mt-5 py-3 text-base shadow-md">
            <i class="fas fa-download" aria-hidden="true"></i>
            Download invitation video
        </a>
    </div>
    <?php endif; ?>

    <div class="mt-8 rounded-2xl border border-slate-200/90 dark:border-slate-700 bg-gradient-to-br from-white to-slate-50 dark:from-slate-800 dark:to-slate-800/80 shadow-sm overflow-hidden w-full relative">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-amber-300 via-amber-400 to-amber-500"></div>
        <div class="px-6 py-5 border-b border-slate-100/80 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-cb-navy dark:text-white flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 text-xs"><i class="fas fa-list-ul"></i></span>
                    Invitation Details
                </h2>
                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Reflects what appears on your cards — blank fields use sample wording until you add your own in Edit.</p>
            </div>
            <a href="<?php echo e(route('client.marriage-invitations.edit', $invitation->id)); ?>" class="cb-btn cb-btn--ghost cb-btn--sm self-start sm:self-auto border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 shadow-sm">
                <i class="fas fa-pen text-xs" aria-hidden="true"></i> Edit Details
            </a>
        </div>
        <dl class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5 text-sm bg-slate-50/50 dark:bg-slate-900/20">
            <?php $__currentLoopData = $cardDisplay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($k === 'couple_image' || $k === 'schedule_events' || $k === 'demo_couple_image_url'): ?> <?php continue; ?> <?php endif; ?>
                <?php if($v === null || $v === ''): ?> <?php continue; ?> <?php endif; ?>
                <?php
                    $displayVal = is_array($v) ? json_encode($v) : $v;
                    if ($k === 'wedding_date' && $displayVal !== '') {
                        try {
                            $displayVal = \Carbon\Carbon::parse($displayVal)->format('j M Y');
                        } catch (\Throwable $e) { /* keep raw */ }
                    }
                    if ($k === 'wedding_time' && $displayVal !== '') {
                        $displayVal = \App\Support\MarriageInvitationCard::formatWeddingTimeForDisplay((string) $displayVal);
                    }
                ?>
                <div class="bg-white rounded-xl p-4 border border-slate-200/60 dark:bg-slate-800 dark:border-slate-600 shadow-sm hover:shadow-md hover:border-amber-200/60 dark:hover:border-amber-700/60 transition-all duration-200">
                    <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                        <i class="fas fa-angle-right text-amber-500/50 text-[10px]"></i>
                        <?php echo e($summaryLabels[$k] ?? str_replace('_', ' ', $k)); ?>

                    </dt>
                    <dd class="text-cb-navy dark:text-white font-medium text-[15px] whitespace-pre-line leading-relaxed"><?php echo e($displayVal); ?></dd>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </dl>
    </div>

    <?php if(!$invitation->exportsUnlockedForUser()): ?>
        <?php if($invitation->hasPendingPayment()): ?>
            <div class="rounded-2xl border border-amber-200/80 dark:border-amber-800/60 bg-amber-50/40 dark:bg-amber-900/20 p-8 text-center">
                <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-400 text-xl mb-4" aria-hidden="true">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <p class="text-cb-navy dark:text-white font-semibold">Templates appear after verification</p>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-2 max-w-md mx-auto">We’re confirming your UPI payment. This page will show all <strong><?php echo e($templateCount); ?> styles</strong> with Open &amp; PNG as soon as an admin approves it.</p>
            </div>
        <?php else: ?>
            <?php
                $packPriceFmt = number_format((float) config('packs.celebration.amount_inr', 300), 0);
                $invRzp = trim((string) config('marriage_invitations.razorpay_payment_link', ''));
            ?>
            <div class="rounded-2xl border border-rose-200/90 dark:border-rose-800/60 bg-rose-50/50 dark:bg-rose-950/20 p-4 sm:p-5 mb-6" role="status">
                <p class="text-sm font-bold text-rose-950 dark:text-rose-300 flex items-center gap-2">
                    <i class="fas fa-lock" aria-hidden="true"></i> Downloads are locked until payment is confirmed
                </p>
                <p class="text-sm text-rose-900/90 dark:text-rose-300/80 mt-2 leading-relaxed">You can read your details on this page, but <strong>Open</strong>, <strong>PNG</strong>, print, and <strong>video</strong> unlock after <strong>Razorpay</strong> payment completes (usually seconds).</p>
                <?php if(true): ?>
                    <div class="mt-4 flex flex-wrap gap-2 sm:gap-3">
                        <a href="<?php echo e(route('client.marriage-invitations.payment', $invitation->id)); ?>" data-loader="payment" class="cb-btn cb-btn-gold cb-btn--sm sm:!py-3 sm:!px-5 shadow-md">
                            <i class="fas fa-lock-open text-xs" aria-hidden="true"></i>
                            Pay ₹<?php echo e(number_format((float) config('marriage_invitations.amount', 300), 0)); ?> with Razorpay
                        </a>
                    </div>
                <?php else: ?>
                    <div class="mt-4 flex flex-wrap gap-2 sm:gap-3">
                        <a href="<?php echo e(route('client.marriage-invitations.payment', $invitation->id)); ?>" data-loader="payment" class="cb-btn cb-btn-gold cb-btn--sm sm:!py-3 sm:!px-5 shadow-md">
                            <i class="fas fa-arrow-up-right-from-square text-xs" aria-hidden="true"></i>
                            Open payment page
                        </a>
                        <a href="<?php echo e(route('client.packs.celebration.pay')); ?>" data-loader="payment" class="cb-btn cb-btn-navy cb-btn--sm sm:!py-3 sm:!px-5">
                            Celebration pack (all layouts)
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="rounded-2xl border border-amber-200/80 dark:border-amber-800/60 bg-gradient-to-br from-amber-50/60 to-white dark:from-amber-900/20 dark:to-slate-800/60 p-5 sm:p-6 mb-6">
                <p class="text-sm font-bold text-amber-900 dark:text-amber-400 mb-1">Demo layouts</p>
                <p class="text-sm text-slate-700 dark:text-slate-300 mb-4 max-w-2xl">Pay the <strong>₹<?php echo e($packPriceFmt); ?> celebration pack</strong> on Razorpay to unlock everything — or <strong>₹<?php echo e(number_format((float) config('marriage_invitations.amount', 300), 0)); ?></strong> for this invitation card only (Razorpay).</p>
                <div class="flex flex-wrap gap-3">
                    <a href="<?php echo e(route('client.packs.celebration.pay')); ?>" data-loader="payment" class="cb-btn cb-btn--gold cb-btn--sm sm:!py-3 sm:!px-5 shadow-md">
                        <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i> Pay ₹<?php echo e($packPriceFmt); ?> pack
                    </a>
                    <a href="<?php echo e(route('client.marriage-invitations.payment', $invitation->id)); ?>" data-loader="payment" class="cb-btn cb-btn--navy cb-btn--sm sm:!py-3 sm:!px-5">
                        <i class="fas fa-lock-open" aria-hidden="true"></i> Pay for this card (₹<?php echo e(number_format((float) config('marriage_invitations.amount', 300), 0)); ?>)
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5 mb-6">
                <?php echo $__env->make('client.marriage-invitations.partials.demo-style-cards', ['templates' => $templates, 'invitation' => $invitation], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->startPush('scripts'); ?>
<script>
    function updateLivePreview(layoutKey, layoutName) {
        // Show loader
        if (window.cbLoader) {
            cbLoader.show('Loading preview…');
        }

        document.getElementById('live-preview-title').textContent = layoutName;
        
        const iframe = document.getElementById('live-preview-iframe');
        iframe.src = "<?php echo e(route('client.marriage-invitations.template-demo', ['layout' => 'LAYOUT_KEY', 'invitation_id' => $invitation->id])); ?>".replace('LAYOUT_KEY', layoutKey);
        
        // Hide loader when iframe loaded
        iframe.onload = function() {
            if (window.cbLoader) {
                cbLoader.hide();
            }
        };

        document.getElementById('live-preview-png-btn').href = "<?php echo e(route('client.marriage-invitations.export.png', $invitation->id)); ?>?layout=" + layoutKey;
        document.getElementById('live-preview-open-btn').href = "<?php echo e(route('client.marriage-invitations.download', $invitation->id)); ?>?layout=" + layoutKey;
        
        // Highlight active card
        document.querySelectorAll('.template-card').forEach(function(card) {
            var isDark = document.documentElement.classList.contains('dark');
            if (card.getAttribute('data-layout') === layoutKey) {
                card.className = isDark
                    ? "template-card rounded-xl border p-4 flex flex-col gap-3 transition-all duration-200 cursor-pointer border-amber-600 bg-amber-900/20 ring-2 ring-amber-400/30"
                    : "template-card rounded-xl border p-4 flex flex-col gap-3 transition-all duration-200 cursor-pointer border-amber-500 bg-amber-50/40 ring-2 ring-amber-400/30";
            } else {
                card.className = isDark
                    ? "template-card rounded-xl border p-4 flex flex-col gap-3 transition-all duration-200 cursor-pointer border-slate-600 bg-slate-700/50 hover:border-amber-600/60 hover:bg-amber-900/20"
                    : "template-card rounded-xl border p-4 flex flex-col gap-3 transition-all duration-200 cursor-pointer border-slate-200 bg-slate-50/50 hover:border-amber-300/60 hover:bg-amber-50/20";
            }
        });

        // Scroll preview container into view on mobile
        if(window.innerWidth < 1024) {
            document.getElementById('live-preview-container').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Marriage invitation show page — dark mode overrides
   These use explicit html.dark rules because the compiled Tailwind CSS
   does not include all gradient dark: variants used on this page. */

html.dark #mi-status-banner {
    background: linear-gradient(to right, rgba(15,23,42,0.9), rgba(15,23,42,0.7)) !important;
    border-color: rgba(255,255,255,0.12) !important;
}


html.dark #mi-video-tips {
    background: var(--cb-card) !important;
    border-color: rgba(139,92,246,0.25) !important;
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/marriage-invitations/show.blade.php ENDPATH**/ ?>