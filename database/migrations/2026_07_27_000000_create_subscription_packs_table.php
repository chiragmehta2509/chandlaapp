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
        Schema::create('subscription_packs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // e.g. celebration, ledger_duo, family, etc.
            $table->string('name');           // e.g. Celebration Pack
            $table->decimal('amount_inr', 10, 2)->default(0);
            $table->integer('min_level')->default(0);
            $table->text('description')->nullable();
            $table->string('badge')->nullable(); // e.g. Best Value, Recommended
            $table->boolean('is_popular')->default(false);
            $table->json('features')->nullable(); // Bullet point array of features
            $table->json('limits')->nullable();   // {events: 1, entries: 50, qrCollection: false, editors: 0}
            $table->string('live_payment_url')->nullable();
            $table->string('test_payment_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_packs');
    }
};
