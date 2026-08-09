<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1e1b4b">
    <title>Thank you — {{ $event->title }}</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="{{ asset('css/tailwind.css') }}?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/chandla-favicon.png') }}">
</head>
<body class="min-h-screen relative overflow-x-hidden text-slate-800">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-violet-950 to-indigo-950 min-h-full"></div>
    <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 50% 0%, rgba(16, 185, 129, 0.35) 0%, transparent 50%);"></div>

    <div class="relative z-10 max-w-md mx-auto px-4 sm:px-5 py-10 sm:py-14">
        <header class="text-center mb-6">
            <a href="{{ route('public.home') }}" class="inline-flex flex-col items-center gap-2 no-underline">
                <img src="{{ asset('images/logo.jpeg') }}"
                     width="64" height="64" class="h-16 w-16 object-contain drop-shadow-md" alt="Chandla Book">
                <span class="text-white/90 text-sm font-bold">Chandla Book</span>
            </a>
        </header>

        <div class="rounded-2xl bg-white/95 backdrop-blur-sm shadow-2xl ring-1 ring-white/50 p-6 sm:p-8 text-center">
            <div class="mx-auto w-20 h-20 rounded-full bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center text-emerald-600 text-4xl shadow-inner border border-emerald-200/60 mb-5">
                <i class="fas fa-circle-check"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Thank you</h1>
            <p class="text-slate-600 mt-2 text-base">Your entry is recorded for <strong class="text-slate-800">{{ $event->title }}</strong>.</p>

            <dl class="mt-8 text-left text-sm space-y-3 border-t border-slate-100 pt-6">
                <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Name</dt><dd class="font-medium text-slate-800 text-right">{{ $chandla->giver_name }}</dd></div>
                <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Amount</dt><dd class="font-bold text-emerald-800 text-right tabular-nums">₹{{ number_format($chandla->amount, 2) }}</dd></div>
                <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Village</dt><dd class="font-medium text-slate-800 text-right">{{ $chandla->giver_address }}</dd></div>
                <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Phone</dt><dd class="font-medium text-slate-800 text-right">{{ $chandla->giver_phone }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Category</dt><dd class="font-medium text-slate-800 text-right">{{ $chandla->category }}</dd></div>
            </dl>

            @if (!($emailStatus['organizer_sent'] ?? false))
                <p class="text-xs text-amber-800 mt-5 bg-amber-50 border border-amber-100 rounded-xl p-3 text-left">
                    <i class="fas fa-triangle-exclamation text-amber-600 mr-1"></i>
                    The organizer could not be emailed automatically. They will still see your entry in their ledger.
                </p>
            @endif
        </div>
        <p class="text-center text-indigo-200/50 text-xs mt-8">
            <a href="{{ route('public.home') }}" class="text-indigo-200/80 hover:text-white transition">Chandla Book</a>
        </p>
    </div>
</body>
</html>
