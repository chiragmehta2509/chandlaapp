<?php $__env->startSection('title', 'Add New User'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 max-w-2xl mx-auto">

    <div class="mb-6">
        <a href="<?php echo e(route('admin.users.index')); ?>" class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-gray-800 transition-colors mb-2">
            <i class="fas fa-arrow-left text-[10px]"></i> Back to Users
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Create New User</h1>
        <p class="text-xs text-gray-500 mt-1">Manually register a new client account.</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
        <form method="POST" action="<?php echo e(route('admin.users.store')); ?>">
            <?php echo csrf_field(); ?>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Full Name *</label>
                    <input type="text" name="name" value="<?php echo e(old('name')); ?>" placeholder="John Doe" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Email Address *</label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="user@example.com" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Phone Number</label>
                    <input type="text" name="phone" value="<?php echo e(old('phone')); ?>" placeholder="+91 9876543210"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Password *</label>
                    <input type="password" name="password" required minlength="6" placeholder="At least 6 characters"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="<?php echo e(route('admin.users.index')); ?>"
                        class="px-5 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-sm transition-colors">
                        <i class="fas fa-user-plus mr-1.5"></i> Create User
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/admin/users/create.blade.php ENDPATH**/ ?>