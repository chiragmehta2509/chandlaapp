@extends('layouts.client')

@section('title', $contact->name)

@section('content')
@php
    $initial = mb_strtoupper(mb_substr(trim($contact->name) ?: '?', 0, 1));
    $phoneDigits = $contact->phone ? preg_replace('/\D+/', '', $contact->phone) : '';
@endphp

<div class="max-w-4xl mx-auto">
    {{-- Back link --}}
    <a href="{{ route('client.contacts.index') }}"
       class="cb-link text-sm inline-flex items-center gap-2 mb-4 sm:mb-5">
        <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>
        <span>Back to contacts</span>
    </a>

    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <i class="fas fa-check-circle mr-1"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Hero card --}}
    <div class="cb-card cb-card--hero relative overflow-hidden p-5 sm:p-7 mb-5 sm:mb-6">
        <div class="pointer-events-none absolute -right-20 -top-20 h-60 w-60 rounded-full bg-amber-500/15 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-16 -left-10 h-48 w-48 rounded-full bg-fuchsia-500/10 blur-3xl" aria-hidden="true"></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4 sm:gap-5 min-w-0">
                <div class="relative shrink-0">
                    <div class="h-16 w-16 sm:h-20 sm:w-20 rounded-full bg-gradient-to-br from-amber-200 via-amber-300 to-amber-400 flex items-center justify-center ring-4 ring-white/20 shadow-lg shadow-black/30">
                        <span class="text-amber-900 font-bold text-2xl sm:text-3xl">{{ $initial }}</span>
                    </div>
                    @if($contact->is_favorite)
                        <div class="absolute -bottom-1 -right-1 h-7 w-7 rounded-full bg-amber-400 ring-2 ring-[#1a3646] flex items-center justify-center">
                            <i class="fas fa-star text-white text-xs" aria-hidden="true"></i>
                        </div>
                    @endif
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl lg:text-[1.65rem] font-bold tracking-tight text-white leading-tight break-words">{{ $contact->name }}</h1>
                    @if($contact->relationship)
                        <p class="mt-1 text-xs sm:text-sm font-medium text-amber-200/95">
                            <i class="fas fa-user-tag mr-1.5 opacity-80" aria-hidden="true"></i>{{ $contact->relationship }}
                        </p>
                    @endif
                    @if($contact->is_favorite)
                        <p class="mt-1 text-xs text-amber-200/90 inline-flex items-center gap-1">
                            <i class="fas fa-star" aria-hidden="true"></i>Favorite contact
                        </p>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="{{ route('client.contacts.edit', $contact->id) }}"
                   class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3.5 py-2 text-xs font-semibold text-white shadow-sm backdrop-blur-sm transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-300/60">
                    <i class="fas fa-pen text-[0.7rem]" aria-hidden="true"></i>
                    Edit
                </a>
                <form action="{{ route('client.contacts.toggle-favorite', $contact->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-full border border-white/20 {{ $contact->is_favorite ? 'bg-amber-400 text-amber-950 hover:bg-amber-300' : 'bg-white/10 text-white hover:bg-white/20' }} px-3.5 py-2 text-xs font-semibold shadow-sm backdrop-blur-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-300/60">
                        <i class="fa-{{ $contact->is_favorite ? 'solid' : 'regular' }} fa-star text-[0.75rem]" aria-hidden="true"></i>
                        {{ $contact->is_favorite ? 'Favorited' : 'Favorite' }}
                    </button>
                </form>
                @canDelete
                <form action="{{ route('client.contacts.destroy', $contact->id) }}" method="POST" class="inline"
                      onsubmit="return confirm('Delete this contact? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-full border border-rose-300/40 bg-rose-500/15 px-3.5 py-2 text-xs font-semibold text-rose-100 shadow-sm backdrop-blur-sm transition hover:bg-rose-500/25 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-300/50">
                        <i class="fas fa-trash text-[0.7rem]" aria-hidden="true"></i>
                        Delete
                    </button>
                </form>
                @endcanDelete
            </div>
        </div>
    </div>

    {{-- Quick contact actions row (only if phone or email present) --}}
    @if($contact->phone || $contact->email)
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 mb-5 sm:mb-6">
            @if($contact->phone)
                <a href="tel:{{ $contact->phone }}"
                   class="group flex flex-col items-center justify-center gap-1.5 rounded-2xl border border-slate-200/90 bg-white px-3 py-4 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-800">
                    <i class="fas fa-phone text-base text-emerald-600 group-hover:scale-110 transition" aria-hidden="true"></i>
                    Call
                </a>
                @if($phoneDigits !== '')
                    <a href="https://wa.me/{{ $phoneDigits }}" target="_blank" rel="noopener"
                       class="group flex flex-col items-center justify-center gap-1.5 rounded-2xl border border-slate-200/90 bg-white px-3 py-4 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-green-300 hover:bg-green-50 hover:text-green-800">
                        <i class="fab fa-whatsapp text-lg text-green-600 group-hover:scale-110 transition" aria-hidden="true"></i>
                        WhatsApp
                    </a>
                @endif
                <a href="sms:{{ $contact->phone }}"
                   class="group flex flex-col items-center justify-center gap-1.5 rounded-2xl border border-slate-200/90 bg-white px-3 py-4 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-sky-300 hover:bg-sky-50 hover:text-sky-800">
                    <i class="fas fa-comment-sms text-base text-sky-600 group-hover:scale-110 transition" aria-hidden="true"></i>
                    SMS
                </a>
            @endif
            @if($contact->email)
                <a href="mailto:{{ $contact->email }}"
                   class="group flex flex-col items-center justify-center gap-1.5 rounded-2xl border border-slate-200/90 bg-white px-3 py-4 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-violet-300 hover:bg-violet-50 hover:text-violet-800">
                    <i class="fas fa-envelope text-base text-violet-600 group-hover:scale-110 transition" aria-hidden="true"></i>
                    Email
                </a>
            @endif
        </div>
    @endif

    {{-- Details grid --}}
    <div class="cb-card p-5 sm:p-6 mb-5 sm:mb-6">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-4 sm:mb-5">Contact details</h2>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
            @if($contact->phone)
                <div class="flex items-start gap-3 rounded-xl border border-slate-200/80 bg-slate-50/60 p-3.5 min-w-0">
                    <div class="shrink-0 h-9 w-9 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <i class="fas fa-phone text-sm" aria-hidden="true"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <dt class="text-[0.7rem] font-bold uppercase tracking-wider text-slate-500">Phone</dt>
                        <dd class="mt-0.5 text-sm font-semibold text-cb-navy break-all">
                            <a href="tel:{{ $contact->phone }}" class="hover:underline">{{ $contact->phone }}</a>
                        </dd>
                    </div>
                    <button type="button" data-copy="{{ $contact->phone }}"
                            class="js-copy-pill shrink-0 self-center text-slate-400 hover:text-cb-navy p-1.5 rounded-lg hover:bg-white transition"
                            title="Copy phone" aria-label="Copy phone">
                        <i class="fas fa-copy text-xs" aria-hidden="true"></i>
                    </button>
                </div>
            @endif

            @if($contact->email)
                <div class="flex items-start gap-3 rounded-xl border border-slate-200/80 bg-slate-50/60 p-3.5 min-w-0">
                    <div class="shrink-0 h-9 w-9 rounded-lg bg-violet-100 text-violet-700 flex items-center justify-center">
                        <i class="fas fa-envelope text-sm" aria-hidden="true"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <dt class="text-[0.7rem] font-bold uppercase tracking-wider text-slate-500">Email</dt>
                        <dd class="mt-0.5 text-sm font-semibold text-cb-navy break-all">
                            <a href="mailto:{{ $contact->email }}" class="hover:underline">{{ $contact->email }}</a>
                        </dd>
                    </div>
                    <button type="button" data-copy="{{ $contact->email }}"
                            class="js-copy-pill shrink-0 self-center text-slate-400 hover:text-cb-navy p-1.5 rounded-lg hover:bg-white transition"
                            title="Copy email" aria-label="Copy email">
                        <i class="fas fa-copy text-xs" aria-hidden="true"></i>
                    </button>
                </div>
            @endif

            @if($contact->relationship)
                <div class="flex items-start gap-3 rounded-xl border border-slate-200/80 bg-slate-50/60 p-3.5 min-w-0">
                    <div class="shrink-0 h-9 w-9 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center">
                        <i class="fas fa-user-tag text-sm" aria-hidden="true"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <dt class="text-[0.7rem] font-bold uppercase tracking-wider text-slate-500">Relationship</dt>
                        <dd class="mt-0.5 text-sm font-semibold text-cb-navy break-words">{{ $contact->relationship }}</dd>
                    </div>
                </div>
            @endif

            <div class="flex items-start gap-3 rounded-xl border border-slate-200/80 bg-slate-50/60 p-3.5 min-w-0">
                <div class="shrink-0 h-9 w-9 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center">
                    <i class="fas fa-calendar-plus text-sm" aria-hidden="true"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <dt class="text-[0.7rem] font-bold uppercase tracking-wider text-slate-500">Added on</dt>
                    <dd class="mt-0.5 text-sm font-semibold text-cb-navy">{{ $contact->created_at->format('d/m/Y') }}</dd>
                </div>
            </div>

            @if($contact->address)
                <div class="sm:col-span-2 flex items-start gap-3 rounded-xl border border-slate-200/80 bg-slate-50/60 p-3.5 min-w-0">
                    <div class="shrink-0 h-9 w-9 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center">
                        <i class="fas fa-location-dot text-sm" aria-hidden="true"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <dt class="text-[0.7rem] font-bold uppercase tracking-wider text-slate-500">Address</dt>
                        <dd class="mt-0.5 text-sm font-semibold text-cb-navy break-words whitespace-pre-line">{{ $contact->address }}</dd>
                    </div>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($contact->address) }}"
                       target="_blank" rel="noopener"
                       class="shrink-0 self-center text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-white transition"
                       title="Open in Google Maps" aria-label="Open address in Google Maps">
                        <i class="fas fa-up-right-from-square text-xs" aria-hidden="true"></i>
                    </a>
                </div>
            @endif
        </dl>
    </div>

    {{-- Notes --}}
    @if($contact->notes)
        <div class="cb-card p-5 sm:p-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center gap-2">
                <i class="fas fa-note-sticky text-amber-500" aria-hidden="true"></i>Notes
            </h2>
            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $contact->notes }}</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.querySelectorAll('.js-copy-pill').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const text = btn.dataset.copy || '';
            if (!text) return;
            const icon = btn.querySelector('i');
            const original = icon ? icon.className : '';
            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(text);
                } else {
                    const ta = document.createElement('textarea');
                    ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
                    document.body.appendChild(ta); ta.select();
                    document.execCommand('copy'); document.body.removeChild(ta);
                }
                if (icon) {
                    icon.className = 'fas fa-check text-xs text-emerald-600';
                    setTimeout(() => { icon.className = original; }, 1200);
                }
            } catch (e) { console.error('Copy failed', e); }
        });
    });
</script>
@endpush
@endsection
