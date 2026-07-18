<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matrimonial_plans', function (Blueprint $table) {
            $table->foreignId('upi_transaction_id')
                ->nullable()
                ->after('user_id')
                ->constrained('upi_transactions')
                ->nullOnDelete();
            $table->unique('upi_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('matrimonial_plans', function (Blueprint $table) {
            $table->dropUnique(['upi_transaction_id']);
            $table->dropConstrainedForeignId('upi_transaction_id');
        });
    }
};
