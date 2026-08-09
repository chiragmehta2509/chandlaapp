

<?php $__env->startSection('title', 'Contact us'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $supportEmail = trim((string) config('chandlabook.support_email', ''));
    if ($supportEmail === '') {
        $supportEmail = trim((string) config('mail.from.address', ''));
    }
    $supportPhone = trim((string) config('chandlabook.support_phone', ''));
    $phoneDigits = $supportPhone !== '' ? preg_replace('/\D/', '', $supportPhone) : '';
    $telUri = $phoneDigits !== '' ? 'tel:+' . $phoneDigits : '#';
    $hasContact = true;
?>

<div class="space-y-8 sm:space-y-10 pb-8">
    
    <div class="cb-card cb-card--hero relative overflow-hidden p-6 sm:p-8 lg:p-10">
        <div class="pointer-events-none absolute -right-20 -top-24 h-64 w-64 rounded-full bg-sky-400/20 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-28 -left-12 h-56 w-56 rounded-full bg-amber-400/18 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute top-1/2 left-1/2 h-40 w-[110%] max-w-lg -translate-x-1/2 -translate-y-1/2 bg-[radial-gradient(ellipse_at_center,rgba(129,140,248,0.15),transparent_70%)]" aria-hidden="true"></div>

        <div class="relative grid gap-8 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end lg:gap-10">
            <div class="min-w-0">
                <p class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5 text-[0.65rem] font-bold uppercase tracking-[0.18em] text-indigo-100 ring-1 ring-white/15 backdrop-blur-sm sm:text-xs">
                    <i class="fa-solid fa-headset text-amber-300" aria-hidden="true"></i>
                    We're here to help
                </p>
                <h1 class="text-[1.65rem] font-bold leading-[1.15] tracking-tight text-white sm:text-3xl lg:text-4xl">
                    Talk to Chandla Book
                </h1>
                <p class="mt-4 max-w-xl text-sm leading-relaxed text-indigo-100/88 sm:text-base">
                    Billing, Razorpay packs, ledger questions, or invites — reach us on phone or email and we’ll point you in the right direction.
                </p>
                <div class="mt-6 flex flex-wrap gap-2.5">
                    <span class="inline-flex items-center gap-2 rounded-xl bg-emerald-500/15 px-3 py-2 text-xs font-semibold text-emerald-50 ring-1 ring-emerald-400/35 backdrop-blur-[2px]">
                        <i class="fa-solid fa-clock text-emerald-300" aria-hidden="true"></i>
                        Typical reply within a business day
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-3 py-2 text-xs font-semibold text-white/95 ring-1 ring-white/15 backdrop-blur-[2px]">
                        <i class="fa-solid fa-receipt text-amber-300" aria-hidden="true"></i>
                        Mention your account email when you write
                    </span>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row lg:flex-col lg:min-w-[13rem]">
                <a href="<?php echo e(route('client.faq')); ?>" class="cb-btn cb-btn--gold cb-btn--sm justify-center shadow-lg shadow-amber-900/30 lg:py-3">
                    <i class="fa-regular fa-circle-question" aria-hidden="true"></i>
                    Browse FAQ
                </a>
                <a href="<?php echo e(route('client.plans')); ?>" class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/25 bg-white/5 px-4 py-2.5 text-sm font-semibold text-white backdrop-blur-sm transition hover:bg-white/12 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-300/60">
                    <i class="fa-solid fa-tags text-amber-200/90" aria-hidden="true"></i>
                    Plans &amp; pricing
                </a>
            </div>
        </div>
    </div>    
    <div class="relative mx-auto max-w-4xl">
        <div class="pointer-events-none absolute inset-x-0 -top-8 mx-auto h-40 max-w-xl rounded-full bg-gradient-to-r from-indigo-400/15 via-amber-300/12 to-teal-400/15 blur-3xl" aria-hidden="true"></div>
        <?php if($hasContact): ?>
            <div class="rounded-2xl border border-white/10 bg-gradient-to-br from-indigo-950 via-purple-900 to-slate-950 p-6 sm:p-8 shadow-2xl shadow-black/35 text-white">
                <p class="text-center text-xs font-bold uppercase tracking-[0.25em] text-white/60 mb-8 font-serif">Get in touch</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php if($supportPhone !== ''): ?>
                    <a href="<?php echo e($telUri); ?>" class="group flex items-center justify-between gap-4 rounded-xl border border-white/5 bg-white/[0.03] p-4 sm:p-5 transition-all duration-300 hover:bg-white/[0.08] hover:border-white/10 hover:shadow-lg hover:shadow-indigo-950/20">
                        <div class="flex items-center gap-4">
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg shadow-orange-950/40">
                                <i class="fa-solid fa-phone text-xl" aria-hidden="true"></i>
                            </span>
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-white/50 mb-0.5">Call us</span>
                                <span class="block text-base sm:text-lg font-bold text-white tracking-wide"><?php echo e($supportPhone); ?></span>
                                <span class="mt-1 block text-xs font-semibold text-emerald-400">Tap to dial</span>
                            </div>
                        </div>
                        <span class="text-white/20 transition-colors group-hover:text-white/60 mr-1">
                            <i class="fa-solid fa-chevron-right text-sm" aria-hidden="true"></i>
                        </span>
                    </a>
                    <?php endif; ?>

                    <?php if($supportEmail !== ''): ?>
                    <a href="mailto:<?php echo e($supportEmail); ?>" class="group flex items-center justify-between gap-4 rounded-xl border border-white/5 bg-white/[0.03] p-4 sm:p-5 transition-all duration-300 hover:bg-white/[0.08] hover:border-white/10 hover:shadow-lg hover:shadow-indigo-950/20">
                        <div class="flex items-center gap-4">
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-950/40">
                                <i class="fa-solid fa-envelope text-xl" aria-hidden="true"></i>
                            </span>
                            <div class="min-w-0">
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-white/50 mb-0.5">Email</span>
                                <span class="block text-base sm:text-lg font-bold text-white tracking-wide truncate max-w-[180px] sm:max-w-xs"><?php echo e($supportEmail); ?></span>
                                <span class="mt-1 block text-xs font-semibold text-emerald-400">Opens your mail app</span>
                            </div>
                        </div>
                        <span class="text-white/20 transition-colors group-hover:text-white/60 mr-1">
                            <i class="fa-solid fa-chevron-right text-sm" aria-hidden="true"></i>
                        </span>
                    </a>
                    <?php endif; ?>

                    <a href="tel:+918200067737" class="group flex items-center justify-between gap-4 rounded-xl border border-white/5 bg-white/[0.03] p-4 sm:p-5 transition-all duration-300 hover:bg-white/[0.08] hover:border-white/10 hover:shadow-lg hover:shadow-indigo-950/20">
                        <div class="flex items-center gap-4">
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg shadow-orange-950/40">
                                <i class="fa-solid fa-phone text-xl" aria-hidden="true"></i>
                            </span>
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-white/50 mb-0.5">Call us (Alternative)</span>
                                <span class="block text-base sm:text-lg font-bold text-white tracking-wide">+91 8200067737</span>
                                <span class="mt-1 block text-xs font-semibold text-emerald-400">Tap to dial</span>
                            </div>
                        </div>
                        <span class="text-white/20 transition-colors group-hover:text-white/60 mr-1">
                            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                        </span>
                    </a>

                    <a href="mailto:info.ksky@gmail.com" class="group flex items-center justify-between gap-4 rounded-xl border border-white/5 bg-white/[0.03] p-4 sm:p-5 transition-all duration-300 hover:bg-white/[0.08] hover:border-white/10 hover:shadow-lg hover:shadow-indigo-950/20">
                        <div class="flex items-center gap-4">
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-950/40">
                                <i class="fa-solid fa-envelope text-xl" aria-hidden="true"></i>
                            </span>
                            <div class="min-w-0">
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-white/50 mb-0.5">Email (Alternative)</span>
                                <span class="block text-base sm:text-lg font-bold text-white tracking-wide truncate max-w-[180px] sm:max-w-xs">info.ksky@gmail.com</span>
                                <span class="mt-1 block text-xs font-semibold text-emerald-400">Opens your mail app</span>
                            </div>
                        </div>
                        <span class="text-white/20 transition-colors group-hover:text-white/60 mr-1">
                            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                        </span>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50/50 px-4 py-6 text-center">
                <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                    <i class="fa-solid fa-screwdriver-wrench text-lg" aria-hidden="true"></i>
                </span>
                <p class="font-semibold text-amber-950">Contact details not configured</p>
                <p class="mt-2 text-sm text-amber-900/85 leading-relaxed max-w-md mx-auto">
                    Add <span class="font-mono text-xs bg-white/70 px-1.5 py-0.5 rounded">CHANDLABOOK_SUPPORT_EMAIL</span> and/or phone via <span class="font-mono text-xs bg-white/70 px-1.5 py-0.5 rounded">CHANDLABOOK_SUPPORT_PHONE</span> in your environment (see <span class="font-mono text-xs">config/chandlabook.php</span>).
                </p>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="cb-card relative overflow-hidden border border-[var(--cb-border)] p-5 sm:p-6 lg:flex lg:items-center lg:justify-between lg:gap-8 mt-8">
        <div class="pointer-events-none absolute -right-12 top-0 h-32 w-32 rounded-full bg-indigo-500/10 blur-2xl" aria-hidden="true"></div>
        <div class="relative min-w-0">
            <p class="text-base font-bold text-[var(--cb-navy)]">Back to your dashboard</p>
            <p class="mt-1 text-sm text-slate-600">Continue managing events, ledger, and invitations.</p>
        </div>
        <div class="relative mt-4 flex flex-wrap gap-2 lg:mt-0 lg:shrink-0">
            <a href="<?php echo e(route('client.dashboard')); ?>" class="cb-btn cb-btn--navy cb-btn--sm">
                <i class="fa-solid fa-house" aria-hidden="true"></i>
                Home
            </a>
            <a href="<?php echo e(route('client.events.index')); ?>" class="cb-btn cb-btn--ghost cb-btn--sm">
                Events
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/contact.blade.php ENDPATH**/ ?>