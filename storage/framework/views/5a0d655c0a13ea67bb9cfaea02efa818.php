<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php $__currentLoopData = $urls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <url>
        <loc><?php echo e($loc); ?></loc>
        <changefreq><?php if($loop->first): ?>weekly@else yearly <?php endif; ?></changefreq>
        <priority><?php if($loop->first): ?>1.0@else0.5@endif</priority>
    </url>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</urlset>
<?php /**PATH /home/chandlabook/public_html/resources/views/public/sitemap-xml.blade.php ENDPATH**/ ?>