<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DeviceToken;
use App\Models\Notification;
use App\Models\NotificationUser;
use Illuminate\Support\Facades\DB;

class PushNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get or create admin and test user
        $admin = User::where('email', 'admin@chandlabook.com')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@chandlabook.com',
                'password' => bcrypt('Admin@123'),
                'is_admin' => true,
                'is_active' => true,
            ]);
        }

        $user = User::where('email', 'dev@example.com')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Development User',
                'email' => 'dev@example.com',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]);
        }

        // 2. Clear existing push notification data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        NotificationUser::truncate();
        Notification::truncate();
        DeviceToken::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 3. Seed device tokens for the test user
        DeviceToken::create([
            'user_id' => $user->id,
            'device_token' => 'fcm_mock_token_android_12345',
            'platform' => 'android',
            'device_name' => 'Pixel 7 Pro',
            'app_version' => '1.0.0',
            'is_active' => true,
        ]);

        DeviceToken::create([
            'user_id' => $user->id,
            'device_token' => 'fcm_mock_token_ios_67890',
            'platform' => 'ios',
            'device_name' => 'iPhone 15 Pro Max',
            'app_version' => '1.0.1',
            'is_active' => true,
        ]);

        DeviceToken::create([
            'user_id' => $user->id,
            'device_token' => 'fcm_mock_token_web_54321',
            'platform' => 'web',
            'device_name' => 'Chrome Windows',
            'app_version' => '1.0.0',
            'is_active' => false, // inactive token for testing
        ]);

        // 4. Seed sample notifications
        $notifications = [
            [
                'title' => 'New Feature: Multi-Ledger Support',
                'message' => 'Now you can manage multiple ledgers and sync them with your friends easily. Check it out!',
                'image' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe',
                'action_type' => 'feature',
                'action_value' => 'multi_ledger_feature',
                'send_to' => 'all',
                'status' => 'sent',
            ],
            [
                'title' => 'Special Offer: 50% Off Premium Plan',
                'message' => 'Upgrade to Premium today and enjoy unlimited invitations and ledger reports. Limited time only!',
                'image' => 'https://images.unsplash.com/photo-1546519638-68e109498ffc',
                'action_type' => 'offer',
                'action_value' => 'premium_plan_discount_50',
                'send_to' => 'selected_users',
                'status' => 'sent',
            ],
            [
                'title' => 'Happy Diwali!',
                'message' => 'Team Chandla Book wishes you and your family a very happy, prosperous, and safe Diwali.',
                'image' => 'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9',
                'action_type' => 'screen',
                'action_value' => 'greetings_diwali',
                'send_to' => 'all',
                'status' => 'sent',
            ],
            [
                'title' => 'Scheduled Maintenance Notice',
                'message' => 'Chandla Book will undergo scheduled maintenance on Sunday from 2 AM to 4 AM IST. Systems may be temporarily unavailable.',
                'image' => null,
                'action_type' => 'none',
                'action_value' => null,
                'send_to' => 'all',
                'status' => 'sent',
            ],
            [
                'title' => 'Urgent: Please update your app',
                'message' => 'A new version of Chandla Book is available on Play Store and App Store. Update now for a smoother experience.',
                'image' => null,
                'action_type' => 'url',
                'action_value' => 'https://play.google.com/store/apps/details?id=com.chandlabook.app',
                'send_to' => 'all',
                'status' => 'sent',
            ]
        ];

        foreach ($notifications as $index => $notifData) {
            $notification = Notification::create(array_merge($notifData, [
                'created_by' => $admin->id,
                'created_at' => now()->subHours($index * 3), // staggered timestamps
            ]));

            // Associate notification with regular user
            NotificationUser::create([
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'is_read' => $index > 2, // make some read and some unread
                'read_at' => $index > 2 ? now()->subHours($index * 2) : null,
                'created_at' => $notification->created_at,
            ]);
        }

        $this->command->info('Push Notification seeding completed successfully!');
    }
}
