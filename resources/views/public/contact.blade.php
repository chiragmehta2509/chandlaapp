@php
    $seoTitle = 'Contact us — Chandla Book';
    $seoDesc = 'Contact Chandla Book — phone and email for billing, Razorpay packs, and product questions.';
    $seoCanonical = url('/contact');
    $seoRobots = 'index, follow';
@endphp
@extends('layouts.public-guest')

@section('omit_footer_marketing_link')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('cb-dark-mode-toggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
    }
});
</script>
@endpush
@endsection

@section('content')
@php
    $supportEmail = trim((string) config('chandlabook.support_email', ''));
    if ($supportEmail === '') {
        $supportEmail = trim((string) config('mail.from.address', ''));
    }
    $supportPhone = trim((string) config('chandlabook.support_phone', ''));
    $phoneDigits = $supportPhone !== '' ? preg_replace('/\D/', '', $supportPhone) : '';
    $telUri = $phoneDigits !== '' ? 'tel:+' . $phoneDigits : '#';
    $hasContact = true;
@endphp

<div class="max-w-4xl mx-auto pt-2">
    <p class="inline-flex items-center gap-2 rounded-full bg-white dark:bg-white/10 px-3 py-1 text-[0.65rem] font-bold uppercase tracking-[0.18em] text-slate-800 dark:text-white/90 ring-1 ring-slate-200 dark:ring-white/15 mb-4">
        <i class="fa-solid fa-headset text-amber-300" aria-hidden="true"></i>
        We're here to help
    </p>
    <h1 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white tracking-tight mb-3">
        Contact us
    </h1>
    <p class="text-slate-700 dark:text-white/75 text-sm sm:text-base leading-relaxed mb-8">
        Billing, Razorpay packs, ledger questions, or invites — reach us on phone or email and we’ll point you in the right direction.
    </p>

    @if($hasContact)
        <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white/[0.04] backdrop-blur-md p-6 sm:p-8 shadow-2xl shadow-black/30">
            <p class="text-center text-xs font-bold uppercase tracking-[0.25em] text-slate-500 dark:text-white/60 mb-8">Get in touch</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($supportPhone !== '')
                <a href="{{ $telUri }}" class="group flex items-center justify-between gap-4 rounded-xl border border-slate-200 dark:border-white/5 bg-white/[0.03] p-4 sm:p-5 transition-all duration-300 hover:bg-white/[0.08] hover:border-slate-200 dark:border-white/10 hover:shadow-lg hover:shadow-indigo-950/20">
                    <div class="flex items-center gap-4">
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 text-slate-900 dark:text-white shadow-lg shadow-orange-950/40">
                            <i class="fa-solid fa-phone text-xl" aria-hidden="true"></i>
                        </span>
                        <div>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-white/50 mb-0.5">Call us</span>
                            <span class="block text-base sm:text-lg font-bold text-slate-900 dark:text-white tracking-wide">{{ $supportPhone }}</span>
                            <span class="mt-1 block text-xs font-semibold text-emerald-400">Tap to dial</span>
                        </div>
                    </div>
                    <span class="text-slate-300 dark:text-white/20 transition-colors group-hover:text-slate-500 dark:text-white/60 mr-1">
                        <i class="fa-solid fa-chevron-right text-sm" aria-hidden="true"></i>
                    </span>
                </a>
                @endif

                @if($supportEmail !== '')
                <a href="mailto:{{ $supportEmail }}" class="group flex items-center justify-between gap-4 rounded-xl border border-slate-200 dark:border-white/5 bg-white/[0.03] p-4 sm:p-5 transition-all duration-300 hover:bg-white/[0.08] hover:border-slate-200 dark:border-white/10 hover:shadow-lg hover:shadow-indigo-950/20">
                    <div class="flex items-center gap-4">
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-slate-900 dark:text-white shadow-lg shadow-blue-950/40">
                            <i class="fa-solid fa-envelope text-xl" aria-hidden="true"></i>
                        </span>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-white/50 mb-0.5">Email</span>
                            <span class="block text-base sm:text-lg font-bold text-slate-900 dark:text-white tracking-wide truncate max-w-[180px] sm:max-w-xs">{{ $supportEmail }}</span>
                            <span class="mt-1 block text-xs font-semibold text-emerald-400">Opens your mail app</span>
                        </div>
                    </div>
                    <span class="text-slate-300 dark:text-white/20 transition-colors group-hover:text-slate-500 dark:text-white/60 mr-1">
                        <i class="fa-solid fa-chevron-right text-sm" aria-hidden="true"></i>
                    </span>
                </a>
                @endif

                <a href="tel:+918200067737" class="group flex items-center justify-between gap-4 rounded-xl border border-slate-200 dark:border-white/5 bg-white/[0.03] p-4 sm:p-5 transition-all duration-300 hover:bg-white/[0.08] hover:border-slate-200 dark:border-white/10 hover:shadow-lg hover:shadow-indigo-950/20">
                    <div class="flex items-center gap-4">
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 text-slate-900 dark:text-white shadow-lg shadow-orange-950/40">
                            <i class="fa-solid fa-phone text-xl" aria-hidden="true"></i>
                        </span>
                        <div>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-white/50 mb-0.5">Call us (Alternative)</span>
                            <span class="block text-base sm:text-lg font-bold text-slate-900 dark:text-white tracking-wide">+91 8200067737</span>
                            <span class="mt-1 block text-xs font-semibold text-emerald-400">Tap to dial</span>
                        </div>
                    </div>
                    <span class="text-slate-300 dark:text-white/20 transition-colors group-hover:text-slate-500 dark:text-white/60 mr-1">
                        <i class="fa-solid fa-chevron-right text-sm" aria-hidden="true"></i>
                    </span>
                </a>

                <a href="mailto:info.ksky@gmail.com" class="group flex items-center justify-between gap-4 rounded-xl border border-slate-200 dark:border-white/5 bg-white/[0.03] p-4 sm:p-5 transition-all duration-300 hover:bg-white/[0.08] hover:border-slate-200 dark:border-white/10 hover:shadow-lg hover:shadow-indigo-950/20">
                    <div class="flex items-center gap-4">
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-slate-900 dark:text-white shadow-lg shadow-blue-950/40">
                            <i class="fa-solid fa-envelope text-xl" aria-hidden="true"></i>
                        </span>
                        <div class="min-w-0">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-white/50 mb-0.5">Email (Alternative)</span>
                            <span class="block text-base sm:text-lg font-bold text-slate-900 dark:text-white tracking-wide truncate max-w-[180px] sm:max-w-xs">info.ksky@gmail.com</span>
                            <span class="mt-1 block text-xs font-semibold text-emerald-400">Opens your mail app</span>
                        </div>
                    </div>
                    <span class="text-slate-300 dark:text-white/20 transition-colors group-hover:text-slate-500 dark:text-white/60 mr-1">
                        <i class="fa-solid fa-chevron-right text-sm" aria-hidden="true"></i>
                    </span>
                </a>
            </div>
        </div>
    @else
        <div class="rounded-2xl border border-amber-200 dark:border-amber-400/25 bg-amber-50 dark:bg-amber-500/10 px-5 py-6 text-center">
            <span class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-amber-400/20 text-amber-200">
                <i class="fa-solid fa-screwdriver-wrench" aria-hidden="true"></i>
            </span>
            <p class="font-semibold text-slate-900 dark:text-white">Contact details not configured</p>
            <p class="mt-2 text-sm text-slate-700 dark:text-white/75 leading-relaxed">
                Add <span class="font-mono text-xs bg-white dark:bg-white/10 px-1.5 py-0.5 rounded">CHANDLABOOK_SUPPORT_EMAIL</span> and/or <span class="font-mono text-xs bg-white dark:bg-white/10 px-1.5 py-0.5 rounded">CHANDLABOOK_SUPPORT_PHONE</span> in your environment.
            </p>
        </div>
    @endif

    <div class="mt-8 rounded-xl border border-slate-200 dark:border-white/10 bg-white/[0.04] px-4 py-4 text-sm text-slate-600 dark:text-white/70 leading-relaxed">
        <p class="font-semibold text-slate-800 dark:text-white/90 mb-2">Before you reach out</p>
        <p>
            Many answers live on our
            <a href="{{ url('/#faq') }}" class="font-semibold text-slate-900 dark:text-white underline decoration-slate-300 dark:decoration-white/35 underline-offset-2 hover:decoration-amber-300/80">FAQ</a>.
            For packs and pricing, see
            <a href="{{ url('/#pricing') }}" class="font-semibold text-slate-900 dark:text-white underline decoration-slate-300 dark:decoration-white/35 underline-offset-2 hover:decoration-amber-300/80">Plans</a>.
        </p>
    </div>

    @auth
        <p class="mt-8 text-center text-sm text-slate-500 dark:text-white/55">
            Signed in?
            <a href="{{ route('client.contact') }}" class="font-semibold text-slate-900 dark:text-white underline hover:text-amber-200">Account contact page</a>
        </p>
    @endauth
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('cb-dark-mode-toggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
    }
});
</script>
@endpush
@endsection
