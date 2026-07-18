{{-- Live iframe previews with demo sample data (auth-only route). --}}
@foreach($templates as $key => $t)
    @php
        $params = ['layout' => $key];
        if (isset($invitation) && $invitation) {
            $params['invitation_id'] = $invitation->id;
        } elseif (isset($latestInvitation) && $latestInvitation) {
            $params['invitation_id'] = $latestInvitation->id;
        }
        $thumbSrc = route('client.marriage-invitations.template-demo', $params);
        $thumbTitle = ($t['name'] ?? $key).' — demo preview';
    @endphp
    <div class="cb-card p-0 overflow-hidden border border-slate-200/80 flex flex-col shadow-sm relative">
        <span class="absolute top-2 right-2 z-10 inline-flex items-center gap-1 rounded-full bg-slate-900/75 text-white text-[0.65rem] font-semibold px-2 py-0.5 backdrop-blur-sm">
            <i class="fas fa-lock text-[0.6rem]" aria-hidden="true"></i> Demo
        </span>
        <div class="px-4 py-3 border-b border-slate-100 flex items-center gap-3 bg-slate-50/80">
            <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-sm font-bold {{ $t['badge_class'] ?? 'bg-slate-200 text-slate-800' }}">{{ $t['badge'] ?? substr($key, 0, 1) }}</span>
            <h3 class="text-base font-bold text-cb-navy leading-tight">{{ $t['name'] }}</h3>
        </div>
        <div class="w-full overflow-hidden">
            @include('client.marriage-invitations.partials.template-thumb-iframe', ['thumbSrc' => $thumbSrc, 'thumbTitle' => $thumbTitle])
        </div>
        <div class="p-4 sm:p-5">
            <p class="text-sm text-slate-600 leading-relaxed">{{ $t['description'] ?? '' }}</p>
            <p class="text-xs text-amber-800/90 mt-3 font-medium">Pay the celebration pack to use your own wording and photo on every style.</p>
        </div>
    </div>
@endforeach
