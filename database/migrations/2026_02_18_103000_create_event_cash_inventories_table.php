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
        Schema::create('event_cash_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->unique()->constrained()->onDelete('cascade');
            $table->unsignedSmallInteger('note_1')->default(0);
            $table->unsignedSmallInteger('note_2')->default(0);
            $table->unsignedSmallInteger('note_5')->default(0);
            $table->unsignedSmallInteger('note_10')->default(0);
            $table->unsignedSmallInteger('note_20')->default(0);
            $table->unsignedSmallInteger('note_50')->default(0);
            $table->unsignedSmallInteger('note_100')->default(0);
            $table->unsignedSmallInteger('note_200')->default(0);
            $table->unsignedSmallInteger('note_500')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_cash_inventories');
    }
};
