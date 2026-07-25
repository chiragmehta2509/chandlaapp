@extends('layouts.client')

@section('title', 'Family members')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('client.dashboard') }}" class="cb-link text-sm font-medium inline-flex items-center gap-1">
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i> Back to dashboard
        </a>
    </div>

    <div class="cb-card p-5 sm:p-6 mb-6">
        <div class="flex items-start gap-3">
            <span class="hidden sm:inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-800" aria-hidden="true">
                <i class="fas fa-users"></i>
            </span>
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-cb-navy">Family members</h1>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">Add up to <strong>{{ $maxAllowed }}</strong> family members so they can sign in and view your events, ledger, contacts, and downloads.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50/80 dark:bg-emerald-950/30 px-4 py-3 text-sm text-emerald-900 dark:text-emerald-300">
            <i class="fas fa-check-circle mr-1.5" aria-hidden="true"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('temp_password'))
        <div class="mb-4 rounded-xl border-2 border-amber-300 bg-amber-50 dark:bg-amber-950/30 p-5">
            <p class="text-sm font-bold text-amber-900 dark:text-amber-200 flex items-center gap-2">
                <i class="fas fa-key" aria-hidden="true"></i> Share these login details with {{ session('temp_password_for') }}
            </p>
            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="rounded-lg bg-white dark:bg-slate-900 border border-amber-200 dark:border-amber-800 px-3 py-2.5">
                    <p class="text-[0.65rem] font-bold uppercase tracking-wider text-amber-800/80 dark:text-amber-300">Login (mobile)</p>
                    <p class="font-mono text-base text-cb-navy mt-0.5 select-all">{{ session('temp_password_login') }}</p>
                </div>
                <div class="rounded-lg bg-white dark:bg-slate-900 border border-amber-200 dark:border-amber-800 px-3 py-2.5">
                    <p class="text-[0.65rem] font-bold uppercase tracking-wider text-amber-800/80 dark:text-amber-300">Temporary password</p>
                    <p class="font-mono text-base text-cb-navy mt-0.5 select-all">{{ session('temp_password') }}</p>
                </div>
            </div>
            <p class="text-xs text-amber-800 dark:text-amber-300 mt-3 leading-relaxed">No email was provided, so we couldn't email these details. Save them — they won't be shown again. {{ session('temp_password_for') }} will be asked to set a new password on first sign-in.</p>
        </div>
    @endif

    @if(session('bulk_added_members'))
        <div class="mb-6 rounded-2xl border-2 border-amber-300 bg-amber-50 dark:bg-amber-950/30 p-5">
            <p class="text-sm font-bold text-amber-900 dark:text-amber-200 flex items-center gap-2 mb-3">
                <i class="fas fa-key text-base" aria-hidden="true"></i> Login details for newly added family members
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach(session('bulk_added_members') as $bm)
                    <div class="rounded-xl bg-white dark:bg-slate-900 border border-amber-200 dark:border-amber-800 p-3.5 shadow-sm">
                        <p class="font-bold text-sm text-cb-navy mb-1.5">{{ $bm['name'] }}</p>
                        <div class="space-y-1 text-xs">
                            <p class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                                <span>Mobile (Login):</span>
                                <code class="font-mono font-bold text-slate-900 dark:text-slate-200 select-all">{{ $bm['phone'] }}</code>
                            </p>
                            @if($bm['has_email'])
                                <p class="text-emerald-700 dark:text-emerald-400 font-medium text-[0.7rem]"><i class="fas fa-paper-plane mr-1"></i>Sent to {{ $bm['email'] }}</p>
                            @else
                                <p class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                                    <span>Temp Password:</span>
                                    <code class="font-mono font-bold text-amber-900 dark:text-amber-300 bg-amber-100/80 dark:bg-amber-900/40 px-1.5 py-0.5 rounded select-all">{{ $bm['temp_password'] }}</code>
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <p class="text-xs text-amber-800 dark:text-amber-300 mt-3 leading-relaxed">Save these details to share with your family members.</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50/80 dark:bg-rose-950/30 px-4 py-3 text-sm text-rose-900 dark:text-rose-300">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Plan-aware capability strip --}}
    <div class="rounded-2xl border {{ $canAddEditors ? 'border-emerald-200 bg-emerald-50/50 dark:border-emerald-800 dark:bg-emerald-950/20' : 'border-sky-200 bg-sky-50/40 dark:border-sky-800 dark:bg-sky-950/20' }} p-4 sm:p-5 mb-6 flex flex-col sm:flex-row gap-3 sm:items-center">
        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $canAddEditors ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-sky-800' }}">
            <i class="fas {{ $canAddEditors ? 'fa-shield-halved' : 'fa-eye' }}" aria-hidden="true"></i>
        </span>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold {{ $canAddEditors ? 'text-emerald-900 dark:text-emerald-300' : 'text-sky-900 dark:text-sky-300' }}">
                @if($canAddEditors)
                    Your plan unlocks <strong>full-access editors</strong>.
                @else
                    Your plan supports <strong>view-only family members</strong>.
                @endif
            </p>
            <p class="text-xs {{ $canAddEditors ? 'text-emerald-800/90 dark:text-emerald-400' : 'text-sky-800/90 dark:text-sky-400' }} mt-0.5 leading-relaxed">
                @if($canAddEditors)
                    Editors can add and edit on your account. They can't delete, manage other family members, or buy plans.
                @else
                    Viewers can browse and download your data. Upgrade to <strong>Family Plan (₹{{ number_format($familyPackAmount, 0) }})</strong> or <strong>Premium Host Plan (₹{{ number_format($completePackAmount, 0) }})</strong> to add full-access editors.
                @endif
            </p>
        </div>
        @unless($canAddEditors)
            <a href="{{ route('client.plans') }}" class="cb-btn cb-btn-navy cb-btn--sm shrink-0 self-start sm:self-auto">
                <i class="fas fa-arrow-up-right-from-square text-xs" aria-hidden="true"></i> View plans
            </a>
        @endunless
    </div>

    {{-- Add Form with Multi-Member Row Support --}}
    @if($remainingSlots > 0)
        <div class="cb-card p-5 sm:p-6 mb-6">
            <div class="flex items-center justify-between gap-3 mb-2 flex-wrap">
                <div>
                    <h2 class="text-lg font-bold text-cb-navy">Add family members</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $remainingSlots }} of {{ $maxAllowed }} {{ $remainingSlots === 1 ? 'slot' : 'slots' }} remaining. You can add one or multiple members at once!
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('client.family-members.store') }}" id="family-members-form" class="space-y-4">
                @csrf

                <div id="members-container" class="space-y-4">
                    {{-- Row 1 --}}
                    <div class="member-row p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 space-y-3 relative">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 member-row-title">Member #1</span>
                            <button type="button" class="remove-row-btn text-rose-500 hover:text-rose-700 text-xs font-semibold hidden">
                                <i class="fas fa-trash-can mr-1"></i> Remove
                            </button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="cb-label">Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="members[0][name]" required maxlength="255" class="cb-field" placeholder="Full name">
                            </div>
                            <div>
                                <label class="cb-label">Mobile <span class="text-rose-500">*</span></label>
                                <input type="tel" name="members[0][phone]" required pattern="[6-9][0-9]{9}" maxlength="10" inputmode="numeric" class="cb-field" placeholder="10-digit mobile">
                            </div>
                            <div>
                                <label class="cb-label">Email <span class="text-slate-400 font-normal">(optional)</span></label>
                                <input type="email" name="members[0][email]" maxlength="255" class="cb-field" placeholder="name@example.com">
                            </div>
                        </div>
                        @if($canAddEditors)
                            <div>
                                <label class="cb-label">Access level</label>
                                <div class="flex flex-wrap gap-3 mt-1">
                                    <label class="relative flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 cursor-pointer text-xs font-semibold text-slate-700 dark:text-slate-300 has-[:checked]:border-sky-500 has-[:checked]:bg-sky-50 dark:has-[:checked]:bg-sky-950/40">
                                        <input type="radio" name="members[0][role]" value="viewer" checked>
                                        <span><i class="fas fa-eye text-sky-600 mr-1"></i> Viewer (read-only)</span>
                                    </label>
                                    <label class="relative flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 cursor-pointer text-xs font-semibold text-slate-700 dark:text-slate-300 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 dark:has-[:checked]:bg-emerald-950/40">
                                        <input type="radio" name="members[0][role]" value="editor">
                                        <span><i class="fas fa-shield-halved text-emerald-600 mr-1"></i> Editor (full access)</span>
                                    </label>
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="members[0][role]" value="viewer">
                        @endif
                    </div>
                </div>

                {{-- Form action controls --}}
                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                    @if($remainingSlots > 1)
                        <button type="button" id="add-another-member-btn" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold border-2 border-dashed border-amber-300 dark:border-amber-700 text-amber-800 dark:text-amber-300 bg-amber-50/60 dark:bg-amber-950/30 hover:bg-amber-100 transition-colors">
                            <i class="fas fa-user-plus"></i>
                            <span>+ Add another member row</span>
                        </button>
                    @else
                        <div></div>
                    @endif

                    <button type="submit" class="cb-btn cb-btn-gold">
                        <i class="fas fa-users-medical mr-2" aria-hidden="true"></i>Save family members
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="rounded-2xl border border-amber-200 bg-amber-50/60 dark:bg-amber-950/30 p-5 mb-6">
            <p class="text-sm font-semibold text-amber-900 dark:text-amber-200"><i class="fas fa-info-circle mr-1.5" aria-hidden="true"></i>You've reached the maximum of {{ $maxAllowed }} family members.</p>
            <p class="text-xs text-amber-800 dark:text-amber-300 mt-1">Remove one below to add another.</p>
        </div>
    @endif

    {{-- List --}}
    <div class="cb-card overflow-hidden">
        <div class="px-5 py-4 sm:px-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-900/50">
            <h2 class="text-base font-bold text-cb-navy">Current family members ({{ $members->count() }})</h2>
        </div>
        @if($members->isEmpty())
            <div class="p-8 text-center text-sm text-slate-500">
                <i class="fas fa-users text-3xl text-slate-300 mb-3 block" aria-hidden="true"></i>
                No family members yet. Add members above so they can sign in to your account.
            </div>
        @else
            <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($members as $member)
                    @php
                        $isEditor = ($member->family_role ?? 'viewer') === 'editor';
                    @endphp
                    <li class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 sm:p-5">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $isEditor ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-sky-800' }} font-semibold" aria-hidden="true">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <p class="font-semibold text-cb-navy truncate flex items-center gap-2">
                                    {{ $member->name }}
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.65rem] font-bold uppercase tracking-wider {{ $isEditor ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-sky-800' }}">
                                        <i class="fas {{ $isEditor ? 'fa-shield-halved' : 'fa-eye' }} text-[0.55rem]" aria-hidden="true"></i>
                                        {{ $isEditor ? 'Editor' : 'Viewer' }}
                                    </span>
                                </p>
                                <div class="text-xs text-slate-600 dark:text-slate-400 mt-0.5 flex flex-wrap gap-x-3 gap-y-1">
                                    @if($member->email)
                                        <span><i class="fas fa-envelope mr-1 text-slate-400" aria-hidden="true"></i>{{ $member->email }}</span>
                                    @endif
                                    @if($member->phone)
                                        <span><i class="fas fa-phone mr-1 text-slate-400" aria-hidden="true"></i>{{ $member->phone }}</span>
                                    @endif
                                    @if($member->must_change_password)
                                        <span class="text-amber-700 dark:text-amber-400 font-medium"><i class="fas fa-clock mr-1" aria-hidden="true"></i>Pending first login</span>
                                    @else
                                        <span class="text-slate-500">Added {{ $member->created_at->diffForHumans() }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 shrink-0">
                            @if($isEditor)
                                <form method="POST" action="{{ route('client.family-members.update-role', $member->id) }}" onsubmit="return confirm('Demote {{ $member->name }} to view-only?');">
                                    @csrf
                                    <input type="hidden" name="role" value="viewer">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 hover:bg-slate-200 transition-colors" title="Make viewer (read-only)">
                                        <i class="fas fa-eye" aria-hidden="true"></i>Make viewer
                                    </button>
                                </form>
                            @elseif($canAddEditors)
                                <form method="POST" action="{{ route('client.family-members.update-role', $member->id) }}" onsubmit="return confirm('Promote {{ $member->name }} to editor?');">
                                    @csrf
                                    <input type="hidden" name="role" value="editor">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 hover:bg-emerald-200 transition-colors" title="Promote to editor (full access)">
                                        <i class="fas fa-shield-halved" aria-hidden="true"></i>Make editor
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('client.family-members.reset-password', $member->id) }}" onsubmit="return confirm('Reset password for {{ $member->name }}?');">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-sky-100 text-sky-800 dark:bg-sky-950/40 dark:text-sky-300 hover:bg-sky-200 transition-colors">
                                    <i class="fas fa-key" aria-hidden="true"></i>Reset password
                                </button>
                            </form>
                            <form method="POST" action="{{ route('client.family-members.destroy', $member->id) }}" onsubmit="return confirm('Remove {{ $member->name }} from your account?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300 hover:bg-rose-200 transition-colors">
                                    <i class="fas fa-trash" aria-hidden="true"></i>Remove
                                </button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('members-container');
    const addBtn = document.getElementById('add-another-member-btn');
    const remainingSlots = {{ $remainingSlots }};
    const canAddEditors = {{ $canAddEditors ? 'true' : 'false' }};
    let memberIndex = 1;

    if (addBtn && container) {
        addBtn.addEventListener('click', function () {
            const currentRows = container.querySelectorAll('.member-row').length;
            if (currentRows >= remainingSlots) {
                alert('You have reached the maximum of ' + remainingSlots + ' member(s) you can add at once.');
                return;
            }

            const rowIdx = memberIndex++;
            const newRow = document.createElement('div');
            newRow.className = 'member-row p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 space-y-3 relative transition-all duration-200';

            let roleHtml = '';
            if (canAddEditors) {
                roleHtml = `
                <div>
                    <label class="cb-label">Access level</label>
                    <div class="flex flex-wrap gap-3 mt-1">
                        <label class="relative flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 cursor-pointer text-xs font-semibold text-slate-700 dark:text-slate-300 has-[:checked]:border-sky-500 has-[:checked]:bg-sky-50 dark:has-[:checked]:bg-sky-950/40">
                            <input type="radio" name="members[${rowIdx}][role]" value="viewer" checked>
                            <span><i class="fas fa-eye text-sky-600 mr-1"></i> Viewer (read-only)</span>
                        </label>
                        <label class="relative flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 cursor-pointer text-xs font-semibold text-slate-700 dark:text-slate-300 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 dark:has-[:checked]:bg-emerald-950/40">
                            <input type="radio" name="members[${rowIdx}][role]" value="editor">
                            <span><i class="fas fa-shield-halved text-emerald-600 mr-1"></i> Editor (full access)</span>
                        </label>
                    </div>
                </div>`;
            } else {
                roleHtml = `<input type="hidden" name="members[${rowIdx}][role]" value="viewer">`;
            }

            newRow.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 member-row-title">Member #${currentRows + 1}</span>
                    <button type="button" class="remove-row-btn text-rose-500 hover:text-rose-700 text-xs font-semibold">
                        <i class="fas fa-trash-can mr-1"></i> Remove
                    </button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="cb-label">Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="members[${rowIdx}][name]" required maxlength="255" class="cb-field" placeholder="Full name">
                    </div>
                    <div>
                        <label class="cb-label">Mobile <span class="text-rose-500">*</span></label>
                        <input type="tel" name="members[${rowIdx}][phone]" required pattern="[6-9][0-9]{9}" maxlength="10" inputmode="numeric" class="cb-field" placeholder="10-digit mobile">
                    </div>
                    <div>
                        <label class="cb-label">Email <span class="text-slate-400 font-normal">(optional)</span></label>
                        <input type="email" name="members[${rowIdx}][email]" maxlength="255" class="cb-field" placeholder="name@example.com">
                    </div>
                </div>
                ${roleHtml}
            `;

            container.appendChild(newRow);
            updateRowNumbers();
        });

        container.addEventListener('click', function (e) {
            const removeBtn = e.target.closest('.remove-row-btn');
            if (removeBtn) {
                const row = removeBtn.closest('.member-row');
                if (row) {
                    row.remove();
                    updateRowNumbers();
                }
            }
        });
    }

    function updateRowNumbers() {
        if (!container) return;
        const rows = container.querySelectorAll('.member-row');
        rows.forEach((row, idx) => {
            const title = row.querySelector('.member-row-title');
            if (title) title.textContent = `Member #${idx + 1}`;
            const removeBtn = row.querySelector('.remove-row-btn');
            if (removeBtn) {
                removeBtn.classList.toggle('hidden', rows.length === 1);
            }
        });
    }
});
</script>
@endpush
@endsection
