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
            $table->decimal('change_amount', 10, 2)->default(0)->after('amount');
            $table->string('change_status', 20)->nullable()->after('change_amount');
            $table->unsignedSmallInteger('change_note_1')->default(0)->after('change_status');
            $table->unsignedSmallInteger('change_note_2')->default(0)->after('change_note_1');
            $table->unsignedSmallInteger('change_note_5')->default(0)->after('change_note_2');
            $table->unsignedSmallInteger('change_note_10')->default(0)->after('change_note_5');
            $table->unsignedSmallInteger('change_note_20')->default(0)->after('change_note_10');
            $table->unsignedSmallInteger('change_note_50')->default(0)->after('change_note_20');
            $table->unsignedSmallInteger('change_note_100')->default(0)->after('change_note_50');
            $table->unsignedSmallInteger('change_note_200')->default(0)->after('change_note_100');
            $table->unsignedSmallInteger('change_note_500')->default(0)->after('change_note_200');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chandlas', function (Blueprint $table) {
            $table->dropColumn([
                'change_amount',
                'change_status',
                'change_note_1',
                'change_note_2',
                'change_note_5',
                'change_note_10',
                'change_note_20',
                'change_note_50',
                'change_note_100',
                'change_note_200',
                'change_note_500',
            ]);
        });
    }
};
