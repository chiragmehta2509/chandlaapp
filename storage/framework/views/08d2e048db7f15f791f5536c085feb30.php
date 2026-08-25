<?php $__env->startSection('title', 'FAQ'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('partials.faq-section', [
    'faqDirectGpayHref' => route('client.plans') . '#direct-gpay',
    'faqReferHref' => route('public.home') . '#refer',
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/faq.blade.php ENDPATH**/ ?>