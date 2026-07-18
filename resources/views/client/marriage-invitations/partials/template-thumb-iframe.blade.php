{{-- Live-rendered template snapshot. Uses staggered auto-load — iframe fires when its turn comes. --}}
<div class="relative aspect-[5/8] w-full overflow-hidden bg-slate-100 isolate rounded-sm cb-lazy-iframe-wrap">

    {{-- Loader: shimmer background + centred spinner, shown until iframe fades in --}}
    <div class="cb-iframe-skeleton absolute inset-0 flex flex-col items-center justify-center gap-2 overflow-hidden"
         style="background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 50%, #f1f5f9 100%); background-size: 200% 200%; animation: cb-shimmer 2s ease-in-out infinite;">
        {{-- Spinning ring --}}
        <div style="width:28px;height:28px;border-radius:50%;border:3px solid #cbd5e1;border-top-color:#94a3b8;animation:cb-spin 0.8s linear infinite;"></div>
        <span style="font-size:10px;color:#94a3b8;font-weight:600;letter-spacing:0.05em;margin-top:4px;">Loading…</span>
    </div>

    <iframe
        data-src="{{ $thumbSrc }}"
        title="{{ $thumbTitle ?? 'Invitation preview' }}"
        class="pointer-events-none absolute inset-0 h-full w-full border-0 opacity-0 transition-opacity duration-500 cb-lazy-iframe"
        referrerpolicy="no-referrer-when-downgrade"
    ></iframe>
</div>

@once
<style>
@keyframes cb-spin    { to { transform: rotate(360deg); } }
@keyframes cb-shimmer { 0%,100% { background-position:0% 50%; } 50% { background-position:100% 50%; } }
</style>
@endonce
