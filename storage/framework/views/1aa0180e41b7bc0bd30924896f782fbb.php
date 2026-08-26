<?php $__env->startSection('title', 'Pre-wedding countdown'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeHints = [
        'misty_dusk' => 'Cool dusk & serif',
        'golden_hour' => 'Warm amber glow',
        'garden_bloom' => 'Botanical colour',
        'midnight_rose' => 'Rose on dark',
        'coastal_fog' => 'Airy ocean blue',
        'saffron_edge' => 'Cream & saffron',
        'lavender_veil' => 'Soft violet',
        'cherry_romance' => 'Deep romantic red',
        'ivory_script' => 'Ivory & script',
        'modern_mono' => 'Bold minimal',
        'blush_arch' => 'Pink blush',
        'emerald_night' => 'Emerald jewel',
        'sunset_warm' => 'Sunset orange',
        'royal_plum' => 'Royal purple',
        'paper_minimal' => 'Clean paper',
        'celebration_gold' => 'Gold celebration',
    ];
?>

<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="cb-page-title">Pre-wedding countdown</h1>
        <p class="cb-subtitle max-w-3xl">Upload a different photo for each milestone — every card uses its <strong>own layout, colours, and typography</strong>. Download a high-resolution PNG for Instagram, WhatsApp, or printing.</p>
        <?php if($showDemoOnly): ?>
            <?php $pwPack = number_format((float) config('packs.celebration.amount_inr', 300), 0); ?>
            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50/80 px-4 py-3 text-sm text-amber-950">
                <p class="font-semibold mb-2">Preview mode — celebration pack not active on this account yet.</p>
                <p class="text-amber-900/90 mb-3">Below are <strong>demo thumbnails</strong> only. Pay <strong>₹<?php echo e($pwPack); ?></strong> on Razorpay with the <strong>same email or phone</strong> as your login, then refresh this page.</p>
                <a href="<?php echo e(route('client.packs.celebration.pay')); ?>" data-loader="payment" class="cb-btn cb-btn--gold cb-btn--sm inline-flex">
                    <i class="fas fa-arrow-up-right-from-square text-xs" aria-hidden="true"></i> Pay ₹<?php echo e($pwPack); ?> celebration pack
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if(!$showDemoOnly): ?>
    <div class="cb-card p-5 sm:p-6 mb-8 border border-slate-200/90 bg-white dark:bg-slate-800 dark:border-slate-700 rounded-2xl shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div>
                <h2 class="text-sm font-bold text-cb-navy dark:text-white uppercase tracking-wide mb-1">Pre-Wedding Settings</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Set your wedding date and custom text/caption to show on the cards.</p>
                <form action="<?php echo e(route('client.pre-wedding.settings')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="wedding_date" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Wedding Date (Optional)</label>
                            <input type="date" name="wedding_date" id="wedding_date" value="<?php echo e($setting->wedding_date?->format('Y-m-d')); ?>"
                                   class="w-full rounded-xl border border-slate-200 dark:border-slate-700 px-3 py-2 text-sm text-cb-navy dark:text-slate-200 bg-white dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label for="custom_text" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Custom Text (Optional)</label>
                            <input type="text" name="custom_text" id="custom_text" value="<?php echo e($setting->custom_text); ?>" placeholder="Enter custom text"
                                   class="w-full rounded-xl border border-slate-200 dark:border-slate-700 px-3 py-2 text-sm text-cb-navy dark:text-slate-200 bg-white dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="cb-btn cb-btn--navy cb-btn--sm py-2 px-4 rounded-xl">Save Settings</button>
                    </div>
                </form>
            </div>

            
            <div class="border-t md:border-t-0 md:border-l border-slate-100 pt-6 md:pt-0 md:pl-6 flex flex-col justify-between">
                <div>
                    <h2 class="text-sm font-bold text-cb-navy dark:text-white uppercase tracking-wide mb-1">Pre-Wedding Photo</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Upload one photo to apply to all countdown cards.</p>
                </div>
                <form action="<?php echo e(route('client.pre-wedding.upload')); ?>" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3 mt-auto">
                    <?php echo csrf_field(); ?>
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Photo (JPG, PNG, WebP · max 15MB)</label>
                        <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg,image/webp" required
                               class="block w-full text-xs text-slate-500 file:mr-3 file:rounded-xl file:border-0 file:bg-amber-100 file:px-3 file:py-2.5 file:text-xs file:font-semibold file:text-amber-900 focus:outline-none">
                    </div>
                    <button type="submit" class="cb-btn cb-btn--gold cb-btn--sm py-2.5 px-4 rounded-xl justify-center">
                        <i class="fas fa-cloud-arrow-up mr-1.5"></i> Save Photo
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if($errors->has('pack')): ?>
        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 text-rose-900 text-sm px-4 py-3"><?php echo e($errors->first('pack')); ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        <?php if($showDemoOnly): ?>
            <?php echo $__env->make('client.pre-wedding.partials.demo-milestone-cards', ['milestones' => $milestones, 'themeHints' => $themeHints], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php else: ?>
        <?php $__currentLoopData = $milestones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $asset = $assets->get($key);
                $hasPhoto = $asset && $asset->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($asset->image_path);
                $thumb = $hasPhoto ? \Illuminate\Support\Facades\Storage::disk('public')->url($asset->image_path) : null;
                $theme = $m['theme'] ?? '';
            ?>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 p-3 flex flex-col justify-between gap-3 shadow-sm hover:shadow-md transition-shadow relative">
                <div class="text-center min-w-0">
                    <h3 class="font-bold text-cb-navy dark:text-white text-xs truncate" title="<?php echo e($m['label']); ?>"><?php echo e($m['label']); ?></h3>
                    <p class="text-[10px] text-violet-600 font-medium mt-0.5 truncate"><?php echo e($themeHints[$theme] ?? $theme); ?></p>
                </div>

            <div class="relative rounded-xl overflow-hidden border border-slate-200 bg-slate-100 aspect-[9/16] w-full max-w-[140px] group mx-auto cb-lazy-iframe-wrap">
                    
                    <div class="cb-iframe-skeleton absolute inset-0 flex flex-col items-center justify-center gap-1.5 overflow-hidden"
                         style="background:linear-gradient(135deg,#f1f5f9 0%,#e2e8f0 50%,#f1f5f9 100%);background-size:200% 200%;animation:cb-shimmer 2s ease-in-out infinite;">
                        <div style="width:22px;height:22px;border-radius:50%;border:2.5px solid #cbd5e1;border-top-color:#94a3b8;animation:cb-spin 0.8s linear infinite;"></div>
                        <span style="font-size:9px;color:#94a3b8;font-weight:600;letter-spacing:0.04em;">Loading…</span>
                    </div>
                    <iframe
                        data-src="<?php echo e(route('client.pre-wedding.thumbnail-preview', ['milestoneKey' => $key])); ?>"
                        title="<?php echo e($m['label']); ?> preview"
                        class="pointer-events-none absolute inset-0 h-full w-full border-0 opacity-0 transition-opacity duration-500 cb-lazy-iframe"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                    
                    
                    <div class="absolute inset-0 bg-slate-950/40 md:opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center gap-2">
                        <a href="<?php echo e(route('client.pre-wedding.card', ['milestoneKey' => $key])); ?>" target="_blank" rel="noopener"
                           class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 hover:bg-white/40 text-white backdrop-blur-md transition-all hover:scale-110 shadow-md"
                           title="View card">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                        <a href="<?php echo e(route('client.pre-wedding.export.png', ['milestoneKey' => $key])); ?>" target="_blank" rel="noopener"
                           class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-500 hover:bg-amber-600 text-white transition-all hover:scale-110 shadow-md"
                           title="Download PNG">
                            <i class="fas fa-download text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
@keyframes cb-spin    { to { transform: rotate(360deg); } }
@keyframes cb-shimmer { 0%,100% { background-position:0% 50%; } 50% { background-position:100% 50%; } }
</style>
<?php $__env->stopPush(); ?>


<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    'use strict';

    var EAGER_COUNT = 5;   // load first N immediately (fills first grid row)
    var STAGGER_MS  = 350; // delay between each subsequent card

    function revealIframe(iframe, skeleton) {
        iframe.style.opacity = '1';
        if (skeleton) {
            skeleton.style.transition = 'opacity 0.4s';
            skeleton.style.opacity = '0';
            setTimeout(function () {
                if (skeleton.parentNode) skeleton.parentNode.removeChild(skeleton);
            }, 420);
        }
    }

    function loadIframe(wrap, onDone) {
        if (wrap.dataset.cbLoaded) { if (onDone) onDone(); return; }
        wrap.dataset.cbLoaded = '1';

        var iframe  = wrap.querySelector('.cb-lazy-iframe');
        var skeleton = wrap.querySelector('.cb-iframe-skeleton');
        if (!iframe || !iframe.dataset.src) { if (onDone) onDone(); return; }

        iframe.src = iframe.dataset.src;
        delete iframe.dataset.src;

        var done = false;
        function finish() {
            if (done) return;
            done = true;
            revealIframe(iframe, skeleton);
            if (onDone) onDone();
        }

        iframe.addEventListener('load',  finish, { once: true });
        iframe.addEventListener('error', finish, { once: true });
        // Fallback: if iframe never fires load, unblock the queue after 5s
        setTimeout(finish, 5000);
    }

    function loadSequentially(wraps, index) {
        if (index >= wraps.length) return;
        loadIframe(wraps[index], function () {
            setTimeout(function () {
                loadSequentially(wraps, index + 1);
            }, STAGGER_MS);
        });
    }

    function init() {
        var wraps = Array.from(document.querySelectorAll('.cb-lazy-iframe-wrap'));
        if (!wraps.length) return;

        // Eager: first row loads in parallel immediately
        wraps.slice(0, EAGER_COUNT).forEach(function (w) { loadIframe(w, null); });

        // Staggered: remaining load one-by-one automatically, no scroll needed
        var remaining = wraps.slice(EAGER_COUNT);
        if (!remaining.length) return;

        // Start the staggered chain after a short initial delay
        setTimeout(function () {
            loadSequentially(remaining, 0);
        }, 600);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/pre-wedding/index.blade.php ENDPATH**/ ?>