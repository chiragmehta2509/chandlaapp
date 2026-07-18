<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user first
        $this->call(AdminUserSeeder::class);
        
        // Create event types
        $this->call(EventTypeSeeder::class);

        $faker = \Faker\Factory::create();

        // 1. Create Users
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'dev@example.com'],
            [
                'name' => 'Development User',
                'phone' => '9876543210',
                'password' => bcrypt('password'),
                'is_active' => true,
                'subscription_status' => 'premium',
                'subscription_expires_at' => now()->addYear(),
            ]
        );

        // 2. Create Contacts
        $contacts = [];
        for ($i = 0; $i < 20; $i++) {
            $contacts[] = \App\Models\Contact::create([
                'user_id' => $user->id,
                'name' => $faker->name,
                'phone' => $faker->numerify('##########'),
                'email' => $faker->unique()->safeEmail,
                'address' => $faker->address,
                'relationship' => $faker->randomElement(['Family', 'Friend', 'Colleague', 'Relative']),
                'is_favorite' => $faker->boolean(20),
            ]);
        }

        // 3. Create Events
        $events = [];
        $eventTypes = ['wedding', 'birthday', 'anniversary', 'other'];
        for ($i = 0; $i < 5; $i++) {
            $events[] = \App\Models\Event::create([
                'user_id' => $user->id,
                'title' => $faker->sentence(3),
                'description' => $faker->paragraph,
                'event_date' => $faker->dateTimeBetween('now', '+6 months'),
                'event_time' => $faker->time('H:i'),
                'venue' => $faker->company . ' Hall',
                'event_type' => $faker->randomElement($eventTypes),
                'is_active' => true,
            ]);
        }

        // 4. Create Entries (Guests)
        foreach ($events as $event) {
            foreach (array_slice($contacts, 0, rand(5, 10)) as $contact) {
                \App\Models\Entry::create([
                    'event_id' => $event->id,
                    'contact_id' => $contact->id,
                    'guest_name' => $contact->name,
                    'guest_phone' => $contact->phone,
                    'guest_email' => $contact->email,
                    'adults_count' => rand(1, 4),
                    'children_count' => rand(0, 2),
                    'status' => $faker->randomElement(['confirmed', 'pending', 'declined']),
                    'confirmed_at' => $faker->dateTimeBetween('-1 month', 'now'),
                ]);
            }
        }

        // 5. Create Invitations
        foreach ($events as $event) {
            \App\Models\Invitation::create([
                'event_id' => $event->id,
                'invitation_code' => \Illuminate\Support\Str::random(10),
                'type' => $faker->randomElement(['digital', 'pdf', 'image']),
                'custom_message' => $faker->paragraph,
                'status' => $faker->randomElement(['draft', 'sent', 'opened', 'accepted', 'declined']),
                'template_id' => 'TEMP_' . rand(1, 5),
            ]);
        }

        // 6. Create UPI Transactions
        for ($i = 0; $i < 10; $i++) {
            \App\Models\UPITransaction::create([
                'user_id' => $user->id,
                'transaction_id' => 'TXN_' . \Illuminate\Support\Str::random(12),
                'razorpay_order_id' => 'order_' . \Illuminate\Support\Str::random(12),
                'razorpay_payment_id' => 'pay_' . \Illuminate\Support\Str::random(12),
                'amount' => rand(100, 5000),
                'status' => $faker->randomElement(['pending', 'completed', 'failed', 'refunded']),
                'payment_method' => $faker->randomElement(['upi', 'card', 'netbanking', 'wallet']),
            ]);
        }

        // 7. Create Notifications
        for ($i = 0; $i < 10; $i++) {
            $e = $faker->randomElement($events);
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => $faker->sentence,
                'body' => $faker->paragraph,
                'type' => $faker->randomElement(['event', 'invitation', 'payment', 'system', 'reminder']),
                'notifiable_type' => \App\Models\Event::class,
                'notifiable_id' => $e->id,
                'is_read' => $faker->boolean,
            ]);
        }

        // 8. Create Device Tokens
        for ($i = 0; $i < 2; $i++) {
            \App\Models\DeviceToken::create([
                'user_id' => $user->id,
                'token' => \Illuminate\Support\Str::random(64),
                'platform' => $faker->randomElement(['android', 'ios', 'web']),
                'is_active' => true,
            ]);
        }
    }
}
