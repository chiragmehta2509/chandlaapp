<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->time('event_time')->nullable();
            $table->string('venue')->nullable();
            $table->string('cover_image')->nullable();
            $table->enum('event_type', ['wedding', 'birthday', 'anniversary', 'other'])->default('other');
            $table->boolean('is_archived')->default(false);
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('event_date');
            $table->index('event_type');
            $table->index('is_archived');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

