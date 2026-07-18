<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if it doesn't exist
        $admin = User::firstOrCreate(
            ['email' => 'admin@chandlabook.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('Admin@123'), // Change this password in production!
                'is_admin' => true,
                'is_active' => true,
                'auth_provider' => 'email',
                'email_verified_at' => now(),
            ]
        );

        // Update existing admin if found
        if ($admin->wasRecentlyCreated === false) {
            $admin->update([
                'password' => Hash::make('Admin@123'),
                'is_admin' => true,
                'is_active' => true,
            ]);
        }

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@chandlabook.com');
        $this->command->info('Password: Admin@123');
        $this->command->warn('⚠️  Please change the admin password after first login!');
    }
}
