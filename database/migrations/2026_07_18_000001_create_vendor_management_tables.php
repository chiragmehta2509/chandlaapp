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
        Schema::create('vendor_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('vendor_categories')->onDelete('cascade');
            $table->string('business_name');
            $table->string('city');
            $table->enum('price_tier', ['budget', 'mid', 'premium'])->default('mid');
            $table->text('description')->nullable();
            $table->string('phone');
            $table->string('whatsapp')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->enum('status', ['pending', 'active', 'inactive'])->default('pending');
            $table->timestamps();

            $table->index('city');
            $table->index('price_tier');
            $table->index('status');
            $table->index('is_featured');
            $table->index('is_verified');
        });

        Schema::create('vendor_portfolio_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->string('image_url');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('vendor_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('set null');
            $table->string('host_name');
            $table->string('host_phone');
            $table->text('message')->nullable();
            $table->string('source')->default('directory');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_leads');
        Schema::dropIfExists('vendor_portfolio_images');
        Schema::dropIfExists('vendors');
        Schema::dropIfExists('vendor_categories');
    }
};
