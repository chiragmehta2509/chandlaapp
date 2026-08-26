
<?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $params = ['layout' => $key];
        if (isset($invitation) && $invitation) {
            $params['invitation_id'] = $invitation->id;
        } elseif (isset($latestInvitation) && $latestInvitation) {
            $params['invitation_id'] = $latestInvitation->id;
        }
        $thumbSrc = route('client.marriage-invitations.template-demo', $params);
        $thumbTitle = ($t['name'] ?? $key).' — demo preview';
    ?>
    <div class="cb-card p-0 overflow-hidden border border-slate-200/80 flex flex-col shadow-sm relative">
        <span class="absolute top-2 right-2 z-10 inline-flex items-center gap-1 rounded-full bg-slate-900/75 text-white text-[0.65rem] font-semibold px-2 py-0.5 backdrop-blur-sm">
            <i class="fas fa-lock text-[0.6rem]" aria-hidden="true"></i> Demo
        </span>
        <div class="px-4 py-3 border-b border-slate-100 flex items-center gap-3 bg-slate-50/80">
            <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-sm font-bold <?php echo e($t['badge_class'] ?? 'bg-slate-200 text-slate-800'); ?>"><?php echo e($t['badge'] ?? substr($key, 0, 1)); ?></span>
            <h3 class="text-base font-bold text-cb-navy leading-tight"><?php echo e($t['name']); ?></h3>
        </div>
        <div class="w-full overflow-hidden">
            <?php echo $__env->make('client.marriage-invitations.partials.template-thumb-iframe', ['thumbSrc' => $thumbSrc, 'thumbTitle' => $thumbTitle], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <div class="p-4 sm:p-5">
            <p class="text-sm text-slate-600 leading-relaxed"><?php echo e($t['description'] ?? ''); ?></p>
            <p class="text-xs text-amber-800/90 mt-3 font-medium">Pay the celebration pack to use your own wording and photo on every style.</p>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /home/chandlabook/public_html/resources/views/client/marriage-invitations/partials/demo-style-cards.blade.php ENDPATH**/ ?>