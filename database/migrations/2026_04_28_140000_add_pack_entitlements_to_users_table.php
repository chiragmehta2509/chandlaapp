<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('celebration_pack_paid_at')->nullable()->after('referral_rewarded_at');
            $table->timestamp('premium_bundle_paid_at')->nullable()->after('celebration_pack_paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['celebration_pack_paid_at', 'premium_bundle_paid_at']);
        });
    }
};
