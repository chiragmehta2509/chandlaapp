<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DebugSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        echo "1. Seeding Users...\n";
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'dev@example.com'],
            [
                'name' => 'Development User',
                'phone' => '9876543210',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]
        );

        echo "2. Seeding Contacts...\n";
        $contacts = [];
        for ($i = 0; $i < 5; $i++) {
            $contacts[] = \App\Models\Contact::create([
                'user_id' => $user->id,
                'name' => $faker->name,
                'phone' => $faker->numerify('##########'),
                'email' => $faker->unique()->safeEmail,
                'relationship' => 'Friend',
            ]);
        }

        echo "3. Seeding Events...\n";
        $events = [];
        for ($i = 0; $i < 2; $i++) {
            $events[] = \App\Models\Event::create([
                'user_id' => $user->id,
                'title' => $faker->sentence(2),
                'event_date' => now()->addDays(rand(1, 30)),
                'event_time' => '10:00',
                'venue' => $faker->city,
                'event_type' => 'other',
            ]);
        }

        echo "4. Seeding Invitations...\n";
        foreach ($events as $event) {
            \App\Models\Invitation::create([
                'event_id' => $event->id,
                'invitation_code' => \Illuminate\Support\Str::random(10),
                'type' => 'digital',
                'status' => 'draft',
            ]);
        }

        echo "5. Seeding UPI Transactions...\n";
        foreach ($events as $event) {
            \App\Models\UPITransaction::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'transaction_id' => 'TXN_' . \Illuminate\Support\Str::random(10),
                'amount' => 1000,
                'status' => 'pending',
                'payment_method' => 'upi',
            ]);
        }

        echo "6. Seeding Notifications...\n";
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'title' => 'Test',
            'body' => 'Test Body',
            'type' => 'system',
            'notifiable_type' => \App\Models\User::class,
            'notifiable_id' => $user->id,
        ]);

        echo "All steps finished!\n";
    }
}
