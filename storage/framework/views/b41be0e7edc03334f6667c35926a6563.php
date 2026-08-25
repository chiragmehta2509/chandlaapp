<?php $__env->startSection('title', $vendor->business_name); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full min-w-0 max-w-5xl mx-auto">
    <div class="mb-5 sm:mb-6">
        <a href="<?php echo e(route('client.vendors.index', ['event_id' => request('event_id')])); ?>" class="cb-link text-sm inline-flex items-center gap-2 mb-3">
            <i class="fas fa-arrow-left"></i> Back to Directory
        </a>
        <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-start">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600"><?php echo e($vendor->category->name); ?></span>
                <h1 class="cb-page-title mt-1 pr-2"><?php echo e($vendor->business_name); ?></h1>
                <p class="text-sm text-slate-500 mt-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-map-marker-alt text-slate-400"></i>
                    <span><?php echo e($vendor->city); ?></span>
                </p>
            </div>
            
            
            <div class="flex gap-2">
                <?php if($vendor->is_verified): ?>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        <i class="fa-solid fa-circle-check mr-1.5"></i> Verified Partner
                    </span>
                <?php endif; ?>
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                    <?php if($vendor->price_tier === 'budget'): ?> Budget (₹) <?php elseif($vendor->price_tier === 'mid'): ?> Mid-range (₹₹) <?php else: ?> Premium (₹₹₹) <?php endif; ?>
                </span>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-6 items-start">
        
        
        <div class="lg:col-span-2 space-y-5 lg:space-y-6">
            
            
            <div class="cb-card p-5 sm:p-6">
                <h2 class="text-base font-bold text-cb-navy mb-3">About the Business</h2>
                <div class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                    <?php echo $vendor->description ?: 'No description provided by the vendor.'; ?>

                </div>
            </div>

            
            <div class="cb-card p-5 sm:p-6">
                <h2 class="text-base font-bold text-cb-navy mb-4">Portfolio / Gallery</h2>
                <?php if($vendor->portfolioImages->isNotEmpty()): ?>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <?php $__currentLoopData = $vendor->portfolioImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(asset('storage/' . $img->image_url)); ?>" target="_blank" class="group relative block aspect-[4/3] rounded-xl overflow-hidden bg-slate-50 border border-slate-100 shadow-sm transition hover:shadow-md">
                                <img src="<?php echo e(asset('storage/' . $img->image_url)); ?>" alt="Portfolio item" class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
                                <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition duration-200"></div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-10 text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        <i class="fa-regular fa-image text-3xl mb-2 text-slate-300"></i>
                        <p class="text-sm">No portfolio images uploaded yet.</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        
        <div class="lg:col-span-1">
            <div class="cb-card p-5 sm:p-6 border-slate-200/90 shadow-sm flex flex-col">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-3">Inquire / Contact</h3>
                
                <p class="text-sm text-slate-600 leading-relaxed mb-5">
                    Click the button below to submit your details and get immediate access to contact numbers and a direct chat on WhatsApp.
                </p>

                
                <button type="button" onclick="openLeadModal()" class="cb-btn cb-btn-navy w-full justify-center text-sm min-h-[2.75rem] shadow-sm border-0 font-bold transition-transform active:scale-[0.98]">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Contact Vendor
                </button>
            </div>
        </div>

    </div>
</div>


<div id="leadModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div class="fixed inset-0 bg-slate-900/60 transition-opacity" aria-hidden="true" onclick="closeLeadModal()"></div>

        
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-5 sm:p-6 border border-slate-200">
            <div class="absolute right-4 top-4">
                <button type="button" onclick="closeLeadModal()" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            
            <div id="leadFormContainer">
                <h3 class="text-lg font-bold text-cb-navy mb-2" id="modal-title">Contact Vendor</h3>
                <p class="text-xs text-slate-500 mb-4">Please fill in your details to connect with <strong><?php echo e($vendor->business_name); ?></strong>.</p>

                <form id="vendorLeadForm" onsubmit="submitLeadForm(event)">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="event_id" value="<?php echo e(request('event_id') ?: ($event->id ?? '')); ?>">
                    
                    <div class="space-y-4">
                        <label class="block min-w-0">
                            <span class="text-xs font-semibold text-slate-600">Your Name</span>
                            <input type="text" name="host_name" required value="<?php echo e(Auth::user()->name); ?>" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                        </label>

                        <label class="block min-w-0">
                            <span class="text-xs font-semibold text-slate-600">Your Phone Number</span>
                            <input type="text" name="host_phone" required value="<?php echo e(Auth::user()->phone); ?>" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                        </label>

                        <label class="block min-w-0">
                            <span class="text-xs font-semibold text-slate-600">Event Context (Optional)</span>
                            <select name="selected_event_id" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm bg-white">
                                <option value="">No specific event</option>
                                <?php if($event): ?>
                                    <option value="<?php echo e($event->id); ?>" selected><?php echo e($event->title); ?></option>
                                <?php endif; ?>
                            </select>
                        </label>

                        <label class="block min-w-0">
                            <span class="text-xs font-semibold text-slate-600">Your Message (Optional)</span>
                            <textarea name="message" rows="3" placeholder="Tell the vendor about your date, venue, or budget requirements..." class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"></textarea>
                        </label>
                    </div>

                    <div class="mt-5 flex gap-2">
                        <button type="submit" class="cb-btn cb-btn-gold text-sm min-h-[2.5rem] flex-1 justify-center">
                            Submit Inquiry
                        </button>
                        <button type="button" onclick="closeLeadModal()" class="cb-btn border border-slate-200 text-sm min-h-[2.5rem] justify-center px-4">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            
            <div id="leadSuccessContainer" class="hidden text-center py-4">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 mb-4">
                    <i class="fa-solid fa-circle-check text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-cb-navy mb-2">Lead Registered!</h3>
                <p class="text-sm text-slate-600 mb-6">Your inquiry details have been saved. You can now contact the vendor directly via WhatsApp or call them.</p>

                <div class="space-y-3">
                    <?php if($vendor->whatsapp): ?>
                        <a href="#" id="whatsappDeepLink" target="_blank" class="cb-btn bg-emerald-600 hover:bg-emerald-700 text-white w-full justify-center py-2.5 rounded-lg font-semibold inline-flex items-center gap-2">
                            <i class="fa-brands fa-whatsapp text-lg"></i> Chat on WhatsApp
                        </a>
                    <?php endif; ?>
                    <div class="bg-slate-50 border border-slate-100 p-3 rounded-lg text-sm text-slate-800">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Phone Number</p>
                        <a href="tel:<?php echo e($vendor->phone); ?>" class="text-base font-bold text-cb-navy hover:underline select-all"><?php echo e($vendor->phone); ?></a>
                    </div>
                    <button type="button" onclick="closeLeadModal()" class="cb-btn border border-slate-200 text-sm min-h-[2.5rem] w-full justify-center">
                        Close
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function openLeadModal() {
        document.getElementById('leadModal').classList.remove('hidden');
    }

    function closeLeadModal() {
        document.getElementById('leadModal').classList.add('hidden');
        // Reset
        document.getElementById('leadFormContainer').classList.remove('hidden');
        document.getElementById('leadSuccessContainer').classList.add('hidden');
    }

    function submitLeadForm(e) {
        e.preventDefault();
        
        const form = document.getElementById('vendorLeadForm');
        const formData = new FormData(form);
        
        // Map selected event id back to the event_id field
        const selEvent = form.querySelector('[name="selected_event_id"]').value;
        if(selEvent) {
            formData.set('event_id', selEvent);
        }

        fetch('<?php echo e(route("client.vendors.lead.submit", $vendor->id)); ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Construct whatsapp link
                const businessName = <?php echo json_encode($vendor->business_name, 15, 512) ?>;
                const whatsappNum = <?php echo json_encode($vendor->whatsapp, 15, 512) ?>;
                
                if(whatsappNum) {
                    const cleanNum = whatsappNum.replace(/[^0-9]/g, '');
                    const messageText = `Hello ${businessName}, I found your business profile on Chandla Book. I'm interested in your services for my event.`;
                    const waUrl = `https://wa.me/${cleanNum}?text=${encodeURIComponent(messageText)}`;
                    document.getElementById('whatsappDeepLink').setAttribute('href', waUrl);
                }

                // Swap views
                document.getElementById('leadFormContainer').classList.add('hidden');
                document.getElementById('leadSuccessContainer').classList.remove('hidden');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Something went wrong. Please try again.');
        });
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/vendors/show.blade.php ENDPATH**/ ?>