<?php $__env->startSection('title', 'Manage Vendors'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Manage Vendors</h1>
    <p class="text-gray-600 mt-1">Review pending submissions, manage active profiles, and check customer leads.</p>
</div>

<?php if(session('success')): ?>
    <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 text-sm">
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<div class="space-y-8">
    
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h2 class="font-bold text-gray-800 text-base">Pending Approvals (<?php echo e($pendingVendors->count()); ?>)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider text-left">
                    <tr>
                        <th class="px-6 py-3">Business Name</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">City</th>
                        <th class="px-6 py-3">Price</th>
                        <th class="px-6 py-3">Contact</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $pendingVendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-900"><?php echo e($v->business_name); ?></span>
                                <?php if($v->description): ?>
                                    <p class="text-xs text-gray-400 mt-1 max-w-sm truncate"><?php echo e($v->description); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4"><?php echo e($v->category->name); ?></td>
                            <td class="px-6 py-4"><?php echo e($v->city); ?></td>
                            <td class="px-6 py-4 uppercase font-bold text-xs text-gray-500"><?php echo e($v->price_tier); ?></td>
                            <td class="px-6 py-4">
                                <div>Call: <span class="font-medium text-gray-950"><?php echo e($v->phone); ?></span></div>
                                <?php if($v->whatsapp): ?>
                                    <div class="text-xs text-green-600">WA: <?php echo e($v->whatsapp); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <form method="POST" action="<?php echo e(route('admin.vendors.approve', $v->id)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-1 px-3 rounded text-xs">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="<?php echo e(route('admin.vendors.reject', $v->id)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs" onsubmit="return confirm('Reject this vendor?');">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400 bg-gray-50/50">No pending vendor approvals.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="font-bold text-gray-800 text-base">Active Vendors (<?php echo e($activeVendors->count()); ?>)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider text-left">
                    <tr>
                        <th class="px-6 py-3">Business Name</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">City</th>
                        <th class="px-6 py-3">Price</th>
                        <th class="px-6 py-3">Contact</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $activeVendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-6 py-4 font-semibold text-gray-900"><?php echo e($v->business_name); ?></td>
                            <td class="px-6 py-4"><?php echo e($v->category->name); ?></td>
                            <td class="px-6 py-4"><?php echo e($v->city); ?></td>
                            <td class="px-6 py-4 uppercase font-bold text-xs text-gray-500"><?php echo e($v->price_tier); ?></td>
                            <td class="px-6 py-4"><?php echo e($v->phone); ?></td>
                            <td class="px-6 py-4">
                                <form method="POST" action="<?php echo e(route('admin.vendors.reject', $v->id)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="border border-red-200 hover:bg-red-50 text-red-600 font-bold py-1 px-3 rounded text-xs" onsubmit="return confirm('Deactivate this vendor?');">
                                        Deactivate
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400 bg-gray-50/50">No active vendors found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="font-bold text-gray-800 text-base">Customer Leads Log</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider text-left">
                    <tr>
                        <th class="px-6 py-3">Vendor</th>
                        <th class="px-6 py-3">Host Contact</th>
                        <th class="px-6 py-3">Event Context</th>
                        <th class="px-6 py-3">Message</th>
                        <th class="px-6 py-3">Created At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-900"><?php echo e($l->vendor->business_name); ?></span>
                                <p class="text-xs text-gray-400"><?php echo e($l->vendor->category->name); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900"><?php echo e($l->host_name); ?></span>
                                <p class="text-xs text-gray-500"><?php echo e($l->host_phone); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <?php if($l->event): ?>
                                    <span class="font-medium text-blue-600"><?php echo e($l->event->title); ?></span>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400">None</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-xs max-w-xs truncate" title="<?php echo e($l->message); ?>">
                                <?php echo e($l->message ?: 'No message text provided'); ?>

                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500"><?php echo e($l->created_at->format('d/m/Y h:i A')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400 bg-gray-50/50">No leads recorded yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($leads->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <?php echo e($leads->links()); ?>

            </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/admin/vendors/index.blade.php ENDPATH**/ ?>