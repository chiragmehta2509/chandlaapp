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
            'total_entries' => \App\Models\Entry::whereHas('event', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->count(),
            'subscription_status' => $user->subscription_status,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}

