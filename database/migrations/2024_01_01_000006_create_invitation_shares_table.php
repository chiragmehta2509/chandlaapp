<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->onDelete('cascade');
            $table->enum('platform', ['whatsapp', 'sms', 'email', 'link'])->default('link');
            $table->string('recipient')->nullable();
            $table->timestamp('shared_at')->nullable();
            $table->timestamps();

            $table->index('invitation_id');
            $table->index('platform');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_shares');
    }
};

