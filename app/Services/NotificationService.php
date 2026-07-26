<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\User;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * @var FirebaseService
     */
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Send notification to all or selected users.
     *
     * @param array $data
     * @param int|null $createdBy
     * @return Notification
     */
    public function send(array $data, ?int $createdBy = null): Notification
    {
        return DB::transaction(function () use ($data, $createdBy) {
            $isScheduled = isset($data['schedule_at']) && strtotime($data['schedule_at']) > time();
            $status = $isScheduled ? 'pending' : 'sent';

            // 1. Create notification record
            $notification = Notification::create([
                'title' => $data['title'],
                'message' => $data['message'],
                'image' => $data['image'] ?? null,
                'action_type' => $data['action_type'] ?? 'none',
                'action_value' => $data['action_value'] ?? null,
                'created_by' => $createdBy,
                'send_to' => $data['send_to'],
                'schedule_at' => $data['schedule_at'] ?? null,
                'status' => $status,
            ]);

            // If it is scheduled for the future, we don't send FCM or attach users yet
            if ($status === 'pending') {
                return $notification;
            }

            // 2. Identify target user IDs
            $userIds = [];
            if ($data['send_to'] === 'all') {
                $userIds = User::where('is_active', true)
                    ->where('is_deleted', false)
                    ->pluck('id')
                    ->toArray();
            } else {
                $userIds = $data['user_ids'] ?? [];
            }

            if (empty($userIds)) {
                return $notification;
            }

            // 3. Create notification_users records in chunks for efficiency
            $notificationUsers = [];
            $now = now();
            foreach ($userIds as $userId) {
                $notificationUsers[] = [
                    'notification_id' => $notification->id,
                    'user_id' => $userId,
                    'is_read' => false,
                    'read_at' => null,
                    'created_at' => $now,
                ];
            }

            foreach (array_chunk($notificationUsers, 500) as $chunk) {
                NotificationUser::insert($chunk);
            }

            // 4. Send FCM Push Notifications to active device tokens
            $tokens = DeviceToken::whereIn('user_id', $userIds)
                ->where('is_active', true)
                ->pluck('device_token')
                ->toArray();

            if (!empty($tokens)) {
                $fcmPayload = [
                    'title' => $notification->title,
                    'body' => $notification->message,
                    'image' => $notification->image,
                    'action_type' => $notification->action_type,
                    'action_value' => $notification->action_value,
                ];

                foreach ($tokens as $token) {
                    // Send to each token via the FirebaseService
                    $this->firebaseService->sendNotification($token, $fcmPayload);
                }
            }

            return $notification;
        });
    }
}
