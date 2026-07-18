<?php

namespace App\Http\Controllers\Matrimonial;

use App\Http\Controllers\Controller;
use App\Models\MatrimonialInterest;
use App\Models\MatrimonialInterestBlock;
use App\Models\MatrimonialProfile;
use App\Support\MatrimonialPlan as MatrimonialPlanSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MatrimonialInterestController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $received = MatrimonialInterest::query()
            ->where('to_user_id', $userId)
            ->with(['fromUser.matrimonialProfile'])
            ->orderByDesc('created_at')
            ->get();
        $sent = MatrimonialInterest::query()
            ->where('from_user_id', $userId)
            ->with(['toUser.matrimonialProfile'])
            ->orderByDesc('created_at')
            ->get();
        $viewerHasPlan = MatrimonialPlanSupport::isPlanActive($userId);
        $blockedFromMeIds = MatrimonialInterestBlock::query()
            ->where('user_id', $userId)
            ->pluck('blocked_user_id')
            ->all();

        return view('client.matrimonial.interests', [
            'received' => $received,
            'sent' => $sent,
            'viewerHasPlan' => $viewerHasPlan,
            'blockedFromMeIds' => $blockedFromMeIds,
        ]);
    }

    public function blocks()
    {
        $blocks = MatrimonialInterestBlock::query()
            ->where('user_id', Auth::id())
            ->with(['blockedUser.matrimonialProfile'])
            ->orderByDesc('created_at')
            ->get();
        $profile = MatrimonialProfile::where('user_id', Auth::id())->first();

        return view('client.matrimonial.blocks', compact('blocks', 'profile'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'to_user_id' => [
                'required',
                'integer',
                Rule::notIn([Auth::id()]),
            ],
        ]);

        $toId = (int) $request->input('to_user_id');
        $toProfile = MatrimonialProfile::where('user_id', $toId)->where('is_complete', true)->firstOrFail();

        if (! MatrimonialProfile::where('user_id', Auth::id())->where('is_complete', true)->exists()) {
            return back()->withErrors(['interest' => 'Complete your profile first.']);
        }

        if (! $toProfile->interests_receiving_enabled) {
            return back()->withErrors(['interest' => 'This member is not accepting new interest requests.']);
        }

        if (MatrimonialInterestBlock::query()
            ->where('user_id', $toId)
            ->where('blocked_user_id', Auth::id())
            ->exists()) {
            return back()->withErrors(['interest' => 'You cannot send interest to this member.']);
        }

        MatrimonialInterest::updateOrCreate(
            [
                'from_user_id' => Auth::id(),
                'to_user_id' => $toId,
            ],
            ['status' => MatrimonialInterest::STATUS_PENDING]
        );

        return back()->with('success', 'Interest sent.');
    }

    public function block(Request $request)
    {
        $data = $request->validate([
            'blocked_user_id' => 'required|integer|exists:users,id',
        ]);
        $blockedId = (int) $data['blocked_user_id'];
        if ($blockedId === Auth::id()) {
            abort(403);
        }

        MatrimonialInterestBlock::query()->firstOrCreate([
            'user_id' => Auth::id(),
            'blocked_user_id' => $blockedId,
        ]);

        MatrimonialInterest::query()
            ->where('from_user_id', $blockedId)
            ->where('to_user_id', Auth::id())
            ->where('status', MatrimonialInterest::STATUS_PENDING)
            ->update(['status' => MatrimonialInterest::STATUS_REJECTED]);

        return back()->with('success', 'This member can no longer send you interest. Pending requests from them were declined.');
    }

    public function unblock(int $blockedUserId)
    {
        $deleted = MatrimonialInterestBlock::query()
            ->where('user_id', Auth::id())
            ->where('blocked_user_id', $blockedUserId)
            ->delete();
        if ($deleted === 0) {
            abort(404);
        }

        return back()->with('success', 'Unblocked. They may send interest again if you both meet the plan rules.');
    }

    public function accept(int $id)
    {
        $row = MatrimonialInterest::where('id', $id)
            ->where('to_user_id', Auth::id())
            ->firstOrFail();
        if ($row->status !== MatrimonialInterest::STATUS_PENDING) {
            return back()->with('info', 'This interest is no longer pending.');
        }
        $row->update(['status' => MatrimonialInterest::STATUS_ACCEPTED]);

        return back()->with('success', 'Interest accepted.');
    }

    public function reject(int $id)
    {
        $row = MatrimonialInterest::where('id', $id)
            ->where('to_user_id', Auth::id())
            ->firstOrFail();
        if ($row->status !== MatrimonialInterest::STATUS_PENDING) {
            return back()->with('info', 'This interest is no longer pending.');
        }
        $row->update(['status' => MatrimonialInterest::STATUS_REJECTED]);

        return back()->with('success', 'Interest declined.');
    }
}
