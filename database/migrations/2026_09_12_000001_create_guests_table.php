<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->json('phones')->nullable();
            $table->string('email')->nullable();
            $table->json('emails')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('relationship')->nullable();
            $table->text('notes')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->timestamps();

            $table->index('user_id');
            $table->index(['name', 'phone', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
