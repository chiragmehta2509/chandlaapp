<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('avatar')->nullable();
            $table->enum('auth_provider', ['google', 'facebook', 'apple', 'phone', 'email'])->default('email');
            $table->string('provider_id')->nullable();
            $table->string('fcm_token')->nullable();
            $table->enum('subscription_status', ['free', 'premium', 'expired'])->default('free');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->string('language')->default('en');
            $table->rememberToken();
            $table->timestamps();

            $table->index(['email', 'phone']);
            $table->index('auth_provider');
            $table->index('subscription_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

