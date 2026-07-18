<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 32)->unique()->comment('e.g. TXN-20260623-000001');
            $table->unsignedBigInteger('user_id')->index();
            $table->string('package_key', 50)->index()->comment('celebration, ledger_duo, family, premium_bundle, guest_pay_single, marriage_invitation, matrimonial_200, matrimonial_500, event_plan_unlimited, direct_gpay_unlock');
            $table->string('package_name', 100)->comment('Human readable, e.g. Celebration Pack ₹300');
            $table->decimal('amount_inr', 10, 2);
            $table->char('currency', 3)->default('INR');
            $table->string('razorpay_order_id', 64)->unique()->nullable()->index();
            $table->string('razorpay_payment_id', 64)->nullable()->index();
            $table->string('razorpay_signature', 255)->nullable();
            $table->string('payment_method', 30)->nullable()->comment('upi, card, netbanking, wallet');
            $table->enum('status', [
                'pending', 'processing', 'success', 'failed', 'cancelled', 'refunded', 'expired'
            ])->default('pending')->index();
            $table->text('failure_reason')->nullable();
            $table->json('gateway_response')->nullable();
            $table->string('reference_id', 64)->nullable()->comment('e.g. event_id, invitation_id, matrimonial_plan_id');
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
