<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventType;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventTypes = [
            [
                'name' => 'Wedding',
                'slug' => 'wedding',
                'description' => 'Wedding ceremonies and receptions',
                'icon' => 'fas fa-heart',
                'color' => '#e91e63',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Birthday',
                'slug' => 'birthday',
                'description' => 'Birthday celebrations',
                'icon' => 'fas fa-birthday-cake',
                'color' => '#ff9800',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Anniversary',
                'slug' => 'anniversary',
                'description' => 'Anniversary celebrations',
                'icon' => 'fas fa-gift',
                'color' => '#9c27b0',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Corporate Event',
                'slug' => 'corporate-event',
                'description' => 'Corporate meetings and events',
                'icon' => 'fas fa-briefcase',
                'color' => '#2196f3',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Festival',
                'slug' => 'festival',
                'description' => 'Cultural and religious festivals',
                'icon' => 'fas fa-music',
                'color' => '#4caf50',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Other',
                'slug' => 'other',
                'description' => 'Other types of events',
                'icon' => 'fas fa-calendar',
                'color' => '#607d8b',
                'is_active' => true,
                'sort_order' => 99,
            ],
        ];

        foreach ($eventTypes as $eventType) {
            EventType::firstOrCreate(
                ['slug' => $eventType['slug']],
                $eventType
            );
        }
    }
}
