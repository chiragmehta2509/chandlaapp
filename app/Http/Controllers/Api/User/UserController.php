<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getProfile(Request $request)
    {
        $user = $request->user()->load('settings');
        
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|unique:users,phone,' . $request->user()->id,
            'language' => 'nullable|string|in:en,hi,gu',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $user->update($request->only(['name', 'phone', 'language']));

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'update_profile',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }

    public function uploadAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Delete old avatar
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Avatar uploaded successfully',
            'data' => [
                'avatar' => Storage::url($path)
            ]
        ]);
    }

    public function deleteAvatar(Request $request)
    {
        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Avatar deleted successfully'
        ]);
    }

    public function getSubscription(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $user->subscription_status,
                'expires_at' => $user->subscription_expires_at,
                'is_active' => $user->subscription_status === 'premium' &&
                              $user->subscription_expires_at &&
                              $user->subscription_expires_at->isFuture(),
            ]
        ]);
    }

    /**
     * GET /api/v1/user/active-plan
     * Returns the user's current active plan with all unlocked features.
     */
    public function activePlan(Request $request)
    {
        $user  = $request->user();
        $level = $user->planLevel();
        $packsConfig = config('packs');
        $levelNames  = $packsConfig['level_names'] ?? [];

        // Build a list of which packs are activated with their timestamps
        $packs = [
            [
                'key'        => 'celebration',
                'label'      => 'Celebration Pack',
                'active'     => $user->celebration_pack_paid_at !== null,
                'activated_at' => $user->celebration_pack_paid_at,
            ],
            [
                'key'        => 'ledger_duo',
                'label'      => 'Host Plus Plan',
                'active'     => $user->ledger_duo_pack_paid_at !== null,
                'activated_at' => $user->ledger_duo_pack_paid_at,
            ],
            [
                'key'        => 'guest_pay_single',
                'label'      => 'Guest Contribution',
                'active'     => $user->guest_pay_single_event_credits > 0,
                'credits'    => (int) ($user->guest_pay_single_event_credits ?? 0),
            ],
            [
                'key'        => 'family',
                'label'      => 'Family Plan',
                'active'     => $user->family_pack_paid_at !== null,
                'activated_at' => $user->family_pack_paid_at,
            ],
            [
                'key'        => 'premium_bundle',
                'label'      => 'Premium Host',
                'active'     => $user->premium_bundle_paid_at !== null,
                'activated_at' => $user->premium_bundle_paid_at,
            ],
            [
                'key'        => 'professional',
                'label'      => 'Professional',
                'active'     => $user->professional_pack_paid_at !== null,
                'activated_at' => $user->professional_pack_paid_at,
            ],
            [
                'key'        => 'enterprise',
                'label'      => 'Enterprise',
                'active'     => $user->enterprise_pack_paid_at !== null,
                'activated_at' => $user->enterprise_pack_paid_at,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'plan_level'       => $level,
                'plan_name'        => $levelNames[$level] ?? 'Unknown',
                'max_events'       => $user->maxEventsAllowed(),
                'max_family_editors' => $user->maxFamilyEditorsAllowed(),
                'features' => [
                    'celebration_pack'         => $user->hasCelebrationPackAccess(),
                    'direct_gpay_qr'           => $user->hasDirectGpayQrUnlocked(),
                    'unlimited_chandla'        => $user->hasLedgerUnlimitedChandla(),
                    'premium_chandla_bundle'   => $user->hasPremiumChandlaBundle(),
                    'advanced_analytics'       => $user->hasAdvancedAnalytics(),
                    'can_add_family_editors'   => $user->canAddFamilyEditors(),
                ],
                'packs'            => $packs,
                'guest_pay_credits' => (int) ($user->guest_pay_single_event_credits ?? 0),
            ]
        ]);
    }

    /**
     * POST /api/v1/user/plan/update
     * Called after a Razorpay payment to record success or failure.
     *
     * On SUCCESS send: status=success, razorpay_order_id, razorpay_payment_id, razorpay_signature
     * On FAILURE send: status=failed, razorpay_order_id, failure_reason (optional)
     */
    public function updatePlan(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'status'              => 'required|in:success,failed',
            'razorpay_order_id'   => 'required|string|max:64',
            'razorpay_payment_id' => 'required_if:status,success|nullable|string|max:64',
            'razorpay_signature'  => 'required_if:status,success|nullable|string|max:512',
            'failure_reason'      => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user    = $request->user();
        $ownerId = $user->dataOwnerId();

        $txn = \App\Models\PaymentTransaction::where('razorpay_order_id', $request->razorpay_order_id)
            ->where('user_id', $ownerId)
            ->first();

        if (!$txn) {
            return response()->json(['success' => false, 'message' => 'Transaction not found.'], 404);
        }

        // Already processed — return current state
        if ($txn->status !== \App\Models\PaymentTransaction::STATUS_PENDING) {
            return response()->json([
                'success' => true,
                'message' => 'Transaction already processed.',
                'data'    => [
                    'status'     => $txn->status,
                    'plan_level' => $user->fresh()->planLevel(),
                    'plan_name'  => (config('packs.level_names') ?? [])[$user->fresh()->planLevel()] ?? 'Unknown',
                ]
            ]);
        }

        if ($request->status === 'failed') {
            $txn->update([
                'status'         => \App\Models\PaymentTransaction::STATUS_FAILED,
                'failure_reason' => $request->input('failure_reason', 'Payment failed or cancelled by user.'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment marked as failed.',
                'data'    => ['status' => 'failed']
            ]);
        }

        // status === success → verify signature and activate pack
        try {
            $razorpay = new \App\Services\RazorpayService();

            $razorpay->verifySignature(
                $request->razorpay_order_id,
                $request->razorpay_payment_id,
                $request->razorpay_signature
            );

            $paymentData   = [];
            $paymentMethod = null;
            try {
                $paymentData   = $razorpay->fetchPayment($request->razorpay_payment_id);
                $paymentMethod = $paymentData['method'] ?? null;
            } catch (\Throwable $e) {
                // non-fatal
            }

            $razorpay->markSuccess(
                $txn,
                $request->razorpay_payment_id,
                $request->razorpay_signature,
                $paymentMethod,
                array_intersect_key($paymentData, array_flip(['id', 'status', 'method', 'amount', 'order_id', 'captured', 'created_at']))
            );

            // Activate the pack (reuse PackController logic)
            $packController = new \App\Http\Controllers\Api\Pack\PackController();
            $packController->activatePackPublic($txn->package_key, $ownerId, $request->razorpay_payment_id);

            $freshUser = $user->fresh();
            $level     = $freshUser->planLevel();

            return response()->json([
                'success' => true,
                'message' => 'Plan activated successfully.',
                'data' => [
                    'status'     => 'success',
                    'plan_level' => $level,
                    'plan_name'  => (config('packs.level_names') ?? [])[$level] ?? 'Unknown',
                ]
            ]);
        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            $txn->update([
                'status'         => \App\Models\PaymentTransaction::STATUS_FAILED,
                'failure_reason' => 'Signature verification failed.',
            ]);
            return response()->json(['success' => false, 'message' => 'Payment signature verification failed.'], 422);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('UserController::updatePlan failed', [
                'order_id' => $request->razorpay_order_id,
                'error'    => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Plan update failed. Please contact support.'], 500);
        }
    }

    public function upgradeSubscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan' => 'required|string|in:monthly,yearly',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // This would typically integrate with payment gateway
        // For now, just update the subscription
        $user = $request->user();
        $expiresAt = $request->plan === 'yearly' 
            ? now()->addYear() 
            : now()->addMonth();

        $user->update([
            'subscription_status' => 'premium',
            'subscription_expires_at' => $expiresAt,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription upgraded successfully',
            'data' => [
                'status' => $user->subscription_status,
                'expires_at' => $user->subscription_expires_at,
            ]
        ]);
    }

    public function cancelSubscription(Request $request)
    {
        $user = $request->user();
        $user->update([
            'subscription_status' => 'expired',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled successfully'
        ]);
    }

    public function deactivateAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!\Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password'
            ], 401);
        }

        $user->update(['is_active' => false]);

        // Revoke all tokens
        $user->tokens()->delete();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'deactivate_account',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Account deactivated successfully'
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!\Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password'
            ], 401);
        }

        $user->update([
            'is_deleted' => true,
            'deleted_at' => now(),
        ]);

        // Revoke all tokens
        $user->tokens()->delete();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'delete_account',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully'
        ]);
    }

    public function getStats(Request $request)
    {
        $user = $request->user();
        $userId = $user->dataOwnerId();

        $stats = [
            'total_events' => \App\Models\Event::where('user_id', $userId)->count(),
            'active_events' => \App\Models\Event::where('user_id', $userId)->active()->count(),
            'archived_events' => \App\Models\Event::where('user_id', $userId)->archived()->count(),
            'total_contacts' => \App\Models\Contact::where('user_id', $userId)->count(),
            'favorite_contacts' => \App\Models\Contact::where('user_id', $userId)->favorite()->count(),
            'total_entries' => \App\Models\Chandla::whereIn('user_id', $user->allowedUserIds())->count(),
            'subscription_status' => $user->subscription_status,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}

