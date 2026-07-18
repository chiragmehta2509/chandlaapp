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
            $table->string('gpay_image')->nullable()->after('payment_method');
            $table->string('gpay_transaction_id')->nullable()->after('gpay_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chandlas', function (Blueprint $table) {
            $table->dropColumn(['gpay_image', 'gpay_transaction_id']);
        });
    }
};
