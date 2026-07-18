<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matrimonial_profiles', function (Blueprint $table) {
            $table->boolean('interests_receiving_enabled')->default(true)->after('phone_visible_to_matches');
        });
    }

    public function down(): void
    {
        Schema::table('matrimonial_profiles', function (Blueprint $table) {
            $table->dropColumn('interests_receiving_enabled');
        });
    }
};
