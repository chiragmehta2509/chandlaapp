<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matrimonial_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('plan_type', 20);
            $table->decimal('price', 10, 2);
            $table->date('start_date');
            $table->date('expiry_date');
            $table->string('payment_id', 64)->nullable();
            $table->string('razorpay_order_id', 64)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'expiry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matrimonial_plans');
    }
};
