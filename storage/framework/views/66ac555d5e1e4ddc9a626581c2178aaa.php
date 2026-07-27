

<?php $__env->startSection('title', 'Plans & pricing'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8 sm:space-y-10 pb-8 w-full min-w-0 max-w-full overflow-x-hidden">
    
    <?php
        $user = Auth::user();
        $ownerId = $user->dataOwnerId();
        
        $activePacks = [];
        if ($user->enterprise_pack_paid_at !== null) {
            $activePacks[] = ['name' => 'Enterprise Plan', 'badge' => 'Enterprise', 'badge_class' => 'bg-zinc-800 text-amber-200 border-zinc-700', 'icon' => 'fa-building text-amber-400', 'desc' => 'Unlimited events and enterprise management controls.', 'date' => $user->enterprise_pack_paid_at];
        }
        if ($user->professional_pack_paid_at !== null) {
            $activePacks[] = ['name' => 'Professional Plan', 'badge' => 'Professional', 'badge_class' => 'bg-rose-50 text-rose-700 border-rose-200', 'icon' => 'fa-briefcase text-rose-500', 'desc' => 'Professional multi-event suite & collaborator access.', 'date' => $user->professional_pack_paid_at];
        }
        if ($user->premium_bundle_paid_at !== null) {
            $activePacks[] = ['name' => 'Premium Host Plan', 'badge' => 'Premium', 'badge_class' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'icon' => 'fa-gem text-emerald-500', 'desc' => 'Full site modules & host tools unlocked.', 'date' => $user->premium_bundle_paid_at];
        }
        if ($user->family_pack_paid_at !== null) {
            $activePacks[] = ['name' => 'Family Plan', 'badge' => 'Family', 'badge_class' => 'bg-purple-50 text-purple-700 border-purple-200', 'icon' => 'fa-shield-halved text-purple-500', 'desc' => 'Family editors and multi-device access enabled.', 'date' => $user->family_pack_paid_at];
        }
        if ($user->ledger_duo_pack_paid_at !== null) {
            $activePacks[] = ['name' => 'Host Plus / Ledger Duo', 'badge' => 'Host Plus', 'badge_class' => 'bg-sky-50 text-sky-700 border-sky-200', 'icon' => 'fa-book-open text-sky-500', 'desc' => 'Expanded event ledger and management controls.', 'date' => $user->ledger_duo_pack_paid_at];
        }
        if ($user->celebration_pack_paid_at !== null) {
            $activePacks[] = ['name' => 'Celebration Pack', 'badge' => 'Celebration', 'badge_class' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'icon' => 'fa-wand-magic-sparkles text-indigo-500', 'desc' => 'Marriage invitations & pre-wedding studio unlocked.', 'date' => $user->celebration_pack_paid_at];
        }
        if (($user->guest_pay_single_event_credits ?? 0) > 0) {
            $activePacks[] = ['name' => 'Guest Contribution Credits', 'badge' => $user->guest_pay_single_event_credits . ' Credits Left', 'badge_class' => 'bg-amber-50 text-amber-700 border-amber-200', 'icon' => 'fa-qrcode text-amber-500', 'desc' => 'Available single event upgrade credits to apply on any event.', 'date' => null];
        }

        $paidEvents = \App\Models\Event::where('user_id', $ownerId)->where('pricing_plan', '!=', 'free')->get();
        foreach($paidEvents as $ev) {
            $activePacks[] = [
                'name' => 'Event Plan: ' . $ev->title,
                'badge' => ucfirst($ev->pricing_plan),
                'badge_class' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
                'icon' => 'fa-calendar-check text-emerald-600',
                'desc' => 'Event-specific paid plan activated.',
                'date' => $ev->unlimited_purchased_at ?? $ev->updated_at
            ];
        }
    ?>

    <div class="cb-card border border-slate-200/90 bg-white p-5 sm:p-6 rounded-2xl shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Your Account Active Packages &amp; Upgrades</p>
                <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 mt-0.5">Active Subscription Summary</h2>
            </div>
            <span class="text-xs text-slate-500">
                Total Active Items: <strong class="text-slate-800"><?php echo e(count($activePacks) > 0 ? count($activePacks) : 1); ?></strong>
            </span>
        </div>

        <?php if(count($activePacks) === 0): ?>
            <div class="flex items-center gap-3 py-2">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-slate-50 border border-slate-100 text-lg shadow-sm">
                    <i class="fa-solid fa-seedling text-slate-400" aria-hidden="true"></i>
                </span>
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        Starter Plan
                        <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border-slate-200">
                            Free Tier
                        </span>
                    </h3>
                    <p class="text-xs text-slate-600 mt-0.5">You are on the free tier. Upgrade below to unlock premium features and invitations.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                <?php $__currentLoopData = $activePacks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pack): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-start gap-4 p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white border border-slate-200/80 text-base shadow-sm">
                            <i class="fa-solid <?php echo e($pack['icon']); ?>" aria-hidden="true"></i>
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-sm font-bold text-slate-900 truncate"><?php echo e($pack['name']); ?></h3>
                                <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider <?php echo e($pack['badge_class']); ?>">
                                    <?php echo e($pack['badge'] ?? 'Active'); ?>

                                </span>
                            </div>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed"><?php echo e($pack['desc']); ?></p>
                            <?php if(!empty($pack['date'])): ?>
                                <p class="text-[11px] text-slate-400 mt-1.5">Activated: <?php echo e(\Carbon\Carbon::parse($pack['date'])->format('d/m/Y')); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="mt-8">
        <?php echo $__env->make('partials.pricing-section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/plans.blade.php ENDPATH**/ ?>