<?php

namespace App\Http\Controllers\Api\Notification;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\DeviceToken;
use App\Models\UserSetting;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    protected $fcmService;

    public function __construct(FCMService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $notifications = $request->user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    public function unread(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->unread()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'count' => $notifications->count()
        ]);
    }

    public function show(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $notification
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'data' => $notification
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()
            ->notifications()
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }

    public function clearAll(Request $request)
    {
        $request->user()->notifications()->delete();

        return response()->json([
            'success' => true,
            'message' => 'All notifications cleared'
        ]);
    }

    public function registerDevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'platform' => 'required|string|in:android,ios,web',
            'device_id' => 'nullable|string',
            'device_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $device = DeviceToken::updateOrCreate(
            [
                'token' => $request->token,
            ],
            [
                'user_id' => $request->user()->id,
                'platform' => $request->platform,
                'device_id' => $request->device_id,
                'device_name' => $request->device_name,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Device registered successfully',
            'data' => $device
        ]);
    }

    public function unregisterDevice(Request $request, $id)
    {
        $device = $request->user()
            ->deviceTokens()
            ->findOrFail($id);

        $device->update(['is_active' => false]);
        $device->delete();

        return response()->json([
            'success' => true,
            'message' => 'Device unregistered successfully'
        ]);
    }

    public function listDevices(Request $request)
    {
        $devices = $request->user()
            ->deviceTokens()
            ->active()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $devices
        ]);
    }

    public function getPreferences(Request $request)
    {
        $settings = $request->user()->settings;

        if (!$settings) {
            $settings = UserSetting::create([
                'user_id' => $request->user()->id,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'push_notifications_enabled' => $settings->push_notifications_enabled,
                'email_notifications_enabled' => $settings->email_notifications_enabled,
                'sms_notifications_enabled' => $settings->sms_notifications_enabled,
            ]
        ]);
    }

    public function updatePreferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'push_notifications_enabled' => 'nullable|boolean',
            'email_notifications_enabled' => 'nullable|boolean',
            'sms_notifications_enabled' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $settings = $request->user()->settings;

        if (!$settings) {
            $settings = UserSetting::create([
                'user_id' => $request->user()->id,
            ]);
        }

        $settings->update($request->only([
            'push_notifications_enabled',
            'email_notifications_enabled',
            'sms_notifications_enabled',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Preferences updated successfully',
            'data' => $settings
        ]);
    }
}

