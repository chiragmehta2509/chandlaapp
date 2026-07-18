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
        Schema::table('chandlas', function (Blueprint $table) {
            $table->string('gift_item_name')->nullable()->after('description');
            $table->unsignedSmallInteger('cash_note_1')->default(0)->after('notes');
            $table->unsignedSmallInteger('cash_note_2')->default(0)->after('cash_note_1');
            $table->unsignedSmallInteger('cash_note_5')->default(0)->after('cash_note_2');
            $table->unsignedSmallInteger('cash_note_10')->default(0)->after('cash_note_5');
            $table->unsignedSmallInteger('cash_note_20')->default(0)->after('cash_note_10');
            $table->unsignedSmallInteger('cash_note_50')->default(0)->after('cash_note_20');
            $table->unsignedSmallInteger('cash_note_100')->default(0)->after('cash_note_50');
            $table->unsignedSmallInteger('cash_note_200')->default(0)->after('cash_note_100');
            $table->unsignedSmallInteger('cash_note_500')->default(0)->after('cash_note_200');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chandlas', function (Blueprint $table) {
            $table->dropColumn([
                'gift_item_name',
                'cash_note_1',
                'cash_note_2',
                'cash_note_5',
                'cash_note_10',
                'cash_note_20',
                'cash_note_50',
                'cash_note_100',
                'cash_note_200',
                'cash_note_500',
            ]);
        });
    }
};
