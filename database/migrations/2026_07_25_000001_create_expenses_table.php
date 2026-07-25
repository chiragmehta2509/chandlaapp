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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Expense details
            $table->string('title');                                // e.g. "Stage Decoration"
            $table->text('description')->nullable();
            $table->string('category');                             // e.g. decoration, food, music, etc.
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('expense_date');

            // Payee / Vendor info
            $table->string('payee_name')->nullable();               // Who was paid
            $table->string('payee_phone')->nullable();
            $table->string('payee_upi')->nullable();                // UPI ID of payee

            // Payment details
            $table->enum('payment_method', ['cash', 'gpay', 'bank_transfer', 'cheque', 'other'])->default('cash');
            $table->string('transaction_id')->nullable();           // GPay / bank ref
            $table->string('receipt_number')->nullable();

            // Proof / attachment
            $table->string('receipt_image')->nullable();            // uploaded receipt photo

            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('event_id');
            $table->index('user_id');
            $table->index('category');
            $table->index('expense_date');
            $table->index('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
