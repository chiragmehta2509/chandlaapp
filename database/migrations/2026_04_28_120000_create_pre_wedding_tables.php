<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_wedding_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('wedding_date')->nullable();
            $table->timestamps();
            $table->unique('user_id');
        });

        Schema::create('pre_wedding_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('milestone_key', 40);
            $table->string('image_path')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'milestone_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_wedding_assets');
        Schema::dropIfExists('pre_wedding_settings');
    }
};
