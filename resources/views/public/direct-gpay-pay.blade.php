<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1e1b4b">
    <title>Pay ₹{{ $amount }} — {{ $event->title }}</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="{{ asset('css/tailwind.css') }}?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/chandla-favicon.png') }}">
</head>
<body class="min-h-screen relative overflow-x-hidden text-slate-800">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-indigo-950 to-emerald-950/80 min-h-full"></div>
    <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 15% 30%, rgba(16, 185, 129, 0.25) 0%, transparent 50%), radial-gradient(circle at 85% 50%, rgba(99, 102, 241, 0.3) 0%, transparent 45%);"></div>

    <div class="relative z-10 max-w-md mx-auto px-4 sm:px-5 py-8 sm:py-12">
        <header class="text-center mb-6">
            <a href="{{ route('public.home') }}" class="inline-flex flex-col items-center gap-2 no-underline">
                <img src="{{ file_exists(public_path('images/chandla-favicon.png')) ? asset('images/chandla-favicon.png') : asset('images/chandla-favicon.png') }}"
                     width="64" height="64" class="h-16 w-16 object-contain drop-shadow-md" alt="Chandla Book">
                <span class="text-white/90 text-sm font-bold tracking-tight">Chandla Book</span>
            </a>
        </header>

        <div class="rounded-2xl bg-white/95 backdrop-blur-sm shadow-2xl shadow-slate-900/50 ring-1 ring-white/50 overflow-hidden text-center p-5 sm:p-7">
            <p class="text-xs font-semibold uppercase tracking-widest text-indigo-600/80 mb-1">Paying for</p>
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 leading-snug px-1">{{ $event->title }}</h1>
            <p class="text-slate-600 text-sm mt-1">{{ $data['giver_name'] }}</p>
            <div class="mt-4 inline-flex items-baseline justify-center gap-0.5 rounded-2xl bg-gradient-to-br from-emerald-50 to-sky-50 border border-emerald-100/80 px-6 py-3">
                <span class="text-emerald-700 text-lg font-bold">₹</span>
                <span class="text-3xl sm:text-4xl font-extrabold text-emerald-800 tabular-nums tracking-tight">{{ $amount }}</span>
            </div>

            <div class="mt-5 mb-1 rounded-2xl bg-slate-50 p-3 inline-block shadow-inner border border-slate-100/80">
                <div class="flex justify-center [&_svg]:max-w-[240px] sm:[&_svg]:max-w-[260px]">
                    {!! $qrSvg !!}
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-2">Scan with any UPI app</p>

            <a href="{{ $upiUri }}"
               class="mt-5 inline-flex items-center justify-center w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-6 py-3.5 rounded-xl hover:from-emerald-500 hover:to-teal-500 font-semibold shadow-lg shadow-emerald-700/20 gap-2">
                <i class="fas fa-mobile-screen-button text-lg"></i>
                <span>Open in Google Pay</span>
            </a>

            <p class="text-sm text-slate-600 mt-6 text-left leading-relaxed">
                <i class="fas fa-circle-info text-sky-500 mr-1"></i>
                When payment succeeds in the app, tap the button below so the host can record you under
                <strong>GPAY GPAY</strong> in the Chandla ledger.
            </p>

            <form method="POST" action="{{ route('public.direct-gpay.complete', ['event' => $event->id]) }}" class="mt-4">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-violet-600 text-white px-6 py-3.5 rounded-xl hover:from-indigo-500 hover:to-violet-500 font-semibold shadow-md shadow-indigo-500/25 transition">
                    <i class="fas fa-check"></i>
                    <span>Payment done — record my entry</span>
                </button>
            </form>

            <a href="{{ route('public.direct-gpay', ['event' => $event->id]) }}"
               class="inline-block mt-5 text-sm text-slate-500 hover:text-indigo-600 transition">Start over</a>
        </div>
    </div>
</body>
</html>
