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
        if (Schema::hasTable('device_tokens')) {
            Schema::table('device_tokens', function (Blueprint $table) {
                // If legacy column 'token' exists and 'device_token' does not, rename it
                if (Schema::hasColumn('device_tokens', 'token') && !Schema::hasColumn('device_tokens', 'device_token')) {
                    $table->renameColumn('token', 'device_token');
                } elseif (!Schema::hasColumn('device_tokens', 'device_token')) {
                    $table->string('device_token')->unique()->after('user_id');
                }

                if (!Schema::hasColumn('device_tokens', 'app_version')) {
                    $table->string('app_version')->nullable()->after('device_name');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('device_tokens') && Schema::hasColumn('device_tokens', 'device_token')) {
            Schema::table('device_tokens', function (Blueprint $table) {
                if (!Schema::hasColumn('device_tokens', 'token')) {
                    $table->renameColumn('device_token', 'token');
                }
            });
        }
    }
};
