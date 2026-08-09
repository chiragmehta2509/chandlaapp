<?php if (! $__env->hasRenderedOnce('5dc57ca4-c2a3-4167-bf10-1aa9a003d897')): $__env->markAsRenderedOnce('5dc57ca4-c2a3-4167-bf10-1aa9a003d897'); ?>
    <?php $__env->startPush('scripts'); ?>
        <script>
            (function () {
                var wrap = document.getElementById('event-type-widget');
                if (!wrap) return;
                var sel = document.getElementById('event-type-select');
                var btn = document.getElementById('event-type-trigger');
                var list = document.getElementById('event-type-listbox');
                if (!sel || !btn || !list) return;
                var textEl = btn.querySelector('.js-event-type-text');
                var chev = btn.querySelector('.js-event-type-chev');
                var opts = list.querySelectorAll('[role="option"]');

                function openState() {
                    return !list.classList.contains('hidden');
                }
                function setOpen(open) {
                    list.classList.toggle('hidden', !open);
                    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
                    if (chev) {
                        chev.style.transform = open ? 'rotate(180deg)' : 'rotate(0deg)';
                    }
                }
                function syncAriaSelected() {
                    var val = sel.value;
                    opts.forEach(function (li) {
                        if (li.id === 'event-type-clear-option') {
                            li.setAttribute('aria-selected', 'false');
                            return;
                        }
                        var v = li.getAttribute('data-value');
                        if (v === null) v = '';
                        li.setAttribute('aria-selected', v === val ? 'true' : 'false');
                    });
                }
                function readLabel() {
                    var opt = sel.options[sel.selectedIndex];
                    return opt ? String(opt.textContent).trim() : '';
                }
                function syncPlaceholderClass() {
                    if (!btn) return;
                    btn.classList.toggle('cb-event-type-trigger--placeholder', sel.value === '');
                }

                function syncClearRow() {
                    var clearLi = document.getElementById('event-type-clear-option');
                    if (clearLi) {
                        clearLi.classList.toggle('hidden', sel.value === '');
                    }
                }

                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    setOpen(!openState());
                });
                list.addEventListener('click', function (e) {
                    var li = e.target.closest('[role="option"]');
                    if (!li) return;
                    e.stopPropagation();
                    var v = li.getAttribute('data-value');
                    if (v === null) v = '';
                    sel.value = v;
                    if (textEl) textEl.textContent = readLabel();
                    syncAriaSelected();
                    syncPlaceholderClass();
                    syncClearRow();
                    setOpen(false);
                });
                document.addEventListener('click', function () {
                    setOpen(false);
                });
                wrap.addEventListener('click', function (e) {
                    e.stopPropagation();
                });
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && openState()) {
                        setOpen(false);
                    }
                });

                sel.addEventListener('change', function () {
                    if (textEl) textEl.textContent = readLabel();
                    syncAriaSelected();
                    syncPlaceholderClass();
                    syncClearRow();
                });

                syncAriaSelected();
                syncPlaceholderClass();
                syncClearRow();
                if (chev && !openState()) {
                    chev.style.transform = 'rotate(0deg)';
                }
            })();
        </script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/events/partials/event-type-select-scripts.blade.php ENDPATH**/ ?>