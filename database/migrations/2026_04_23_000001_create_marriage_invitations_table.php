<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marriage_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('template_key', 32);
            $table->json('data');
            $table->decimal('amount', 8, 2)->default(50);
            $table->foreignId('upi_transaction_id')->nullable()->constrained('upi_transactions')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'template_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marriage_invitations');
    }
};
