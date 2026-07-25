<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GanpatiEventTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('event_types')->upsert(
            [
                [
                    'name'        => 'Ganpati Special',
                    'slug'        => 'ganpati_special',
                    'description' => 'Ganpati Utsav chanda/chandla collection — free & unlimited for all users.',
                    'icon'        => 'fa-om',
                    'color'       => '#f97316',
                    'is_active'   => true,
                    'sort_order'  => 0,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
            ],
            ['slug'],
            ['name', 'description', 'icon', 'color', 'is_active', 'updated_at']
        );
    }
}
