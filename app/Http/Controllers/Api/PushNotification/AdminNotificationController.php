<?php

namespace App\Http\Controllers\Api\PushNotification;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendPushNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class AdminNotificationController extends Controller
{
    /**
     * @var NotificationService
     */
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get paginated history of sent notifications.
     */
    public function index()
    {
        $notifications = \App\Models\Notification::withCount('users')
            ->orderBy('created_at', 'desc')
            ->paginate(request('per_page', 20));

        return response()->json([
            'status' => true,
            'message' => 'Notifications history retrieved.',
            'data' => $notifications
        ]);
    }

    /**
     * Send push notification.
     *
     * @param SendPushNotificationRequest $request
     * @return JsonResponse
     */
    public function send(SendPushNotificationRequest $request): JsonResponse
    {
        $adminId = auth()->id();
        
        $notification = $this->notificationService->send($request->validated(), $adminId);

        $message = $notification->status === 'pending'
            ? 'Notification scheduled successfully.'
            : 'Notification sent successfully.';

        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => new NotificationResource($notification)
        ], 200);
    }
}
