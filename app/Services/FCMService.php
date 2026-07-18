<?php

namespace App\Services;

use App\Models\User;
use App\Models\DeviceToken;
use App\Models\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FCMNotification;
use Illuminate\Support\Facades\Log;

class FCMService
{
    protected $messaging;

    public function __construct()
    {
        $credentialsPath = config('firebase.credentials_path');
        
        if (file_exists($credentialsPath)) {
            $factory = (new Factory)->withServiceAccount($credentialsPath);
            $this->messaging = $factory->createMessaging();
        } else {
            Log::warning('Firebase credentials file not found');
        }
    }

    public function sendToUser(User $user, string $title, string $body, array $data = [])
    {
        if (!$this->messaging) {
            return false;
        }

        // Check user notification preferences
        $settings = $user->settings;
        if ($settings && !$settings->push_notifications_enabled) {
            return false;
        }

        $tokens = $user->deviceTokens()->active()->pluck('token')->toArray();

        if (empty($tokens)) {
            return false;
        }

        // Create notification record
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
            'type' => $data['type'] ?? 'system',
            'data' => $data,
            'sent_at' => now(),
        ]);

        try {
            $message = CloudMessage::new()
                ->withNotification(FCMNotification::create($title, $body))
                ->withData($data);

            $report = $this->messaging->sendMulticast($message, $tokens);

            // Update device tokens based on results
            foreach ($report->getItems() as $index => $result) {
                if ($result->isSuccessful()) {
                    DeviceToken::where('token', $tokens[$index])
                        ->update(['last_used_at' => now()]);
                } else {
                    // Remove invalid tokens
                    DeviceToken::where('token', $tokens[$index])->delete();
                }
            }

            return $report->hasFailures() ? false : true;
        } catch (\Exception $e) {
            Log::error('FCM Send Error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendToToken(string $token, string $title, string $body, array $data = [])
    {
        if (!$this->messaging) {
            return false;
        }

        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(FCMNotification::create($title, $body))
                ->withData($data);

            $this->messaging->send($message);

            DeviceToken::where('token', $token)
                ->update(['last_used_at' => now()]);

            return true;
        } catch (\Exception $e) {
            Log::error('FCM Send Error: ' . $e->getMessage());
            
            // Remove invalid token
            DeviceToken::where('token', $token)->delete();
            
            return false;
        }
    }

    public function sendToMultipleUsers(array $userIds, string $title, string $body, array $data = [])
    {
        $users = User::whereIn('id', $userIds)->get();
        $successCount = 0;

        foreach ($users as $user) {
            if ($this->sendToUser($user, $title, $body, $data)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    public function sendToTopic(string $topic, string $title, string $body, array $data = [])
    {
        if (!$this->messaging) {
            return false;
        }

        try {
            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification(FCMNotification::create($title, $body))
                ->withData($data);

            $this->messaging->send($message);
            return true;
        } catch (\Exception $e) {
            Log::error('FCM Topic Send Error: ' . $e->getMessage());
            return false;
        }
    }
}

