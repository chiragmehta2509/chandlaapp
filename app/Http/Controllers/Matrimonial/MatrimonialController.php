<?php

namespace App\Http\Controllers\Matrimonial;

use App\Http\Controllers\Controller;
use App\Models\MatrimonialInterest;
use App\Models\MatrimonialInterestBlock;
use App\Models\MatrimonialProfile;
use App\Support\MatrimonialPlan as MatrimonialPlanSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MatrimonialController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $profile = MatrimonialProfile::where('user_id', $user->id)->first();

        if (!$profile || !$profile->is_complete) {
            return redirect()->route('client.matrimonial.profile.edit');
        }

        $query = MatrimonialProfile::query()
            ->where('is_complete', true)
            ->where('user_id', '!=', $user->id)
            ->with('user');

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->string('city')->trim() . '%');
        }
        if ($request->filled('caste')) {
            $query->where('caste', 'like', '%' . $request->string('caste')->trim() . '%');
        }
        if ($request->filled('education')) {
            $query->where('education', 'like', '%' . $request->string('education')->trim() . '%');
        }
        if ($request->filled('age_min')) {
            $query->where('age', '>=', (int) $request->input('age_min'));
        }
        if ($request->filled('age_max')) {
            $query->where('age', '<=', (int) $request->input('age_max'));
        }

        $matches = $query->orderBy('updated_at', 'desc')->paginate(12)->withQueryString();
        $viewerHasPlan = MatrimonialPlanSupport::isPlanActive($user->id);
        $activePlan = MatrimonialPlanSupport::activePlanFor($user->id);

        return view('client.matrimonial.index', compact('matches', 'viewerHasPlan', 'activePlan', 'profile'));
    }

    public function show(MatrimonialProfile $matrimonialProfile)
    {
        $user = Auth::user();
        if ($matrimonialProfile->user_id === $user->id) {
            return redirect()->route('client.matrimonial.profile.edit');
        }
        if (!$matrimonialProfile->is_complete) {
            abort(404);
        }

        $viewerHasPlan = MatrimonialPlanSupport::isPlanActive($user->id);
        $ownProfile = MatrimonialProfile::where('user_id', $user->id)->first();
        if (!$ownProfile?->is_complete) {
            return redirect()
                ->route('client.matrimonial.profile.edit')
                ->with('info', 'Complete your profile to view others in detail.');
        }

        $targetUser = $matrimonialProfile->user;
        $interest = MatrimonialInterest::where('from_user_id', $user->id)
            ->where('to_user_id', $matrimonialProfile->user_id)
            ->first();
        $reverseInterest = MatrimonialInterest::where('from_user_id', $matrimonialProfile->user_id)
            ->where('to_user_id', $user->id)
            ->first();

        $blockedFromSending = MatrimonialInterestBlock::query()
            ->where('user_id', $matrimonialProfile->user_id)
            ->where('blocked_user_id', $user->id)
            ->exists();

        $canSendInterest = $viewerHasPlan
            && $matrimonialProfile->interests_receiving_enabled
            && !$blockedFromSending;

        $interestBlockReason = null;
        if (!$matrimonialProfile->interests_receiving_enabled) {
            $interestBlockReason = 'not_accepting';
        } elseif ($blockedFromSending) {
            $interestBlockReason = 'you_are_blocked';
        }

        return view('client.matrimonial.show', [
            'p' => $matrimonialProfile,
            'targetUser' => $targetUser,
            'viewerHasPlan' => $viewerHasPlan,
            'interest' => $interest,
            'reverseInterest' => $reverseInterest,
            'canSendInterest' => $canSendInterest,
            'interestBlockReason' => $interestBlockReason,
        ]);
    }

    public function profileEdit()
    {
        $user = Auth::user();
        $profile = MatrimonialProfile::firstOrNew(['user_id' => $user->id]);

        return view('client.matrimonial.profile', compact('profile', 'user'));
    }

    public function profileUpdate(Request $request)
    {
        $user = Auth::user();
        $profile = MatrimonialProfile::firstOrNew(['user_id' => $user->id]);

        $rules = [
            'display_name' => 'required|string|max:120',
            'age' => 'required|integer|min:18|max:99',
            'gender' => 'required|in:male,female,other',
            'city' => 'required|string|max:120',
            'religion' => 'nullable|string|max:120',
            'caste' => 'nullable|string|max:120',
            'sub_caste' => 'nullable|string|max:120',
            'education' => 'nullable|string|max:255',
            'profession' => 'nullable|string|max:255',
            'income' => 'nullable|string|max:120',
            'family_details' => 'nullable|string|max:20000',
            'about_me' => 'nullable|string|max:20000',
            'partner_preferences' => 'nullable|string|max:20000',
            'photo' => ($profile->exists && $profile->photo_path) ? 'nullable|image|max:4096' : 'required|image|max:4096',
        ];

        $data = $request->validate($rules);
        if ($request->hasFile('photo')) {
            if ($profile->photo_path) {
                Storage::disk('public')->delete($profile->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('matrimonial/photos', 'public');
        }
        unset($data['photo']);
        $data['phone_visible_to_matches'] = $request->has('phone_visible_to_matches');
        $data['interests_receiving_enabled'] = $request->has('interests_receiving_enabled');
        $data['is_complete'] = true;
        if (!$profile->exists) {
            $data['user_id'] = $user->id;
        }
        $profile->fill($data);
        $profile->save();

        return redirect()
            ->route('client.matrimonial.index')
            ->with('success', 'Profile saved. Browse matches on Find Partner.');
    }
}
