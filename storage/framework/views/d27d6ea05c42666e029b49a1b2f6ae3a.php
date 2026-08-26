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


<!-- Users Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="data-table min-w-full divide-y divide-gray-200">
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
                            <form action="<?php echo e(route('admin.users.toggle-status', $user->id)); ?>" method="POST" class="inline mr-3">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900" title="<?php echo e($user->is_active ? 'Deactivate' : 'Activate'); ?> User">
                                    <i class="fas fa-toggle-<?php echo e($user->is_active ? 'on' : 'off'); ?>"></i>
                                </button>
                            </form>
                            <button type="button"
                                onclick="confirmDelete('<?php echo e(route('admin.users.destroy', $user->id)); ?>', '<?php echo e(addslashes($user->name)); ?>')"
                                class="text-red-600 hover:text-red-900" title="Delete User">
                                <i class="fas fa-trash"></i>
                            </button>
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

</div>


<div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900">Delete User</h3>
                <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-gray-700 mb-6">Are you sure you want to permanently delete <strong id="deleteUserName"></strong>? All their data will be removed.</p>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium text-sm transition-colors">
                Cancel
            </button>
            <form id="deleteForm" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-medium text-sm transition-colors">
                    <i class="fas fa-trash mr-1"></i> Delete Permanently
                </button>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    function confirmDelete(actionUrl, userName) {
        document.getElementById('deleteUserName').textContent = userName;
        document.getElementById('deleteForm').action = actionUrl;
        document.getElementById('deleteModal').classList.remove('hidden');
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
    // Close on backdrop click
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/admin/users/index.blade.php ENDPATH**/ ?>