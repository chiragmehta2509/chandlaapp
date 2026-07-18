@foreach($milestones as $key => $m)
    @php
        $theme = $m['theme'] ?? '';
    @endphp
    <div class="bg-white rounded-2xl border border-slate-200/80 p-3 flex flex-col justify-between gap-3 shadow-sm relative overflow-hidden group">
        <span class="absolute top-2 right-2 z-10 inline-flex items-center gap-1 rounded-full bg-slate-900/75 text-white text-[8px] font-bold uppercase tracking-wider px-2 py-0.5 backdrop-blur-sm">
            <i class="fas fa-lock text-[7px]" aria-hidden="true"></i> Demo
        </span>

        <div class="text-center min-w-0">
            <h3 class="font-bold text-cb-navy text-xs truncate" title="{{ $m['label'] }}">{{ $m['label'] }}</h3>
            <p class="text-[10px] text-violet-600 font-medium mt-0.5 truncate">{{ $themeHints[$theme] ?? $theme }}</p>
        </div>

        <div class="relative rounded-xl overflow-hidden border border-slate-200 bg-slate-100 aspect-[9/16] w-full max-w-[140px] group mx-auto cb-lazy-iframe-wrap">
            {{-- Spinner loader --}}
            <div class="cb-iframe-skeleton absolute inset-0 flex flex-col items-center justify-center gap-1.5 overflow-hidden"
                 style="background:linear-gradient(135deg,#f1f5f9 0%,#e2e8f0 50%,#f1f5f9 100%);background-size:200% 200%;animation:cb-shimmer 2s ease-in-out infinite;">
                <div style="width:22px;height:22px;border-radius:50%;border:2.5px solid #cbd5e1;border-top-color:#94a3b8;animation:cb-spin 0.8s linear infinite;"></div>
                <span style="font-size:9px;color:#94a3b8;font-weight:600;letter-spacing:0.04em;">Loading…</span>
            </div>
            <iframe
                data-src="{{ route('client.pre-wedding.thumbnail-preview', ['milestoneKey' => $key]) }}"
                title="{{ $m['label'] }} preview"
                class="pointer-events-none absolute inset-0 h-full w-full border-0 opacity-0 transition-opacity duration-500 cb-lazy-iframe"
                referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
        </div>
    </div>
@endforeach

@once
<style>
@keyframes cb-spin    { to { transform: rotate(360deg); } }
@keyframes cb-shimmer { 0%,100% { background-position:0% 50%; } 50% { background-position:100% 50%; } }
</style>
@endonce
