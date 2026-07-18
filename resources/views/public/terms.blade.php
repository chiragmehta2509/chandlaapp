@php
    $seoTitle = 'Terms & Conditions — Chandla Book';
    $seoDesc = 'Terms and Conditions for Chandla Book.';
    $seoCanonical = url('/terms');
    $seoRobots = 'index, follow';
@endphp
@extends('layouts.public-guest')

@section('content')
<div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-16 lg:pt-12 lg:pb-24">
    
    <!-- Background Accents -->
    <div class="absolute inset-x-0 top-[-10rem] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[-20rem]" aria-hidden="true">
        <div class="relative left-1/2 -z-10 aspect-[1155/678] w-[36.125rem] max-w-none -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#80ffb5] to-[#89a2fc] opacity-20 dark:opacity-30 sm:left-[calc(50%-40rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
    </div>

    <!-- Hero Section -->
    <div class="text-center max-w-3xl mx-auto mb-8 lg:mb-12 relative z-10">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 text-indigo-700 dark:text-indigo-300 text-xs font-bold uppercase tracking-widest mb-4 shadow-sm">
            <i class="fas fa-file-signature"></i> Legal Hub
        </div>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-6">Terms & Conditions</h1>
        <p class="text-base sm:text-lg text-slate-500 dark:text-slate-400 font-medium">Effective Date: 19 Jun 2026</p>
    </div>

    <!-- Layout Split -->
    <div class="flex flex-col lg:flex-row gap-8 lg:gap-12 relative">
        
        <!-- Sidebar TOC -->
        <aside class="lg:w-1/4 flex-shrink-0 hidden lg:block">
            <div class="sticky top-28 bg-white/50 dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-2xl p-6 shadow-sm">
                <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-widest mb-6">Table of Contents</h3>
                <nav class="space-y-1.5" id="toc-nav">
                    <a href="#section-1" class="block py-2 px-3 text-sm text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-white/5 rounded-lg transition-all font-medium border-l-2 border-transparent hover:border-indigo-500">1. Acceptance</a>
                    <a href="#section-2" class="block py-2 px-3 text-sm text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-white/5 rounded-lg transition-all font-medium border-l-2 border-transparent hover:border-indigo-500">2. Services</a>
                    <a href="#section-3" class="block py-2 px-3 text-sm text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-white/5 rounded-lg transition-all font-medium border-l-2 border-transparent hover:border-indigo-500">3. User Responsibilities</a>
                    <a href="#section-4" class="block py-2 px-3 text-sm text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-white/5 rounded-lg transition-all font-medium border-l-2 border-transparent hover:border-indigo-500">4. Account Security</a>
                    <a href="#section-5" class="block py-2 px-3 text-sm text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-white/5 rounded-lg transition-all font-medium border-l-2 border-transparent hover:border-indigo-500">5. Payments</a>
                    <a href="#section-6" class="block py-2 px-3 text-sm text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-white/5 rounded-lg transition-all font-medium border-l-2 border-transparent hover:border-indigo-500">6. Direct UPI Payments</a>
                    <a href="#section-7" class="block py-2 px-3 text-sm text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-white/5 rounded-lg transition-all font-medium border-l-2 border-transparent hover:border-indigo-500">7. User Content</a>
                    <a href="#section-8" class="block py-2 px-3 text-sm text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-white/5 rounded-lg transition-all font-medium border-l-2 border-transparent hover:border-indigo-500">8. Prohibited Activities</a>
                    <a href="#section-9" class="block py-2 px-3 text-sm text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-white/5 rounded-lg transition-all font-medium border-l-2 border-transparent hover:border-indigo-500">9. Intellectual Property</a>
                    <a href="#section-10" class="block py-2 px-3 text-sm text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-white/5 rounded-lg transition-all font-medium border-l-2 border-transparent hover:border-indigo-500">10. Service Availability</a>
                    <a href="#section-11" class="block py-2 px-3 text-sm text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-white/5 rounded-lg transition-all font-medium border-l-2 border-transparent hover:border-indigo-500">11. Limitation of Liability</a>
                    <a href="#section-12" class="block py-2 px-3 text-sm text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-white/5 rounded-lg transition-all font-medium border-l-2 border-transparent hover:border-indigo-500">12. Suspensions</a>
                    <a href="#section-13" class="block py-2 px-3 text-sm text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-white/5 rounded-lg transition-all font-medium border-l-2 border-transparent hover:border-indigo-500">13. Governing Law</a>
                    <a href="#section-14" class="block py-2 px-3 text-sm text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-white/5 rounded-lg transition-all font-medium border-l-2 border-transparent hover:border-indigo-500">14. Contact</a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:w-3/4">
            <div class="bg-white dark:bg-slate-900/40 backdrop-blur-2xl border border-slate-200 dark:border-white/10 rounded-3xl p-8 sm:p-12 shadow-xl shadow-slate-200/50 dark:shadow-none">
                
                <section id="section-1" class="mb-16 scroll-mt-32">
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-white/5 pb-4">1. Acceptance</h2>
                    <p class="text-base lg:text-lg leading-relaxed text-slate-600 dark:text-slate-300 mb-6">By accessing or using <strong class="text-slate-900 dark:text-white">Chandla Book</strong> through Web, Android, or iOS applications, you agree to be bound by these Terms and Conditions.</p>
                </section>

                <section id="section-2" class="mb-16 scroll-mt-32">
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white mb-8 border-b border-slate-100 dark:border-white/5 pb-4">2. Services</h2>
                    <p class="text-base lg:text-lg leading-relaxed text-slate-600 dark:text-slate-300 mb-6">Chandla Book provides a suite of event and financial management tools:</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10">
                            <i class="fas fa-calendar-check text-indigo-500"></i> <span class="text-slate-700 dark:text-slate-300 font-medium">Event Management</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10">
                            <i class="fas fa-hand-holding-dollar text-emerald-500"></i> <span class="text-slate-700 dark:text-slate-300 font-medium">Chandla Tracking</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10">
                            <i class="fas fa-users text-blue-500"></i> <span class="text-slate-700 dark:text-slate-300 font-medium">Guest Management</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10">
                            <i class="fas fa-qrcode text-purple-500"></i> <span class="text-slate-700 dark:text-slate-300 font-medium">QR Payments</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10">
                            <i class="fas fa-mobile-screen text-teal-500"></i> <span class="text-slate-700 dark:text-slate-300 font-medium">UPI Collection</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10">
                            <i class="fas fa-envelope-open-text text-amber-500"></i> <span class="text-slate-700 dark:text-slate-300 font-medium">Invitations</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10">
                            <i class="fas fa-chart-pie text-rose-500"></i> <span class="text-slate-700 dark:text-slate-300 font-medium">Analytics & Reports</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10">
                            <i class="fas fa-user-shield text-slate-500"></i> <span class="text-slate-700 dark:text-slate-300 font-medium">Multi-Admin Features</span>
                        </div>
                    </div>
                </section>

                <section id="section-3" class="mb-16 scroll-mt-32">
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white mb-8 border-b border-slate-100 dark:border-white/5 pb-4">3. User Responsibilities</h2>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start gap-4 text-base lg:text-lg text-slate-600 dark:text-slate-300">
                            <span class="flex-shrink-0 mt-1 w-6 h-6 flex items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400"><i class="fas fa-check text-[10px]"></i></span>
                            Provide accurate and truthful information.
                        </li>
                        <li class="flex items-start gap-4 text-base lg:text-lg text-slate-600 dark:text-slate-300">
                            <span class="flex-shrink-0 mt-1 w-6 h-6 flex items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400"><i class="fas fa-check text-[10px]"></i></span>
                            Maintain account security and confidentiality.
                        </li>
                        <li class="flex items-start gap-4 text-base lg:text-lg text-slate-600 dark:text-slate-300">
                            <span class="flex-shrink-0 mt-1 w-6 h-6 flex items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400"><i class="fas fa-check text-[10px]"></i></span>
                            Use the platform legally and ethically.
                        </li>
                        <li class="flex items-start gap-4 text-base lg:text-lg text-slate-600 dark:text-slate-300">
                            <span class="flex-shrink-0 mt-1 w-6 h-6 flex items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400"><i class="fas fa-check text-[10px]"></i></span>
                            Respect the intellectual property and rights of third parties.
                        </li>
                    </ul>
                </section>

                <section id="section-4" class="mb-16 scroll-mt-32">
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-white/5 pb-4">4. Account Security</h2>
                    <p class="text-base lg:text-lg leading-relaxed text-slate-600 dark:text-slate-300 mb-6">Users are entirely responsible for all activities performed through their accounts. You must notify us immediately of any unauthorized use or security breach.</p>
                </section>

                <section id="section-5" class="mb-16 scroll-mt-32">
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white mb-8 border-b border-slate-100 dark:border-white/5 pb-4">5. Payments</h2>
                    <p class="text-base lg:text-lg leading-relaxed text-slate-600 dark:text-slate-300 mb-6">Payments may be processed through Razorpay or other authorized providers.</p>
                    <div class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 rounded-xl p-6 mb-6">
                        <p class="text-base font-medium text-emerald-900 dark:text-emerald-200"><i class="fas fa-shield-check mr-2"></i> We do not store sensitive payment data.</p>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start gap-4 text-base lg:text-lg text-slate-600 dark:text-slate-300">
                            <span class="flex-shrink-0 mt-1 w-6 h-6 flex items-center justify-center rounded-full bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-slate-400"><i class="fas fa-times text-[10px]"></i></span> Card Numbers
                        </li>
                        <li class="flex items-start gap-4 text-base lg:text-lg text-slate-600 dark:text-slate-300">
                            <span class="flex-shrink-0 mt-1 w-6 h-6 flex items-center justify-center rounded-full bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-slate-400"><i class="fas fa-times text-[10px]"></i></span> CVV Numbers
                        </li>
                        <li class="flex items-start gap-4 text-base lg:text-lg text-slate-600 dark:text-slate-300">
                            <span class="flex-shrink-0 mt-1 w-6 h-6 flex items-center justify-center rounded-full bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-slate-400"><i class="fas fa-times text-[10px]"></i></span> UPI PINs
                        </li>
                        <li class="flex items-start gap-4 text-base lg:text-lg text-slate-600 dark:text-slate-300">
                            <span class="flex-shrink-0 mt-1 w-6 h-6 flex items-center justify-center rounded-full bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-slate-400"><i class="fas fa-times text-[10px]"></i></span> Banking Passwords
                        </li>
                    </ul>
                </section>

                <section id="section-6" class="mb-16 scroll-mt-32">
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white mb-8 border-b border-slate-100 dark:border-white/5 pb-4">6. Direct UPI Payments</h2>
                    <p class="text-base lg:text-lg leading-relaxed text-slate-600 dark:text-slate-300 mb-6">Users are responsible for ensuring the accuracy of:</p>
                    <div class="flex flex-wrap gap-3 mb-6">
                        <span class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-700 dark:text-slate-300">UPI IDs</span>
                        <span class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-700 dark:text-slate-300">QR Codes</span>
                        <span class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-700 dark:text-slate-300">Payment Verification</span>
                    </div>
                    <p class="text-base lg:text-lg leading-relaxed text-slate-600 dark:text-slate-300 mb-6 font-medium">Chandla Book does not act as a bank or payment institution. We only facilitate the tracking of records.</p>
                </section>

                <section id="section-7" class="mb-16 scroll-mt-32">
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white mb-8 border-b border-slate-100 dark:border-white/5 pb-4">7. User Content</h2>
                    <p class="text-base lg:text-lg leading-relaxed text-slate-600 dark:text-slate-300 mb-6">Users retain full ownership of their content, including Photos, Invitations, Videos, and Event Data.</p>
                    <p class="text-base lg:text-lg leading-relaxed text-slate-600 dark:text-slate-300 mb-6">By uploading, you grant Chandla Book permission to store and process this content solely for service functionality and optimization.</p>
                </section>

                <section id="section-8" class="mb-16 scroll-mt-32">
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white mb-8 border-b border-slate-100 dark:border-white/5 pb-4">8. Prohibited Activities</h2>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start gap-4 text-base lg:text-lg text-slate-600 dark:text-slate-300">
                            <span class="flex-shrink-0 mt-1 w-6 h-6 flex items-center justify-center rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400"><i class="fas fa-ban text-[10px]"></i></span>
                            Commit fraud or impersonate others.
                        </li>
                        <li class="flex items-start gap-4 text-base lg:text-lg text-slate-600 dark:text-slate-300">
                            <span class="flex-shrink-0 mt-1 w-6 h-6 flex items-center justify-center rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400"><i class="fas fa-ban text-[10px]"></i></span>
                            Upload illegal or highly offensive content.
                        </li>
                        <li class="flex items-start gap-4 text-base lg:text-lg text-slate-600 dark:text-slate-300">
                            <span class="flex-shrink-0 mt-1 w-6 h-6 flex items-center justify-center rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400"><i class="fas fa-ban text-[10px]"></i></span>
                            Distribute malware or attempt unauthorized system access.
                        </li>
                        <li class="flex items-start gap-4 text-base lg:text-lg text-slate-600 dark:text-slate-300">
                            <span class="flex-shrink-0 mt-1 w-6 h-6 flex items-center justify-center rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400"><i class="fas fa-ban text-[10px]"></i></span>
                            Abuse payment systems or game features.
                        </li>
                    </ul>
                </section>

                <section id="section-9" class="mb-16 scroll-mt-32">
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-white/5 pb-4">9. Intellectual Property</h2>
                    <p class="text-base lg:text-lg leading-relaxed text-slate-600 dark:text-slate-300 mb-6">All software, branding, source code, designs, logos, and platform assets remain the exclusive property of Skylight Tech. Unauthorized reproduction is strictly prohibited.</p>
                </section>

                <section id="section-10" class="mb-16 scroll-mt-32">
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-white/5 pb-4">10. Service Availability</h2>
                    <p class="text-base lg:text-lg leading-relaxed text-slate-600 dark:text-slate-300 mb-6">Services are provided on an "as available" basis. We strive for 99% uptime, but we do not guarantee completely uninterrupted service.</p>
                </section>

                <section id="section-11" class="mb-16 scroll-mt-32">
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white mb-8 border-b border-slate-100 dark:border-white/5 pb-4">11. Limitation of Liability</h2>
                    <p class="text-base lg:text-lg leading-relaxed text-slate-600 dark:text-slate-300 mb-6">Skylight Tech shall not be liable for:</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10 text-slate-700 dark:text-slate-300"><i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i> User-entered data errors</div>
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10 text-slate-700 dark:text-slate-300"><i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i> Payment disputes</div>
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10 text-slate-700 dark:text-slate-300"><i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i> Banking failures</div>
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10 text-slate-700 dark:text-slate-300"><i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i> Data loss from user actions</div>
                    </div>
                </section>

                <section id="section-12" class="mb-16 scroll-mt-32">
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white mb-8 border-b border-slate-100 dark:border-white/5 pb-4">12. Suspension and Termination</h2>
                    <p class="text-base lg:text-lg leading-relaxed text-slate-600 dark:text-slate-300 mb-6">Accounts may be suspended or permanently terminated for policy violations, fraudulent activity, or security threats at our sole discretion.</p>
                </section>

                <section id="section-13" class="mb-16 scroll-mt-32">
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-white/5 pb-4">13. Governing Law</h2>
                    <p class="text-base lg:text-lg leading-relaxed text-slate-600 dark:text-slate-300 mb-6">These Terms are governed by the laws of India. Jurisdiction shall be Surat, Gujarat, India.</p>
                </section>

                <section id="section-14" class="mb-0 scroll-mt-32">
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white mb-8 border-b border-slate-100 dark:border-white/5 pb-4">14. Contact Us</h2>
                    <div class="bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl p-8 text-center sm:text-left flex flex-col sm:flex-row items-center gap-6">
                        <div class="w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-envelope-open-text text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Skylight Tech</h4>
                            <p class="text-slate-600 dark:text-slate-400 mb-4 text-base">If you have any questions or concerns regarding these Terms, please reach out to us.</p>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <a href="mailto:chandlabook@gmail.com" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition shadow-sm">
                                    <i class="fas fa-envelope"></i> chandlabook@gmail.com
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('#toc-nav a');
    const sections = Array.from(navLinks).map(link => document.querySelector(link.getAttribute('href')));
    
    // Smooth scrolling
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            if(targetSection) {
                window.scrollTo({
                    top: targetSection.offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Intersection Observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                navLinks.forEach(link => {
                    if(link.getAttribute('href') === `#${id}`) {
                        link.classList.add('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400', 'bg-slate-50', 'dark:bg-white/5');
                        link.classList.remove('border-transparent', 'text-slate-600', 'dark:text-slate-400');
                    } else {
                        link.classList.remove('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400', 'bg-slate-50', 'dark:bg-white/5');
                        link.classList.add('border-transparent', 'text-slate-600', 'dark:text-slate-400');
                    }
                });
            }
        });
    }, { rootMargin: '-20% 0px -70% 0px' });

    sections.forEach(section => {
        if(section) observer.observe(section);
    });
});
</script>
@endpush
@endsection
