<?php $__env->startSection('title', 'Contacts'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-5 sm:mb-6 flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-start">
    <div>
        <h1 class="cb-page-title">Contacts</h1>
        <p class="cb-subtitle">People you track across events</p>
    </div>
    <?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
    <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
        <a href="<?php echo e(route('client.contacts.import')); ?>" class="cb-btn cb-btn--ghost w-full sm:w-auto justify-center">
            <i class="fas fa-file-import"></i>Import from phone
        </a>
        <a href="<?php echo e(route('client.contacts.create')); ?>" class="cb-btn cb-btn--gold w-full sm:w-auto justify-center">
            <i class="fas fa-plus"></i>Add contact
        </a>
    </div>
    <?php endif; ?>
</div>

<?php if(session('success')): ?>
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        <i class="fas fa-check-circle mr-1"></i><?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<div class="cb-card p-4 sm:p-5 mb-6">
    <form method="GET" action="<?php echo e(route('client.contacts.index')); ?>" id="contacts-filter-form">
        <div class="flex flex-col gap-3">
            
            <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                    <input type="text" name="search" id="contacts-search" value="<?php echo e(request('search')); ?>"
                           placeholder="Search by name, phone…"
                           class="cb-field w-full" style="padding-left: 2.5rem;">
                </div>
                <button type="submit" class="cb-btn cb-btn--navy w-full sm:w-auto shrink-0">
                    <i class="fas fa-search"></i>Search
                </button>
            </div>

            
            <div class="flex flex-col xs:flex-row sm:flex-row gap-3 sm:items-center sm:justify-between">
                <label class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-white cursor-pointer shrink-0 self-start sm:self-auto">
                    <input type="checkbox" name="favorites" value="1"
                           <?php echo e(request('favorites') ? 'checked' : ''); ?>

                           class="rounded border-slate-300 text-cb-gold focus:ring-cb-gold"
                           onchange="document.getElementById('contacts-filter-form').submit()">
                    <span class="text-sm text-slate-700 select-none">
                        <i class="fas fa-star text-amber-400 text-xs mr-0.5"></i>Favorites only
                    </span>
                </label>

                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-sm text-slate-500 whitespace-nowrap">Show:</span>
                    <select name="per_page" id="contacts-per-page"
                            class="cb-field"
                            style="width: auto; min-width: 7rem; padding-top: 0.5rem; padding-bottom: 0.5rem;"
                            onchange="document.getElementById('contacts-filter-form').submit()">
                        <option value="10"  <?php echo e($perPage == 10  ? 'selected' : ''); ?>>10 / page</option>
                        <option value="50"  <?php echo e($perPage == 50  ? 'selected' : ''); ?>>50 / page</option>
                        <option value="100" <?php echo e($perPage == 100 ? 'selected' : ''); ?>>100 / page</option>
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
    <?php $__empty_1 = true; $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <article class="cb-card p-5 flex flex-col sm:flex-row gap-4 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-start gap-4 flex-1 min-w-0">
                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-amber-100 to-amber-200 flex items-center justify-center ring-2 ring-amber-300/50 shrink-0">
                    <span class="text-amber-900 font-bold text-lg"><?php echo e(strtoupper(substr(trim($contact->name) ?: '?', 0, 1))); ?></span>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="text-base font-bold text-cb-navy truncate flex items-center gap-2">
                        <?php echo e($contact->name); ?>

                        <?php if($contact->is_favorite): ?>
                            <i class="fas fa-star text-amber-500 text-sm shrink-0"></i>
                        <?php endif; ?>
                    </h3>
                    <?php if($contact->phone): ?>
                    <p class="text-sm text-slate-600 mt-1 flex items-center gap-2">
                        <i class="fas fa-phone text-slate-400 text-xs"></i><?php echo e($contact->phone); ?>

                    </p>
                    <?php endif; ?>
                    <?php if($contact->email): ?>
                    <p class="text-sm text-slate-600 flex items-center gap-2 truncate">
                        <i class="fas fa-envelope text-slate-400 text-xs shrink-0"></i><?php echo e($contact->email); ?>

                    </p>
                    <?php endif; ?>
                    <?php if($contact->relationship): ?>
                    <p class="text-sm text-slate-500 mt-1"><?php echo e($contact->relationship); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex sm:flex-col justify-between sm:items-end gap-2 border-t sm:border-t-0 sm:border-l border-slate-100 pt-3 sm:pt-0 sm:pl-4 sm:min-w-[100px]">
                <a href="<?php echo e(route('client.contacts.show', $contact->id)); ?>" class="cb-link text-sm font-semibold">View</a>
                <div class="flex items-center gap-2">
                    <?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
                    <a href="<?php echo e(route('client.contacts.edit', $contact->id)); ?>" class="text-sky-600 hover:opacity-80 p-1" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="<?php echo e(route('client.contacts.toggle-favorite', $contact->id)); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="text-amber-600 hover:opacity-80 p-1" title="Toggle favorite">
                            <i class="fa-<?php echo e($contact->is_favorite ? 'solid' : 'regular'); ?> fa-star"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                    <?php if (\Illuminate\Support\Facades\Blade::check('canDelete')): ?>
                    <form action="<?php echo e(route('client.contacts.destroy', $contact->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="text-red-600 hover:opacity-80 p-1" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </article>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full text-center py-14 cb-card">
            <i class="fas fa-address-book text-slate-300 text-5xl mb-4"></i>
            <p class="text-slate-600 text-lg">No contacts found</p>
            <a href="<?php echo e(route('client.contacts.create')); ?>" class="cb-link mt-4 inline-block">Add your first contact</a>
        </div>
    <?php endif; ?>
</div>


<div class="mt-6">
    
    <p class="text-sm text-slate-500 mb-3">
        Showing
        <span class="font-semibold text-slate-700"><?php echo e($contacts->firstItem() ?? 0); ?></span>
        –
        <span class="font-semibold text-slate-700"><?php echo e($contacts->lastItem() ?? 0); ?></span>
        of
        <span class="font-semibold text-slate-700"><?php echo e($contacts->total()); ?></span>
        contacts
    </p>

    <?php if($contacts->hasPages()): ?>
    <div class="flex flex-wrap items-center gap-2">
        
        <?php if($contacts->onFirstPage()): ?>
            <span class="cb-btn cb-btn--ghost" style="opacity:0.4;cursor:not-allowed;">
                <i class="fas fa-chevron-left text-xs"></i> Prev
            </span>
        <?php else: ?>
            <a href="<?php echo e($contacts->previousPageUrl()); ?>" class="cb-btn cb-btn--ghost">
                <i class="fas fa-chevron-left text-xs"></i> Prev
            </a>
        <?php endif; ?>

        
        <?php
            $currentPage = $contacts->currentPage();
            $lastPage    = $contacts->lastPage();
            $window      = 2;
        ?>

        <?php for($p = 1; $p <= $lastPage; $p++): ?>
            <?php
                $inWindow          = abs($p - $currentPage) <= $window;
                $isEdge            = $p === 1 || $p === $lastPage;
                $showEllipsisBefore = ($p === $currentPage - $window - 1) && $p > 2;
                $showEllipsisAfter  = ($p === $currentPage + $window + 1) && $p < $lastPage - 1;
            ?>

            <?php if($showEllipsisBefore): ?>
                <span class="text-slate-400" style="font-size:0.875rem;padding:0 0.25rem;">…</span>
            <?php endif; ?>

            <?php if($inWindow || $isEdge): ?>
                <?php if($p === $currentPage): ?>
                    <span class="cb-page-btn cb-page-btn--active"><?php echo e($p); ?></span>
                <?php else: ?>
                    <a href="<?php echo e($contacts->url($p)); ?>" class="cb-page-btn"><?php echo e($p); ?></a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if($showEllipsisAfter): ?>
                <span class="text-slate-400" style="font-size:0.875rem;padding:0 0.25rem;">…</span>
            <?php endif; ?>
        <?php endfor; ?>

        
        <?php if($contacts->hasMorePages()): ?>
            <a href="<?php echo e($contacts->nextPageUrl()); ?>" class="cb-btn cb-btn--navy">
                Next <i class="fas fa-chevron-right text-xs"></i>
            </a>
        <?php else: ?>
            <span class="cb-btn cb-btn--navy" style="opacity:0.4;cursor:not-allowed;">
                Next <i class="fas fa-chevron-right text-xs"></i>
            </span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
<a href="<?php echo e(route('client.contacts.create')); ?>" class="cb-fab" title="Add contact" aria-label="Add contact">
    <i class="fas fa-plus"></i>
</a>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/contacts/index.blade.php ENDPATH**/ ?>