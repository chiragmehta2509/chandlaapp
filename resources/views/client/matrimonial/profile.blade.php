@extends('layouts.client')

@section('title', 'Matrimonial profile')

@section('content')
<div class="w-full min-w-0 max-w-2xl mx-auto">
    <a href="{{ route('client.matrimonial.index') }}" class="cb-link text-sm inline-flex items-center gap-2 mb-3 sm:mb-4 min-h-[44px] touch-manipulation">
        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Find Partner
    </a>
    <h1 class="cb-page-title">Your matrimonial profile</h1>
    <p class="cb-subtitle mt-1 max-w-2xl break-words">This information is shown only in the Find Partner section. Required fields are marked *</p>

    <form method="post" action="{{ route('client.matrimonial.profile.update') }}" enctype="multipart/form-data" class="cb-card p-4 sm:p-6 mt-4 sm:mt-6 space-y-4 min-w-0">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700">Display name *</label>
            <input type="text" name="display_name" value="{{ old('display_name', $profile->display_name) }}" required maxlength="120" class="mt-1 w-full min-w-0 rounded-lg border border-slate-200 px-3 py-2.5 sm:py-2 text-base sm:text-sm">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700">Age *</label>
                <input type="number" name="age" value="{{ old('age', $profile->age) }}" required min="18" max="99" inputmode="numeric" class="mt-1 w-full min-w-0 rounded-lg border border-slate-200 px-3 py-2.5 sm:py-2 text-base sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Gender *</label>
                <select name="gender" required class="mt-1 w-full min-w-0 rounded-lg border border-slate-200 px-3 py-2.5 sm:py-2 text-base sm:text-sm bg-white">
                    <option value="">Select</option>
                    <option value="male" @selected(old('gender', $profile->gender) === 'male')>Male</option>
                    <option value="female" @selected(old('gender', $profile->gender) === 'female')>Female</option>
                    <option value="other" @selected(old('gender', $profile->gender) === 'other')>Other</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">City *</label>
            <input type="text" name="city" value="{{ old('city', $profile->city) }}" required maxlength="120" class="mt-1 w-full min-w-0 rounded-lg border border-slate-200 px-3 py-2.5 sm:py-2 text-base sm:text-sm" autocapitalize="words" autocomplete="address-level2">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="min-w-0 sm:col-span-1">
                <label class="block text-sm font-medium text-slate-700">Religion</label>
                <input type="text" name="religion" value="{{ old('religion', $profile->religion) }}" maxlength="120" class="mt-1 w-full min-w-0 rounded-lg border border-slate-200 px-3 py-2.5 sm:py-2 text-base sm:text-sm">
            </div>
            <div class="min-w-0 sm:col-span-1">
                <label class="block text-sm font-medium text-slate-700">Caste</label>
                <input type="text" name="caste" value="{{ old('caste', $profile->caste) }}" maxlength="120" class="mt-1 w-full min-w-0 rounded-lg border border-slate-200 px-3 py-2.5 sm:py-2 text-base sm:text-sm">
            </div>
            <div class="min-w-0 sm:col-span-1">
                <label class="block text-sm font-medium text-slate-700">Sub-caste</label>
                <input type="text" name="sub_caste" value="{{ old('sub_caste', $profile->sub_caste) }}" maxlength="120" class="mt-1 w-full min-w-0 rounded-lg border border-slate-200 px-3 py-2.5 sm:py-2 text-base sm:text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Education</label>
            <input type="text" name="education" value="{{ old('education', $profile->education) }}" maxlength="255" class="mt-1 w-full min-w-0 rounded-lg border border-slate-200 px-3 py-2.5 sm:py-2 text-base sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Profession</label>
            <input type="text" name="profession" value="{{ old('profession', $profile->profession) }}" maxlength="255" class="mt-1 w-full min-w-0 rounded-lg border border-slate-200 px-3 py-2.5 sm:py-2 text-base sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Income (optional)</label>
            <input type="text" name="income" value="{{ old('income', $profile->income) }}" placeholder="e.g. ₹5–8 LPA" maxlength="120" class="mt-1 w-full min-w-0 rounded-lg border border-slate-200 px-3 py-2.5 sm:py-2 text-base sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Family details</label>
            <textarea name="family_details" rows="3" class="mt-1 w-full min-w-0 max-w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base sm:text-sm resize-y min-h-[5rem]">{{ old('family_details', $profile->family_details) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">About me</label>
            <textarea name="about_me" rows="4" class="mt-1 w-full min-w-0 max-w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base sm:text-sm resize-y min-h-[6rem]">{{ old('about_me', $profile->about_me) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Partner preferences</label>
            <textarea name="partner_preferences" rows="3" class="mt-1 w-full min-w-0 max-w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base sm:text-sm resize-y min-h-[5rem]">{{ old('partner_preferences', $profile->partner_preferences) }}</textarea>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 space-y-4">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Interest &amp; privacy</p>
            <div class="flex items-start gap-3">
                <input type="checkbox" name="interests_receiving_enabled" value="1" id="ire" class="mt-1.5 h-4 w-4 shrink-0 touch-manipulation" @checked(old('interests_receiving_enabled', $profile->interests_receiving_enabled ?? true))>
                <label for="ire" class="text-sm text-slate-700 leading-snug">Allow other members to send me interest requests</label>
            </div>
            <p class="text-xs text-slate-600 pl-7 -mt-2">If you turn this off, nobody can send you a new interest (existing ones stay as they are until you act on them).</p>
            <a href="{{ route('client.matrimonial.interest-privacy') }}" class="inline-flex text-sm font-medium text-cb-navy hover:underline touch-manipulation">Manage who can send you interest →</a>
        </div>
        <div class="flex items-start gap-3">
            <input type="checkbox" name="phone_visible_to_matches" value="1" id="phv" class="mt-1.5 h-4 w-4 shrink-0 touch-manipulation" @checked(old('phone_visible_to_matches', $profile->phone_visible_to_matches ?? true))>
            <label for="phv" class="text-sm text-slate-700 leading-snug">Show my account phone to matches when they have a paid plan</label>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Photo * {{ $profile->photo_path ? '(replace)' : '' }}</label>
            @if($profile->photo_path)
                <div class="mt-2"><img src="{{ $profile->photoUrl() }}" alt="Current" class="h-32 w-32 sm:h-40 sm:w-40 object-cover rounded-lg border border-slate-200 max-w-full"></div>
            @endif
            <input type="file" name="photo" accept="image/*" class="mt-2 block w-full max-w-full text-sm text-slate-600 file:mr-2 file:min-h-[2.5rem] file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-slate-100 file:text-cb-navy" @if(!$profile->photo_path) required @endif>
        </div>
        <div class="pt-1">
            <button type="submit" class="cb-btn cb-btn-navy w-full sm:w-auto min-h-[2.75rem] px-6 touch-manipulation">Save profile</button>
        </div>
    </form>
</div>
@endsection
