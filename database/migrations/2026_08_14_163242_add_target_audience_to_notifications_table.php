<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('target_audience')->nullable()->after('send_to'); // e.g. 'all', 'plan_wise', 'specific_users'
            $table->json('target_data')->nullable()->after('target_audience'); // e.g. plan_level: 1 or user_ids: [1,2,3]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['target_audience', 'target_data']);
        });
    }
};
