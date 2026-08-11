

<?php $__env->startSection('title', 'Users Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Users Management</h1>
        <p class="text-gray-600 mt-1">Manage all users in the system</p>
    </div>
    <a href="<?php echo e(route('admin.users.create')); ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-sm transition-colors flex items-center gap-1.5">
        <i class="fas fa-user-plus"></i> Add New User
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form method="GET" action="<?php echo e(route('admin.users.index')); ?>" class="flex gap-4">
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search users..." 
               class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All Status</option>
            <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Active</option>
            <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
            <option value="premium" <?php echo e(request('status') === 'premium' ? 'selected' : ''); ?>>Premium</option>
        </select>
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
            <i class="fas fa-search mr-2"></i>Search
        </button>
    </form>
</div>

<!-- Users Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subscription</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-600 font-semibold"><?php echo e(substr($user->name, 0, 1)); ?></span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($user->name); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo e($user->email); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo e($user->phone ?? 'N/A'); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                <?php echo e($user->is_active ? 'Active' : 'Inactive'); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($user->subscription_status === 'premium' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'); ?>">
                                <?php echo e(ucfirst($user->subscription_status ?? 'free')); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo e($user->created_at->format('M d, Y')); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="<?php echo e(route('admin.users.show', $user->id)); ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('admin.users.toggle-status', $user->id)); ?>" method="POST" class="inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900">
                                    <i class="fas fa-toggle-<?php echo e($user->is_active ? 'on' : 'off'); ?>"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No users found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="bg-gray-50 px-6 py-4">
        <?php echo e($users->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/admin/users/index.blade.php ENDPATH**/ ?>