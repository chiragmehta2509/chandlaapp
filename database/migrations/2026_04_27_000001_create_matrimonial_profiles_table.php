<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matrimonial_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('display_name', 120);
            $table->unsignedSmallInteger('age');
            $table->string('gender', 20);
            $table->string('city', 120);
            $table->string('religion', 120)->nullable();
            $table->string('caste', 120)->nullable();
            $table->string('sub_caste', 120)->nullable();
            $table->string('education', 255)->nullable();
            $table->string('profession', 255)->nullable();
            $table->string('income', 120)->nullable();
            $table->text('family_details')->nullable();
            $table->text('about_me')->nullable();
            $table->text('partner_preferences')->nullable();
            $table->string('photo_path', 500)->nullable();
            $table->boolean('phone_visible_to_matches')->default(true);
            $table->boolean('is_complete')->default(false);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matrimonial_profiles');
    }
};
