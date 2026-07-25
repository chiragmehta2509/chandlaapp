<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VendorCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Caterers',
            'Decorators',
            'Venue/Mandap',
            'Photographers/Videographers',
            'DJ/Music',
            'Makeup Artists',
            'Invitation Printers',
            'Transport/Car Rentals'
        ];

        foreach ($categories as $category) {
            $slug = Str::slug($category);
            
            // Check if exists
            $exists = DB::table('vendor_categories')->where('slug', $slug)->exists();
            if (!$exists) {
                DB::table('vendor_categories')->insert([
                    'name' => $category,
                    'slug' => $slug,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
