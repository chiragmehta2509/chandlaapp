<?php

namespace App\Http\Controllers\Api\PushNotification;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationUserResource;
use App\Models\NotificationUser;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    /**
     * Get paginated notifications history for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = (int)$request->query('per_page', 15);
        
        $notifications = $request->user()->notificationUsers()
            ->with('notification')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Notifications retrieved successfully.',
            'data' => [
                'items' => NotificationUserResource::collection($notifications->items()),
                'pagination' => [
                    'total' => $notifications->total(),
                    'count' => $notifications->count(),
                    'per_page' => $notifications->perPage(),
                    'current_page' => $notifications->currentPage(),
                    'total_pages' => $notifications->lastPage(),
                ]
            ]
        ], 200);
    }

    /**
     * Get the count of unread notifications for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreadCount(Request $request)
    {
        $unreadCount = $request->user()->notificationUsers()
            ->where('is_read', false)
            ->count();

        return response()->json([
            'status' => true,
            'message' => 'Unread count retrieved successfully.',
            'data' => [
                'unread_count' => $unreadCount
            ]
        ], 200);
    }

    /**
     * Mark a specific notification as read.
     *
     * @param Request $request
     * @param mixed $id (supports either notification_users.id or notifications.id)
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request, $id)
    {
        $notificationUser = $request->user()->notificationUsers()
            ->where(function ($query) use ($id) {
                $query->where('id', $id)
                      ->orWhere('notification_id', $id);
            })->first();

        if (!$notificationUser) {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found.',
                'errors' => (object)[]
            ], 404);
        }

        if (!$notificationUser->is_read) {
            $notificationUser->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Notification marked as read successfully.',
            'data' => new NotificationUserResource($notificationUser)
        ], 200);
    }

    /**
     * Mark all notifications for the authenticated user as read.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllRead(Request $request)
    {
        $request->user()->notificationUsers()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'status' => true,
            'message' => 'All notifications marked as read successfully.',
            'data' => (object)[]
        ], 200);
    }

    /**
     * Delete a specific notification from user history.
     *
     * @param Request $request
     * @param mixed $id (supports either notification_users.id or notifications.id)
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $notificationUser = $request->user()->notificationUsers()
            ->where(function ($query) use ($id) {
                $query->where('id', $id)
                      ->orWhere('notification_id', $id);
            })->first();

        if (!$notificationUser) {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found.',
                'errors' => (object)[]
            ], 404);
        }

        $notificationUser->delete();

        return response()->json([
            'status' => true,
            'message' => 'Notification deleted successfully.',
            'data' => (object)[]
        ], 200);
    }
}
