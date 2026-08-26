<?php $__env->startSection('title', 'Account'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-lg mx-auto">
    <div class="mb-6 text-center sm:text-left">
        <h1 class="cb-page-title">Account</h1>
        <p class="cb-subtitle">Change your sign-in password.</p>
    </div>

    <div class="cb-card p-5 sm:p-6">
        <p class="cb-section-label text-left">Security</p>
        <form method="POST" action="<?php echo e(route('client.password.update')); ?>" class="space-y-5">
            <?php echo csrf_field(); ?>

            <div>
                <label for="current_password" class="cb-label !normal-case !tracking-normal !text-slate-600 !text-sm">Current password *</label>
                <div class="relative">
                    <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="cb-field pr-10">
                    <button type="button" 
                        onmousedown="document.getElementById('current_password').type='text'; this.querySelector('i').classList.replace('fa-eye', 'fa-eye-slash');" 
                        onmouseup="document.getElementById('current_password').type='password'; this.querySelector('i').classList.replace('fa-eye-slash', 'fa-eye');" 
                        onmouseleave="document.getElementById('current_password').type='password'; this.querySelector('i').classList.replace('fa-eye-slash', 'fa-eye');" 
                        ontouchstart="document.getElementById('current_password').type='text'; this.querySelector('i').classList.replace('fa-eye', 'fa-eye-slash');" 
                        ontouchend="document.getElementById('current_password').type='password'; this.querySelector('i').classList.replace('fa-eye-slash', 'fa-eye');" 
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-none">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div>
                <label for="password" class="cb-label !normal-case !tracking-normal !text-slate-600 !text-sm">New password *</label>
                <div class="relative">
                    <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" class="cb-field pr-10">
                    <button type="button" 
                        onmousedown="document.getElementById('password').type='text'; this.querySelector('i').classList.replace('fa-eye', 'fa-eye-slash');" 
                        onmouseup="document.getElementById('password').type='password'; this.querySelector('i').classList.replace('fa-eye-slash', 'fa-eye');" 
                        onmouseleave="document.getElementById('password').type='password'; this.querySelector('i').classList.replace('fa-eye-slash', 'fa-eye');" 
                        ontouchstart="document.getElementById('password').type='text'; this.querySelector('i').classList.replace('fa-eye', 'fa-eye-slash');" 
                        ontouchend="document.getElementById('password').type='password'; this.querySelector('i').classList.replace('fa-eye-slash', 'fa-eye');" 
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-none">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="cb-label !normal-case !tracking-normal !text-slate-600 !text-sm">Confirm new password *</label>
                <div class="relative">
                    <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" class="cb-field pr-10">
                    <button type="button" 
                        onmousedown="document.getElementById('password_confirmation').type='text'; this.querySelector('i').classList.replace('fa-eye', 'fa-eye-slash');" 
                        onmouseup="document.getElementById('password_confirmation').type='password'; this.querySelector('i').classList.replace('fa-eye-slash', 'fa-eye');" 
                        onmouseleave="document.getElementById('password_confirmation').type='password'; this.querySelector('i').classList.replace('fa-eye-slash', 'fa-eye');" 
                        ontouchstart="document.getElementById('password_confirmation').type='text'; this.querySelector('i').classList.replace('fa-eye', 'fa-eye-slash');" 
                        ontouchend="document.getElementById('password_confirmation').type='password'; this.querySelector('i').classList.replace('fa-eye-slash', 'fa-eye');" 
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-none">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="cb-btn cb-btn-navy w-full sm:w-auto rounded-2xl py-3">
                    <i class="fas fa-floppy-disk"></i>Update password
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/auth/change-password.blade.php ENDPATH**/ ?>