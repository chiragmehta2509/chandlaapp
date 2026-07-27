<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/chandla-favicon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/chandla-app-icon.png')); ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $__env->make('public.partials.seo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('public.partials.jsonld', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/tailwind.css')); ?>?v=4">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white">
    <header class="sticky top-0 z-50 w-full bg-slate-50 dark:bg-slate-950/80 backdrop-blur-md border-b border-slate-200 dark:border-white/5 transition-all duration-300">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center">
                <img src="<?php echo e(asset('images/chandla-favicon.png')); ?>" alt="Chandla Book" class="h-14 w-auto mr-3" decoding="async">
                <span class="text-2xl font-bold text-slate-900 dark:text-white">Chandla Book</span>
            </div>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <button id="themeToggleBtn" class="w-10 h-10 flex items-center justify-center text-slate-900 dark:text-white/80 hover:text-slate-900 dark:hover:text-white rounded-full bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 transition-colors" title="Toggle theme">
                    <i class="fas fa-sun" id="themeToggleIcon"></i>
                </button>
                <a href="<?php echo e(route('public.contact')); ?>" class="px-4 py-2 text-slate-900 dark:text-white/80 hover:text-slate-900 dark:hover:text-white font-semibold">Contact</a>
                <a href="<?php echo e(route('client.login')); ?>" class="px-4 py-2 text-slate-900 dark:text-white/80 hover:text-slate-900 dark:hover:text-white font-semibold">Login</a>
                <a href="<?php echo e(route('client.register')); ?>" class="px-4 py-2 bg-white text-indigo-700 rounded-lg hover:bg-indigo-50 font-semibold">Get Started</a>
            </div>
        </div>
    </header>

    <div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-slate-50 dark:from-indigo-950 dark:via-purple-900 dark:to-slate-950">

        <section class="max-w-6xl mx-auto px-6 py-12">
            
            <div class="text-center mb-10">
                <div class="inline-flex items-center gap-2 bg-slate-200 dark:bg-white/10 text-slate-900 dark:text-white/90 px-3 py-1 rounded-full text-sm mb-4">
                    <i class="fas fa-sparkles"></i>
                    Smart event collection platform
                </div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold leading-tight whitespace-nowrap overflow-hidden text-ellipsis">
                    Track every rupee, every note, and every guest in one beautiful dashboard.
                </h1>
                <p class="mt-4 text-slate-900 dark:text-white/80 text-lg">
                    Cash, Cover, Gift, UPI proofs, PDFs, inventory, and admin reports — built for real Indian events.
                </p>
                <div class="mt-8 flex flex-wrap justify-center items-center gap-3">
                    <a href="<?php echo e(route('client.register')); ?>" class="px-6 py-3 bg-white text-indigo-700 rounded-lg hover:bg-indigo-50 font-semibold">
                        Start Free
                    </a>
                    <a href="#pricing" class="px-6 py-3 border border-slate-400 dark:border-white/30 rounded-lg hover:bg-slate-200 dark:hover:bg-white/10 font-medium">
                        View Plans
                    </a>
                    <?php
                        $shareSiteUrl = url('/');
                        $waShareText = rawurlencode('Chandla Book — event chandla ledger & Guest Contributionments for Indian occasions. '.$shareSiteUrl);
                    ?>
                    <a href="https://wa.me/?text=<?php echo e($waShareText); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-6 py-3 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold shadow-lg shadow-emerald-900/30">
                        <i class="fab fa-whatsapp mr-2" aria-hidden="true"></i> Share on WhatsApp
                    </a>
                </div>

                
                <div class="mt-6 flex flex-wrap justify-center items-center gap-4 text-xs font-semibold text-slate-600 dark:text-slate-400">
                    <span>Download Mobile App:</span>
                    <a href="<?php echo e(config('chandlabook.play_store_url')); ?>" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 dark:bg-white/10 dark:hover:bg-white/20 text-slate-900 dark:text-white transition-colors border border-slate-300 dark:border-white/10">
                        <i class="fab fa-google-play text-sm text-green-500"></i> Google Play
                    </a>
                    <a href="<?php echo e(config('chandlabook.app_store_url')); ?>" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 dark:bg-white/10 dark:hover:bg-white/20 text-slate-900 dark:text-white transition-colors border border-slate-300 dark:border-white/10">
                        <i class="fab fa-apple text-sm"></i> App Store
                    </a>
                </div>
            </div>

            
            <div class="max-w-2xl mx-auto">
                <div class="bg-slate-200 dark:bg-white/10 backdrop-blur rounded-2xl p-6 border border-slate-300 dark:border-white/20">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold">Live Snapshot</h3>
                        <span class="text-xs text-slate-900 dark:text-white/70">Last 24 hours</span>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-slate-200 dark:bg-white/10 rounded-xl p-4">
                            <div class="flex items-center justify-between text-sm text-slate-900 dark:text-white/80">
                                <span>Total Collected</span>
                                <span class="font-semibold">₹1,52,400</span>
                            </div>
                            <div class="mt-2 h-2 bg-slate-200 dark:bg-white/10 rounded-full overflow-hidden">
                                <div class="h-full w-3/4 bg-emerald-400 rounded-full"></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-slate-200 dark:bg-white/10 rounded-xl p-4">
                                <div class="text-xs text-slate-900 dark:text-white/70">Cash on hand</div>
                                <div class="text-xl font-bold">₹38,920</div>
                            </div>
                            <div class="bg-slate-200 dark:bg-white/10 rounded-xl p-4">
                                <div class="text-xs text-slate-900 dark:text-white/70">Pending change</div>
                                <div class="text-xl font-bold">₹1,120</div>
                            </div>
                        </div>
                        <div class="bg-slate-200 dark:bg-white/10 rounded-xl p-4 text-sm text-slate-900 dark:text-white/80">
                            <i class="fas fa-check text-emerald-400 mr-2"></i>
                            Inventory auto-updates with every cash entry.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="pricing">
            <?php echo $__env->make('partials.pricing-section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </section>
    </div>


    <section class="bg-white dark:bg-slate-900 text-slate-900 dark:text-white border-y border-slate-200 dark:border-white/10">
        <div class="max-w-6xl mx-auto px-6 py-12">
            <h2 class="text-2xl md:text-3xl font-bold text-center mb-8">What you get at a glance</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="rounded-2xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 p-6 md:p-8">
                    <div class="text-4xl font-black text-amber-300 tabular-nums mb-2">₹<?php echo e(number_format((float) config('packs.celebration.amount_inr', 300), 0)); ?></div>
                    <h3 class="text-xl font-semibold mb-3">Marriage invitation for social media</h3>
                    <ul class="space-y-2 text-slate-900 dark:text-white/80 text-sm leading-relaxed">
                        <li><i class="fas fa-check text-amber-400 mr-2"></i>One beautiful card — <strong>10+ layouts</strong> including Heritage gold, Minimal bloom, royal, garden, midnight glam &amp; more</li>
                        <li><i class="fas fa-check text-amber-400 mr-2"></i>Photo, schedule of events, venue &amp; dates</li>
                        <li><i class="fas fa-check text-amber-400 mr-2"></i>Print or save as PDF — perfect for <strong>WhatsApp, Instagram, Facebook</strong></li>
                    </ul>
                    <div class="mt-6 pt-6 border-t border-slate-200 dark:border-white/10">
                        <p class="text-xs font-semibold text-amber-200/90 uppercase tracking-wider mb-3">Layout previews (demo)</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <?php
                                $demoCoupleSrc = asset('images/marriage-demo-couple.jpg').'?v=6';
                            ?>
                            <figure class="m-0 min-w-0 w-full">
                                <div class="rounded-xl overflow-hidden border border-amber-500/30 bg-slate-50 dark:bg-slate-950/50 shadow-lg w-full">
                                    <div class="bg-gradient-to-b from-[#fffef9] to-[#ede6d8] text-center px-2 pt-3 pb-2 border-b border-amber-800/20">
                                        <p class="text-[0.55rem] sm:text-[0.6rem] tracking-[0.2em] text-amber-950/80 font-serif font-medium">WEDDING INVITATION</p>
                                        <p class="text-[0.5rem] sm:text-[0.55rem] mt-0.5 italic text-amber-900/60 font-serif">Together with our families</p>
                                        <div class="h-px w-16 mx-auto my-1.5 bg-gradient-to-r from-transparent via-amber-600/70 to-transparent"></div>
                                    </div>
                                    <div class="p-1.5 sm:p-2 bg-gradient-to-br from-amber-500/30 to-amber-900/30 w-full">
                                        <div class="w-full min-w-0 overflow-hidden rounded-lg bg-amber-950/20 h-40 sm:h-48">
                                            <img
                                                src="<?php echo e($demoCoupleSrc); ?>"
                                                alt="Sample couple photo, Heritage style"
                                                class="h-full w-full object-cover object-center"
                                                width="200"
                                                height="267"
                                                loading="lazy"
                                                decoding="async"
                                            />
                                        </div>
                                    </div>
                                    <p class="text-center text-sm sm:text-base font-serif text-amber-950/95 py-1.5 bg-[#fffef9]/95">Barbie &amp; Ken</p>
                                </div>
                                <figcaption class="text-center text-xs text-amber-200/85 font-medium mt-2">Heritage gold</figcaption>
                            </figure>
                            <figure class="m-0 min-w-0 w-full">
                                <div class="rounded-2xl overflow-hidden border border-slate-400/20 bg-white dark:bg-slate-900/80 shadow-lg w-full">
                                    <div class="h-1.5 w-full bg-gradient-to-r from-fuchsia-500 via-violet-500 to-cyan-400"></div>
                                    <div class="px-2 pt-2 pb-1 text-center bg-white text-slate-900">
                                        <p class="text-[0.5rem] sm:text-[0.55rem] tracking-[0.18em] text-slate-500 font-semibold">WE INVITE YOU TO CELEBRATE</p>
                                        <p class="text-lg sm:text-xl font-serif font-semibold text-slate-900 mt-1">Barbie</p>
                                        <p class="text-sm font-bold bg-gradient-to-r from-violet-600 via-pink-600 to-cyan-600 bg-clip-text text-transparent">&amp;</p>
                                        <p class="text-lg sm:text-xl font-serif font-semibold text-slate-900">Ken</p>
                                    </div>
                                    <div class="p-1.5 sm:p-2 bg-slate-100 w-full">
                                        <div class="w-full min-w-0 h-32 sm:h-36 overflow-hidden rounded-xl border-2 border-slate-200">
                                            <img
                                                src="<?php echo e($demoCoupleSrc); ?>"
                                                alt="Sample couple photo, Minimal style"
                                                class="h-full w-full object-cover object-center"
                                                width="200"
                                                height="160"
                                                loading="lazy"
                                                decoding="async"
                                            />
                                        </div>
                                    </div>
                                    <p class="text-center text-[0.6rem] tracking-wider text-slate-500 font-semibold py-1.5 bg-white">WHEN - WHERE</p>
                                </div>
                                <figcaption class="text-center text-xs text-amber-200/85 font-medium mt-2">Minimal bloom</figcaption>
                            </figure>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 p-6 md:p-8">
                    <div class="text-4xl font-black text-emerald-300 tabular-nums mb-2">₹<?php echo e(number_format((float) config('packs.guest_pay_single.amount_inr', 400), 0)); ?></div>
                    <h3 class="text-xl font-semibold mb-3">Guest Contribution — ledger + “pay to you” for <strong>one</strong> event</h3>
                    <ul class="space-y-2 text-slate-900 dark:text-white/80 text-sm leading-relaxed">
                        <li><i class="fas fa-check text-emerald-400 mr-2" aria-hidden="true"></i><strong>₹<?php echo e(number_format((float) config('packs.guest_pay_single.amount_inr', 400), 0)); ?> one-time</strong> → one <strong>credit</strong>; apply it on <strong>Unlock Direct QR</strong> in the app for the event you choose.</li>
                        <li><i class="fas fa-check text-emerald-400 mr-2" aria-hidden="true"></i><strong>Unlimited chandla</strong> on that event, <strong>full event PDF</strong> export &amp; email, plus <strong>Direct GPay</strong>: your UPI / QR → guests pay <strong>any amount</strong> straight to you.</li>
                        <li><i class="fas fa-check text-emerald-400 mr-2" aria-hidden="true"></i>Alternatively, unlock Direct GPay per event in-app for <strong>₹<?php echo e(number_format((float) config('services.direct_gpay_unlock.amount', 400), 0)); ?></strong> (see checkout for current amount).</li>
                        <li><i class="fas fa-check text-emerald-400 mr-2" aria-hidden="true"></i><strong>Host Plus Plan</strong> / <strong>Premium Host Plan</strong> cover <strong>2 or 3 events</strong> with account-wide unlimited chandla — see the pricing table.</li>
                    </ul>

                    
                    <div class="mt-6 pt-6 border-t border-slate-200 dark:border-white/10">
                        <p class="text-xs font-semibold text-emerald-200/90 uppercase tracking-wider mb-3">Feature previews (demo)</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                            
                            <figure class="m-0 min-w-0 w-full">
                                <div class="rounded-xl overflow-hidden border border-emerald-500/30 bg-slate-50 dark:bg-slate-950/50 shadow-lg w-full">
                                    
                                    <div class="bg-gradient-to-r from-emerald-700 to-teal-700 px-3 py-2 flex items-center gap-2">
                                        <div class="h-2 w-2 rounded-full bg-white/40"></div>
                                        <p class="text-[0.55rem] tracking-[0.18em] text-slate-900 dark:text-white/90 font-semibold uppercase">Direct GPay · Unlocked</p>
                                    </div>
                                    
                                    <div class="bg-white dark:bg-slate-900 px-3 pt-3 pb-2 flex flex-col items-center gap-2">
                                        
                                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-lg border-2 border-emerald-400/60 bg-white p-1 flex items-center justify-center shadow-md shadow-emerald-900/40">
                                            <svg viewBox="0 0 21 21" class="w-full h-full" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <!-- QR finder squares -->
                                                <rect x="1" y="1" width="6" height="6" rx="0.5" fill="#0f172a"/>
                                                <rect x="2" y="2" width="4" height="4" rx="0.3" fill="white"/>
                                                <rect x="3" y="3" width="2" height="2" fill="#0f172a"/>
                                                <rect x="14" y="1" width="6" height="6" rx="0.5" fill="#0f172a"/>
                                                <rect x="15" y="2" width="4" height="4" rx="0.3" fill="white"/>
                                                <rect x="16" y="3" width="2" height="2" fill="#0f172a"/>
                                                <rect x="1" y="14" width="6" height="6" rx="0.5" fill="#0f172a"/>
                                                <rect x="2" y="15" width="4" height="4" rx="0.3" fill="white"/>
                                                <rect x="3" y="16" width="2" height="2" fill="#0f172a"/>
                                                <!-- data dots -->
                                                <rect x="9" y="1" width="1" height="1" fill="#0f172a"/>
                                                <rect x="11" y="1" width="1" height="1" fill="#0f172a"/>
                                                <rect x="9" y="3" width="2" height="1" fill="#0f172a"/>
                                                <rect x="9" y="5" width="1" height="2" fill="#0f172a"/>
                                                <rect x="11" y="4" width="2" height="1" fill="#0f172a"/>
                                                <rect x="8" y="8" width="1" height="2" fill="#0f172a"/>
                                                <rect x="10" y="8" width="2" height="1" fill="#0f172a"/>
                                                <rect x="13" y="8" width="1" height="2" fill="#0f172a"/>
                                                <rect x="15" y="8" width="2" height="1" fill="#0f172a"/>
                                                <rect x="18" y="9" width="2" height="1" fill="#0f172a"/>
                                                <rect x="8" y="11" width="2" height="1" fill="#0f172a"/>
                                                <rect x="11" y="11" width="1" height="2" fill="#0f172a"/>
                                                <rect x="14" y="11" width="2" height="2" fill="#0f172a"/>
                                                <rect x="17" y="11" width="1" height="1" fill="#0f172a"/>
                                                <rect x="8" y="14" width="1" height="2" fill="#0f172a"/>
                                                <rect x="10" y="14" width="2" height="1" fill="#0f172a"/>
                                                <rect x="13" y="15" width="1" height="2" fill="#0f172a"/>
                                                <rect x="15" y="14" width="2" height="1" fill="#0f172a"/>
                                                <rect x="18" y="15" width="2" height="2" fill="#0f172a"/>
                                                <rect x="8" y="17" width="1" height="2" fill="#0f172a"/>
                                                <rect x="11" y="18" width="2" height="1" fill="#0f172a"/>
                                                <rect x="15" y="17" width="1" height="2" fill="#0f172a"/>
                                                <rect x="18" y="18" width="2" height="1" fill="#0f172a"/>
                                            </svg>
                                        </div>
                                        <p class="text-[0.6rem] text-emerald-300/90 font-semibold tracking-wide">Scan &amp; Pay Any Amount</p>
                                        <div class="w-full rounded-md bg-emerald-600/20 border border-emerald-500/30 py-1 text-center">
                                            <span class="text-[0.6rem] text-emerald-200 font-medium">UPI · GPay · PhonePe · Paytm</span>
                                        </div>
                                    </div>
                                </div>
                                <figcaption class="text-center text-xs text-emerald-200/85 font-medium mt-2">Direct GPay QR</figcaption>
                            </figure>

                            
                            <figure class="m-0 min-w-0 w-full">
                                <div class="rounded-xl overflow-hidden border border-teal-500/30 bg-slate-50 dark:bg-slate-950/50 shadow-lg w-full">
                                    
                                    <div class="bg-gradient-to-r from-teal-700 to-cyan-700 px-3 py-2 flex items-center gap-2">
                                        <div class="h-2 w-2 rounded-full bg-white/40"></div>
                                        <p class="text-[0.55rem] tracking-[0.18em] text-slate-900 dark:text-white/90 font-semibold uppercase">Event PDF · Export</p>
                                    </div>
                                    
                                    <div class="bg-white px-2.5 pt-2.5 pb-2 flex flex-col gap-1.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[0.5rem] font-bold text-slate-700 uppercase tracking-wider">Chandla Ledger</span>
                                            <span class="text-[0.45rem] text-slate-400">Event Report</span>
                                        </div>
                                        <div class="h-px bg-slate-200"></div>
                                        
                                        <?php $__currentLoopData = [['Ramesh Sharma','Cash','₹1,100'],['Sunil Verma','Cover','₹501'],['Priya Patel','Gift','Saree'],['Kavita Joshi','UPI','₹2,000'],['Anil Kumar','Cash','₹500']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex items-center gap-1 text-[0.45rem] text-slate-600">
                                            <span class="w-3 h-3 rounded-full bg-teal-100 border border-teal-300 flex items-center justify-center shrink-0">
                                                <i class="fas fa-user text-teal-600" style="font-size:0.3rem"></i>
                                            </span>
                                            <span class="flex-1 truncate font-medium text-slate-700"><?php echo e($row[0]); ?></span>
                                            <span class="text-slate-400"><?php echo e($row[1]); ?></span>
                                            <span class="font-semibold text-teal-700 ml-auto"><?php echo e($row[2]); ?></span>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <div class="h-px bg-slate-200 mt-0.5"></div>
                                        <div class="flex justify-between text-[0.5rem] font-bold text-slate-800 pt-0.5">
                                            <span>Total</span>
                                            <span class="text-teal-700">₹4,101 + Gift</span>
                                        </div>
                                        <div class="mt-1 rounded bg-teal-600 text-white text-center py-0.5">
                                            <span class="text-[0.5rem] font-semibold tracking-wide">Download PDF &amp; Email</span>
                                        </div>
                                    </div>
                                </div>
                                <figcaption class="text-center text-xs text-emerald-200/85 font-medium mt-2">Full event PDF</figcaption>
                            </figure>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="relative overflow-hidden bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-white">
        <div class="max-w-6xl mx-auto px-6 relative" style="padding-top:5rem;padding-bottom:5rem">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-8">
                <div class="max-w-xl">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-indigo-600 mb-3">Features</p>
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white tracking-tight">Built for real Indian events</h2>
                    <p class="text-slate-600 dark:text-slate-300 mt-3 text-base leading-relaxed">Ledger, guests, social-ready invites, and pre‑wedding graphics — in one calm dashboard.</p>
                </div>
                <a href="<?php echo e(route('client.register')); ?>" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-semibold shadow-lg shadow-indigo-500/25 hover:from-indigo-500 hover:to-violet-500 transition-all shrink-0">
                    <i class="fas fa-sparkles text-sm opacity-90" aria-hidden="true"></i>
                    Create free account
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-7 mt-12">
                <div class="group relative flex flex-col h-full rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white/95 dark:bg-slate-900/50 p-6 md:p-7 shadow-md shadow-slate-200/50 ring-1 ring-white/60 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-500/10 hover:border-indigo-200/70">
                    <div class="absolute inset-x-0 top-0 h-1 rounded-t-2xl bg-gradient-to-r from-indigo-500 via-violet-500 to-purple-500 opacity-90 group-hover:opacity-100" aria-hidden="true"></div>
                    <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 text-white shadow-lg shadow-indigo-500/35">
                        <i class="fas fa-coins text-lg" aria-hidden="true"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 tracking-tight">Cash inventory management</h3>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed flex-1">Track every <strong>₹1 to ₹500</strong> note, opening balance, and change returned.</p>
                </div>
                <div class="group relative flex flex-col h-full rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white/95 dark:bg-slate-900/50 p-6 md:p-7 shadow-md shadow-slate-200/50 ring-1 ring-white/60 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-500/10 hover:border-emerald-200/70">
                    <div class="absolute inset-x-0 top-0 h-1 rounded-t-2xl bg-gradient-to-r from-emerald-500 to-teal-500 opacity-90 group-hover:opacity-100" aria-hidden="true"></div>
                    <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/35">
                        <i class="fas fa-file-pdf text-lg" aria-hidden="true"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 tracking-tight">One‑click PDFs</h3>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed flex-1">Separate pages for <strong>Cash</strong>, <strong>Cover</strong>, and <strong>Gift</strong> with totals and summaries.</p>
                </div>
                <div class="group relative flex flex-col h-full rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white/95 dark:bg-slate-900/50 p-6 md:p-7 shadow-md shadow-slate-200/50 ring-1 ring-white/60 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-violet-500/10 hover:border-violet-200/70">
                    <div class="absolute inset-x-0 top-0 h-1 rounded-t-2xl bg-gradient-to-r from-violet-500 to-fuchsia-500 opacity-90 group-hover:opacity-100" aria-hidden="true"></div>
                    <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 text-white shadow-lg shadow-violet-500/35">
                        <i class="fas fa-qrcode text-lg" aria-hidden="true"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 tracking-tight">QR + UPI proof</h3>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed flex-1">Generate QR, upload <strong>GPay</strong> proof, and verify UPI transactions.</p>
                </div>
                <div class="group relative flex flex-col h-full rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white/95 dark:bg-slate-900/50 p-6 md:p-7 shadow-md shadow-slate-200/50 ring-1 ring-white/60 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-amber-500/10 hover:border-amber-200/70">
                    <div class="absolute inset-x-0 top-0 h-1 rounded-t-2xl bg-gradient-to-r from-amber-500 to-orange-500 opacity-90 group-hover:opacity-100" aria-hidden="true"></div>
                    <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-500/35">
                        <i class="fas fa-users text-lg" aria-hidden="true"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 tracking-tight">Guest &amp; event tracking</h3>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed flex-1"><strong>50</strong> entries total on Free. <strong>Guest Contribution</strong> removes the cap for <strong>one</strong> event; <strong>Host Plus Plan</strong> / <strong>Premium Host Plan</strong> unlock unlimited chandla for <strong>all covered events</strong>. Filters, search, one ledger per event.</p>
                </div>
                <div class="group relative flex flex-col h-full rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white/95 dark:bg-slate-900/50 p-6 md:p-7 shadow-md shadow-slate-200/50 ring-1 ring-white/60 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-rose-500/10 hover:border-rose-200/70">
                    <div class="absolute inset-x-0 top-0 h-1 rounded-t-2xl bg-gradient-to-r from-rose-500 to-pink-500 opacity-90 group-hover:opacity-100" aria-hidden="true"></div>
                    <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 text-white shadow-lg shadow-rose-500/35">
                        <i class="fas fa-envelope-open-text text-lg" aria-hidden="true"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 tracking-tight">Invitation creation</h3>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed flex-1">One shared form powers <strong>10+ print styles</strong> — save PNGs, print, and share a <strong>story‑ready video</strong> for WhatsApp, Instagram &amp; Reels (with the Celebration Plan).</p>
                </div>
                <div class="group relative flex flex-col h-full rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white/95 dark:bg-slate-900/50 p-6 md:p-7 shadow-md shadow-slate-200/50 ring-1 ring-white/60 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-fuchsia-500/10 hover:border-fuchsia-200/70">
                    <div class="absolute inset-x-0 top-0 h-1 rounded-t-2xl bg-gradient-to-r from-fuchsia-500 to-purple-600 opacity-90 group-hover:opacity-100" aria-hidden="true"></div>
                    <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-fuchsia-500 to-purple-600 text-white shadow-lg shadow-fuchsia-500/35">
                        <i class="fas fa-photo-film text-lg" aria-hidden="true"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 tracking-tight">Pre‑wedding image + video</h3>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed flex-1">Upload a photo per countdown milestone, get a <strong>styled card</strong> for each theme, and download <strong>high‑res PNGs</strong>; pair with your invitation <strong>video export</strong> for a full social suite.</p>
                </div>
                <div class="group relative flex flex-col h-full rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white/95 dark:bg-slate-900/50 p-6 md:p-7 shadow-md shadow-slate-200/50 ring-1 ring-white/60 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-cyan-500/10 hover:border-cyan-200/70">
                    <div class="absolute inset-x-0 top-0 h-1 rounded-t-2xl bg-gradient-to-r from-cyan-500 to-slate-600 opacity-90 group-hover:opacity-100" aria-hidden="true"></div>
                    <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-slate-600 to-slate-800 text-white shadow-lg shadow-slate-600/35">
                        <i class="fas fa-lock text-lg" aria-hidden="true"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 tracking-tight">Secure &amp; verified</h3>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed flex-1">Verification controls for payments and <strong>audit-ready</strong> reports.</p>
                </div>
                <div class="group relative flex flex-col h-full rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white/95 dark:bg-slate-900/50 p-6 md:p-7 shadow-md shadow-slate-200/50 ring-1 ring-white/60 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-500/10 hover:border-indigo-200/70">
                    <div class="absolute inset-x-0 top-0 h-1 rounded-t-2xl bg-gradient-to-r from-indigo-500 via-violet-500 to-purple-500 opacity-90 group-hover:opacity-100" aria-hidden="true"></div>
                    <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 text-white shadow-lg shadow-indigo-500/35">
                        <i class="fas fa-user-shield text-lg" aria-hidden="true"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 tracking-tight">Multi‑Admin Access</h3>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed flex-1">Add trusted family members to safely help log cash entries simultaneously from different counters during rush hour.</p>
                </div>
                <div class="group relative flex flex-col h-full rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white/95 dark:bg-slate-900/50 p-6 md:p-7 shadow-md shadow-slate-200/50 ring-1 ring-white/60 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-rose-500/10 hover:border-rose-200/70">
                    <div class="absolute inset-x-0 top-0 h-1 rounded-t-2xl bg-gradient-to-r from-rose-500 to-pink-500 opacity-90 group-hover:opacity-100" aria-hidden="true"></div>
                    <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 text-white shadow-lg shadow-rose-500/35">
                        <i class="fas fa-chart-pie text-lg" aria-hidden="true"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 tracking-tight">Analytics &amp; Insights</h3>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed flex-1">Get real-time insights into top contributors, cash vs UPI breakdowns, and beautifully visualized collection metrics.</p>
                </div>
            </div>
        </div>
    </section>

    
    <section class="bg-white dark:bg-slate-900 border-y border-slate-200 dark:border-white/10 py-10 overflow-hidden">
        <div class="max-w-6xl mx-auto px-6 mb-8 text-center">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-indigo-400 mb-2">Trusted By</p>
            <h2 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">Hosts across India use Chandla Book</h2>
        </div>
        <style>
            .cb-marquee-track {
                display: flex;
                width: max-content;
                animation: cb-marquee-scroll 28s linear infinite;
            }
            .cb-marquee-track:hover { animation-play-state: paused; }
            @keyframes cb-marquee-scroll {
                0%   { transform: translateX(0); }
                100% { transform: translateX(-50%); }
            }
            .cb-marquee-logo {
                display: inline-flex;
                align-items: center;
                gap: 0.6rem;
                padding: 0.6rem 1.5rem;
                margin: 0 0.75rem;
                border-radius: 999px;
                
                
                white-space: nowrap;
                filter: grayscale(100%) opacity(0.55);
                transition: filter 0.3s ease, background 0.3s ease;
                cursor: default;
            }
            .cb-marquee-logo:hover {
                filter: grayscale(0%) opacity(1);
                
            }
            .cb-marquee-logo-icon {
                width: 2rem; height: 2rem;
                border-radius: 8px;
                display: flex; align-items: center; justify-content: center;
                font-weight: 800; font-size: 0.75rem; color: #fff;
                flex-shrink: 0;
            }
            .cb-marquee-logo-name { font-size: 0.85rem; font-weight: 600;  letter-spacing: 0.01em; }
        </style>
        <div class="relative">
            
            <div class="pointer-events-none absolute left-0 top-0 h-full w-16 z-10 bg-gradient-to-r from-white dark:from-slate-900 to-transparent" aria-hidden="true"></div>
            <div class="pointer-events-none absolute right-0 top-0 h-full w-16 z-10 bg-gradient-to-l from-white dark:from-slate-900 to-transparent" aria-hidden="true"></div>
            <div class="overflow-hidden" aria-label="Companies using Chandla Book" role="list">
                <div class="cb-marquee-track">
                    <?php
                    $cbLogos = [
                        ['name' => 'Mehta Events',    'bg' => '#4f46e5', 'init' => 'ME'],
                        ['name' => 'Sharma Caterers', 'bg' => '#0891b2', 'init' => 'SC'],
                        ['name' => 'Patel Mandaps',   'bg' => '#059669', 'init' => 'PM'],
                        ['name' => 'Royal Occasions', 'bg' => '#b45309', 'init' => 'RO'],
                        ['name' => 'Gupta Decors',    'bg' => '#7c3aed', 'init' => 'GD'],
                        ['name' => 'Joshi Weddings',  'bg' => '#db2777', 'init' => 'JW'],
                        ['name' => 'Verma & Sons',    'bg' => '#0f766e', 'init' => 'VS'],
                        ['name' => 'Agarwal Banquets','bg' => '#c2410c', 'init' => 'AB'],
                    ];
                    ?>
                    <?php $__currentLoopData = array_merge($cbLogos, $cbLogos); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $logo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="cb-marquee-logo bg-slate-100 border border-slate-200 hover:bg-slate-200 dark:bg-white/5 dark:border-white/10 dark:hover:bg-white/10" role="listitem">
                            <span class="cb-marquee-logo-icon" style="background:<?php echo e($logo['bg']); ?>"><?php echo e($logo['init']); ?></span>
                            <span class="cb-marquee-logo-name text-slate-600 dark:text-slate-200"><?php echo e($logo['name']); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </section>

    
    <section class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white border-t border-slate-200 dark:border-white/10" style="padding-top:5rem;padding-bottom:5rem">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center" style="margin-bottom:5rem">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-indigo-400 mb-4">Testimonials</p>
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-5">Loved by hosts across India</h2>
                <p class="text-slate-900 dark:text-white/60 text-lg max-w-xl mx-auto">See how Chandla Book is transforming event collections.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 p-8 rounded-2xl shadow-lg relative hover:-translate-y-1 transition-transform duration-300">
                    <div class="text-amber-400 mb-4 text-sm flex gap-1">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="text-slate-900 dark:text-white/80 italic mb-6">"Managing chandla during my brother's wedding used to be a headache with notebooks. This app made it so simple to track every envelope and UPI payment. The PDF export saved us hours of calculation!"</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-600/70 text-indigo-100 flex items-center justify-center font-bold">R</div>
                        <div>
                            <div class="font-bold text-slate-900 dark:text-white text-sm">Rajesh Kumar</div>
                            <div class="text-xs text-slate-900 dark:text-white/50">Delhi</div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 p-8 rounded-2xl shadow-lg relative hover:-translate-y-1 transition-transform duration-300">
                    <div class="text-amber-400 mb-4 text-sm flex gap-1">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="text-slate-900 dark:text-white/80 italic mb-6">"The Direct GPay feature is a game-changer. Guests could just scan and pay directly to my UPI, and it auto-updated in the ledger. No more matching transaction screenshots manually."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-600/70 text-emerald-100 flex items-center justify-center font-bold">S</div>
                        <div>
                            <div class="font-bold text-slate-900 dark:text-white text-sm">Sneha Patel</div>
                            <div class="text-xs text-slate-900 dark:text-white/50">Ahmedabad</div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 p-8 rounded-2xl shadow-lg relative hover:-translate-y-1 transition-transform duration-300">
                    <div class="text-amber-400 mb-4 text-sm flex gap-1">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="text-slate-900 dark:text-white/80 italic mb-6">"I got the Celebration Plan for my daughter's reception. The invitation designs were stunning, and sharing them on WhatsApp was incredibly easy. Highly recommend for any Indian function."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-rose-600/70 text-rose-100 flex items-center justify-center font-bold">A</div>
                        <div>
                            <div class="font-bold text-slate-900 dark:text-white text-sm">Amit Sharma</div>
                            <div class="text-xs text-slate-900 dark:text-white/50">Mumbai</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white dark:bg-slate-950 text-slate-900 dark:text-white">
        <div class="max-w-6xl mx-auto px-6 py-12">
            <h2 class="text-3xl font-bold mb-6 text-slate-900 dark:text-white">How it works</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <div class="text-sm text-slate-500 dark:text-slate-400">Step 01</div>
                    <h3 class="font-semibold mb-2 text-slate-900 dark:text-white">Create Event</h3>
                    <p class="text-slate-600 dark:text-slate-300">Add date, venue, and event type in seconds.</p>
                </div>
                <div class="p-6 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <div class="text-sm text-slate-500 dark:text-slate-400">Step 02</div>
                    <h3 class="font-semibold mb-2 text-slate-900 dark:text-white">Collect Contributions</h3>
                    <p class="text-slate-600 dark:text-slate-300">Record Cash/Cover/Gift with exact note quantities.</p>
                </div>
                <div class="p-6 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                    <div class="text-sm text-slate-500 dark:text-slate-400">Step 03</div>
                    <h3 class="font-semibold mb-2 text-slate-900 dark:text-white">Report &amp; export</h3>
                    <p class="text-slate-600 dark:text-slate-300">Download event PDFs, email reports, and optional <strong>Direct GPay</strong> guest links when your plan includes them.</p>
                </div>
            </div>
        </div>
    </section>


    <section id="refer" class="bg-white dark:bg-slate-950 text-slate-900 dark:text-white scroll-mt-20" style="padding-top:2rem;">
        <div class="max-w-6xl mx-auto px-6 py-12">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <h2 class="text-3xl font-bold mb-2 text-slate-900 dark:text-white">Refer &amp; earn</h2>
                <p class="text-slate-600 dark:text-slate-300">When someone signs up with <strong>your code</strong> and later <strong>completes a qualifying payment</strong> (e.g. an upgrade in the app), you earn a reward — see below.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">
                <div class="rounded-2xl border-2 border-indigo-200 dark:border-indigo-800 bg-indigo-50/50 dark:bg-indigo-900/20 p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-12 w-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow">
                            <i class="fas fa-gift" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">What you receive</h3>
                    </div>
                    <p class="text-slate-700 dark:text-slate-300 leading-relaxed">If a <strong>referred user makes a payment</strong> (as defined in the app), the <strong>referrer is credited with <span class="text-indigo-700">1 free event</span></strong> that includes:</p>
                    <ul class="mt-4 space-y-2 text-slate-800 dark:text-slate-300 text-sm list-none">
                        <li class="flex gap-2"><i class="fas fa-check text-indigo-600 mt-0.5 shrink-0" aria-hidden="true"></i><span><strong>Unlimited chandla entries</strong> for that event (same class as a paid “unlimited” event)</span></li>
                        <li class="flex gap-2"><i class="fas fa-check text-indigo-600 mt-0.5 shrink-0" aria-hidden="true"></i><span><strong>Full event PDF</strong> — export, download, and email the complete report (per app support)</span></li>
                    </ul>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-4">Eligibility, timing, and which payments count are confirmed in the app and may change; this is a plain-language summary.</p>
                </div>
                <div class="rounded-2xl border-2 border-indigo-200 dark:border-indigo-800 bg-indigo-50/50 dark:bg-indigo-900/20 p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-12 w-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow shrink-0">
                            <i class="fas fa-share-nodes" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Your referral code (after sign‑up)</h3>
                    </div>
                    <p class="text-slate-700 dark:text-slate-300 leading-relaxed mb-6">Log in to the client area — your code appears on the <strong>dashboard</strong> for you to copy and share.</p>
                    <div class="rounded-xl bg-white/60 dark:bg-slate-900/60 p-5 border border-dashed border-indigo-300 dark:border-indigo-700 text-center shadow-sm">
                        <div class="text-xs text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-widest mb-2">Example format</div>
                        <div class="font-mono text-2xl text-indigo-900 dark:text-indigo-300 font-bold tracking-wider">REF-8K2P7QX</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php echo $__env->make('partials.faq-section', ['faqFlush' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <section class="bg-indigo-600 text-white">
        <div class="max-w-6xl mx-auto px-6 py-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h2 class="text-3xl font-bold">Create your first event today</h2>
                <p class="text-indigo-100 mt-2">Free: 1 event · 50 entries · Paid: Celebration ₹<?php echo e(number_format((float) config('packs.celebration.amount_inr', 300), 0)); ?> · Guest Contribution (1 event) ₹<?php echo e(number_format((float) config('packs.guest_pay_single.amount_inr', 400), 0)); ?> · Host Plus Plan ₹<?php echo e(number_format((float) config('packs.ledger_duo.amount_inr', 500), 0)); ?> · Family Plan ₹<?php echo e(number_format((float) config('packs.family.amount_inr', 600), 0)); ?> · Premium Host Plan ₹<?php echo e(number_format((float) config('packs.premium_bundle.amount_inr', 700), 0)); ?> · Professional Plan ₹<?php echo e(number_format((float) config('packs.professional.amount_inr', 999), 0)); ?>. <a href="#faq" class="text-slate-900 dark:text-white font-medium underline decoration-white/50 hover:decoration-white">Read FAQs</a></p>
            </div>
            <a href="<?php echo e(route('client.register')); ?>" class="shrink-0 px-6 py-3 bg-white text-indigo-700 rounded-lg font-semibold shadow-lg hover:bg-indigo-50 transition-colors">
                Create free account
            </a>
        </div>
    </section>

    <footer class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white/70 border-t border-slate-200 dark:border-white/10">
        <div class="max-w-6xl mx-auto px-6 py-6 text-sm flex flex-col md:flex-row justify-between">
            <div>© <?php echo e(date('Y')); ?> Chandla Book. All rights reserved.</div>
            <div class="flex flex-wrap gap-4 justify-end">
                <a href="#features" class="hover:text-slate-900 dark:hover:text-white">Features</a>
                <a href="#pricing" class="hover:text-slate-900 dark:hover:text-white">Plans</a>
                <a href="#faq" class="hover:text-slate-900 dark:hover:text-white">FAQ</a>
                <a href="<?php echo e(route('public.contact')); ?>" class="hover:text-slate-900 dark:hover:text-white">Contact</a>
                <a href="<?php echo e(route('public.privacy')); ?>" class="hover:text-slate-900 dark:hover:text-white">Privacy</a>
                <a href="<?php echo e(route('public.terms')); ?>" class="hover:text-slate-900 dark:hover:text-white">Terms</a>
                <a href="<?php echo e(route('client.login')); ?>" class="hover:text-slate-900 dark:hover:text-white">Login</a>
                <a href="<?php echo e(route('client.register')); ?>" class="hover:text-slate-900 dark:hover:text-white">Register</a>
            </div>
        </div>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('themeToggleBtn');
            const icon = document.getElementById('themeToggleIcon');
            const html = document.documentElement;

            btn.addEventListener('click', () => {
                html.classList.toggle('dark');
                if (html.classList.contains('dark')) {
                    icon.classList.remove('fa-moon');
                    icon.classList.add('fa-sun');
                } else {
                    icon.classList.remove('fa-sun');
                    icon.classList.add('fa-moon');
                }
            });
        });
    </script>
</body>
</html>
<?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/public/home.blade.php ENDPATH**/ ?>