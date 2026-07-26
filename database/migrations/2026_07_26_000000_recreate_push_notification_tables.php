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
        // Drop existing tables to ensure a clean new schema
        Schema::dropIfExists('notification_users');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('device_tokens');

        // 1. device_tokens table
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('device_token')->unique();
            $table->enum('platform', ['android', 'ios', 'web']);
            $table->string('device_name')->nullable();
            $table->string('app_version')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('user_id');
            $table->index('device_token');
            $table->index('platform');
        });

        // 2. notifications table
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->string('image')->nullable();
            $table->enum('action_type', ['none', 'feature', 'offer', 'url', 'screen'])->default('none');
            $table->string('action_value')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('send_to', ['all', 'selected_users']);
            $table->timestamp('schedule_at')->nullable();
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->timestamps();

            $table->index('created_by');
            $table->index('status');
        });

        // 3. notification_users table
        Schema::create('notification_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->nullable(); // will be manually populated or default to now

            $table->index('notification_id');
            $table->index('user_id');
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_users');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('device_tokens');
    }
};
