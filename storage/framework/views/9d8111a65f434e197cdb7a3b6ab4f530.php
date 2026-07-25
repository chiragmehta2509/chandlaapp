<?php
    $faqDirectGpayHref = $faqDirectGpayHref ?? '#direct-gpay';
    $faqReferHref = $faqReferHref ?? '#refer';
?>
<section id="faq" class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white dark:text-white scroll-mt-20 <?php echo e(($faqFlush ?? false) ? 'border-t border-slate-200/80 dark:border-slate-700/80' : 'border border-slate-200/80 dark:border-slate-700/80 rounded-xl'); ?>" aria-labelledby="faq-heading">
    <div class="max-w-3xl mx-auto px-6 py-12">
        <h2 id="faq-heading" class="text-2xl font-bold mb-6">Common questions</h2>
        <dl class="space-y-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-5">
                <dt class="font-semibold text-slate-900 dark:text-white">What’s in the ₹<?php echo e(number_format((float) config('packs.celebration.amount_inr', 300), 0)); ?> Celebration Plan?</dt>
                <dd class="mt-2 text-slate-600 dark:text-slate-300 text-sm leading-relaxed">It bundles <strong>10 marriage invitation layouts</strong> (save PNG / print), <strong>one</strong> <strong>video</strong> export for sharing, and the <strong>pre‑wedding</strong> countdown studio with downloadable cards per milestone. It does <strong>not</strong> by itself raise your chandla entry cap past the free <strong>50</strong> — upgrade the ledger separately if you need more gift entries.</dd>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-5">
                <dt class="font-semibold text-slate-900 dark:text-white">What is the Guest Contribution (₹<?php echo e(number_format((float) config('packs.guest_pay_single.amount_inr', 400), 0)); ?>)?</dt>
                <dd class="mt-2 text-slate-600 dark:text-slate-300 text-sm leading-relaxed">One payment adds a <strong>credit</strong> on your account. In the client app, open <strong>Unlock Direct QR</strong> for <strong>one</strong> event and <strong>apply the credit</strong>. That event gets <strong>Direct GPay</strong> (your UPI / QR, pay‑to‑you link for <strong>any amount</strong>), <strong>unlimited chandla</strong> rows for that event only, and <strong>full event PDF</strong>. It does <strong>not</strong> include invitation / pre‑wedding studio.</dd>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-5">
                <dt class="font-semibold text-slate-900 dark:text-white">What is the Host Plus Plan (₹<?php echo e(number_format((float) config('packs.ledger_duo.amount_inr', 500), 0)); ?>)?</dt>
                <dd class="mt-2 text-slate-600 dark:text-slate-300 text-sm leading-relaxed">It unlocks <strong>two events</strong> on your account with <strong>unlimited chandla</strong> entries and normal PDF export — <strong>without</strong> the marriage invitation, video, or pre‑wedding studio (those stay in the Celebration / Premium Host Plans).</dd>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-5">
                <dt class="font-semibold text-slate-900 dark:text-white">What does the Premium Host Plan (₹<?php echo e(number_format((float) config('packs.premium_bundle.amount_inr', 700), 0)); ?>) include?</dt>
                <dd class="mt-2 text-slate-600 dark:text-slate-300 text-sm leading-relaxed">It includes <strong>everything in the Celebration Plan</strong>, <strong>3 events</strong> on your account, <strong>account-wide unlimited chandla</strong> (same class as Host Plus Plan for the ledger), and <strong>full event PDF</strong> workflows. You can still buy <strong>Guest Contribution</strong> credits or use the in-app Direct GPay unlock (currently ₹<?php echo e(number_format((float) config('services.direct_gpay_unlock.amount', 400), 0)); ?> per event) when you need pay-to-you QR on extra events.</dd>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-5">
                <dt class="font-semibold text-slate-900 dark:text-white">What is the Family Plan (₹<?php echo e(number_format((float) config('packs.family.amount_inr', 600), 0)); ?>)?</dt>
                <dd class="mt-2 text-slate-600 dark:text-slate-300 text-sm leading-relaxed">It upgrades your account to support <strong>shared event management</strong>. You get <strong>2 events</strong> with unlimited entries, Direct GPay support, and you can invite up to <strong>3 Family Editors</strong> with write access so multiple people can log gifts and manage the ledger simultaneously.</dd>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-5">
                <dt class="font-semibold text-slate-900 dark:text-white">What does the Professional Plan (₹<?php echo e(number_format((float) config('packs.professional.amount_inr', 999), 0)); ?>) offer?</dt>
                <dd class="mt-2 text-slate-600 dark:text-slate-300 text-sm leading-relaxed">Designed for power users, event coordinators, or very large extended families, this plan unlocks up to <strong>10 events</strong>, <strong>unlimited family editors</strong>, an advanced analytics dashboard, custom branding (ability to remove our logo), and priority premium support.</dd>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-5">
                <dt class="font-semibold text-slate-900 dark:text-white">How does “Direct GPay / pay to you” work?</dt>
                <dd class="mt-2 text-slate-600 dark:text-slate-300 text-sm leading-relaxed">For an event you add <strong>your UPI or an uploaded static QR</strong>. We generate a <strong>dedicated scannable pay QR and link</strong> so <strong>guests pay you directly</strong> (they choose the amount), and the app records the line in your ledger. Unlock either with the <strong>in‑app per‑event fee</strong> or a <strong>₹<?php echo e(number_format((float) config('packs.guest_pay_single.amount_inr', 400), 0)); ?> Guest Contribution</strong> credit — see the <a href="<?php echo e($faqDirectGpayHref); ?>" class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline">Direct GPay section on the plans page</a>.</dd>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-5">
                <dt class="font-semibold text-slate-900 dark:text-white">When do I get a referral reward?</dt>
                <dd class="mt-2 text-slate-600 dark:text-slate-300 text-sm leading-relaxed">When your referred contact <strong>completes a qualifying payment</strong> in the app, you can receive a <strong>free event with unlimited entries + full PDF</strong> — as described in the <a href="<?php echo e($faqReferHref); ?>" class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline">Refer &amp; earn</a> section on our website.</dd>
            </div>
        </dl>
    </div>
</section>
<?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/partials/faq-section.blade.php ENDPATH**/ ?>