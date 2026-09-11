<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_packs', function (Blueprint $table) {
            if (!Schema::hasColumn('subscription_packs', 'apple_product_id')) {
                // e.g., "com.skylighttech.chandlaBook.ios.pack_celebration"
                $table->string('apple_product_id')->nullable()->unique()->after('slug')
                      ->comment('StoreKit 2 product identifier set in App Store Connect');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscription_packs', function (Blueprint $table) {
            if (Schema::hasColumn('subscription_packs', 'apple_product_id')) {
                $table->dropUnique(['apple_product_id']);
                $table->dropColumn('apple_product_id');
            }
        });
    }
};
