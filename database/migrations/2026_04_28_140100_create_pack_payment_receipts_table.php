<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pack_payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('pack_type', 32);
            $table->string('razorpay_payment_id')->unique();
            $table->unsignedInteger('amount_paise');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pack_payment_receipts');
    }
};
