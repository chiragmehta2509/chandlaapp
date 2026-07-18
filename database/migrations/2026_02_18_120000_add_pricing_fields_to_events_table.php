<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('pricing_plan', 20)->default('free')->after('event_type');
            $table->unsignedSmallInteger('free_entry_limit')->default(200)->after('pricing_plan');
            $table->decimal('per_entry_price', 8, 2)->default(1)->after('free_entry_limit');
            $table->decimal('unlimited_price', 8, 2)->default(500)->after('per_entry_price');
            $table->timestamp('unlimited_purchased_at')->nullable()->after('unlimited_price');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'pricing_plan',
                'free_entry_limit',
                'per_entry_price',
                'unlimited_price',
                'unlimited_purchased_at',
            ]);
        });
    }
};
