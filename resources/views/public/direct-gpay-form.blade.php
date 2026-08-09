<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1e1b4b">
    <title>Pay — {{ $event->title }} — Chandla Book</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="{{ asset('css/tailwind.css') }}?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/chandla-favicon.png') }}">
</head>
<body class="min-h-screen relative overflow-x-hidden text-slate-800">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-indigo-950 to-violet-950 min-h-full"></div>
    <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 20% 20%, rgba(99, 102, 241, 0.35) 0%, transparent 45%), radial-gradient(circle at 80% 60%, rgba(124, 58, 237, 0.25) 0%, transparent 40%);"></div>

    <div class="relative z-10 max-w-md mx-auto px-4 sm:px-5 py-10 sm:py-14">
        <header class="text-center mb-8">
            <a href="{{ route('public.home') }}" class="inline-flex flex-col items-center gap-3 group no-underline">
                <div class="relative">
                    <div class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-indigo-500/30 to-fuchsia-500/30 blur-md opacity-80 group-hover:opacity-100 transition"></div>
                    <img src="{{ asset('images/logo.jpeg') }}"
                         width="80" height="80"
                         class="relative h-20 w-20 object-contain drop-shadow-lg"
                         alt="Chandla Book">
                </div>
                <div>
                    <p class="text-white/95 text-lg font-bold tracking-tight">Chandla Book</p>
                    <p class="text-indigo-200/90 text-sm mt-0.5">UPI &amp; Google Pay for your host</p>
                </div>
            </a>
        </header>

        <div class="rounded-2xl bg-white/95 backdrop-blur-sm shadow-2xl shadow-indigo-950/50 ring-1 ring-white/50 overflow-hidden">
            <div class="px-1 pt-1 pb-5 bg-gradient-to-r from-indigo-600/10 via-violet-500/10 to-sky-500/10">
                <div class="px-5 pt-5 pb-1">
                    <p class="text-xs font-semibold uppercase tracking-widest text-indigo-600/80 mb-1">Event</p>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 leading-snug">{{ $event->title }}</h1>
                    <p class="text-slate-600 text-sm mt-2 flex items-start gap-2">
                        <i class="fas fa-mobile-screen-button text-indigo-500 mt-0.5 shrink-0"></i>
                        <span>Enter your details, then we’ll show a <strong>QR</strong> and a link to <strong>Google Pay</strong> with your amount pre-filled.</span>
                    </p>
                </div>
            </div>

            <div class="p-5 sm:p-6 pt-2">
            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200/80 text-rose-800 text-sm p-4">
                    <p class="font-medium mb-1 flex items-center gap-2"><i class="fas fa-circle-exclamation"></i> Please check</p>
                    <ul class="list-disc list-inside space-y-1 text-rose-800/90">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('public.direct-gpay.prepare', ['event' => $event->id]) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Name <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"><i class="fas fa-user"></i></span>
                        <input type="text" name="giver_name" value="{{ old('giver_name') }}" required maxlength="255"
                               class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-3 bg-slate-50/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/80 focus:border-indigo-400 transition shadow-inner"
                               placeholder="Full name">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Amount (₹) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 font-semibold">₹</span>
                        <input type="number" name="amount" value="{{ old('amount') }}" required step="0.01" min="1"
                               class="w-full border border-slate-200 rounded-xl pl-9 pr-4 py-3 bg-slate-50/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/80 focus:border-indigo-400 transition shadow-inner text-lg font-semibold tabular-nums"
                               placeholder="0.00">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Village <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"><i class="fas fa-location-dot"></i></span>
                        <input type="text" name="village" value="{{ old('village') }}" required maxlength="255"
                               class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-3 bg-slate-50/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/80 focus:border-indigo-400 transition shadow-inner"
                               placeholder="Village / area">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Cell number <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"><i class="fas fa-phone"></i></span>
                        <input type="text" name="giver_phone" value="{{ old('giver_phone') }}" required maxlength="20" inputmode="numeric"
                               class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-3 bg-slate-50/80 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/80 focus:border-indigo-400 transition shadow-inner"
                               placeholder="10-digit mobile">
                    </div>
                </div>
                <button type="submit"
                        class="w-full mt-2 flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-violet-600 text-white px-6 py-3.5 rounded-xl hover:from-indigo-500 hover:to-violet-500 font-semibold shadow-lg shadow-indigo-500/30 transition">
                    <span>Continue to Google Pay</span>
                    <i class="fas fa-arrow-right text-sm"></i>
                </button>
            </form>

            <p class="text-xs text-slate-500 mt-5 text-center leading-relaxed">
                Secure UPI to your host’s account. The organizer can see this gift in their Chandla ledger.
            </p>
            </div>
        </div>
        <p class="text-center text-indigo-200/50 text-xs mt-8">
            <a href="{{ route('public.home') }}" class="text-indigo-200/80 hover:text-white transition">Chandla Book</a>
            <span class="mx-1">·</span>
            <span>India</span>
        </p>
    </div>
</body>
</html>
