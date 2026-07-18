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
        Schema::create('chandlas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('giver_name');
            $table->string('giver_phone')->nullable();
            $table->string('giver_email')->nullable();
            $table->text('giver_address')->nullable();
            $table->enum('category', ['chandla', 'cover', 'gift'])->default('chandla');
            $table->enum('payment_method', ['hard_form', 'gpay', 'cash', 'other'])->default('hard_form');
            $table->decimal('amount', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->date('received_date');
            $table->string('receipt_number')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('event_id');
            $table->index('user_id');
            $table->index('category');
            $table->index('payment_method');
            $table->index('received_date');
            $table->index('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chandlas');
    }
};
