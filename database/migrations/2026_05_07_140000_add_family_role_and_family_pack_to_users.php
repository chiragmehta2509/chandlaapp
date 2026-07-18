<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 'viewer' (read-only) or 'editor' (full access except billing/family management/delete).
            // NULL for main users.
            $table->string('family_role', 16)->nullable()->after('must_change_password');
            // ₹600 Family pack — unlocks adding 3 family editors. Same effect as premium_bundle (₹700) for family.
            $table->timestamp('family_pack_paid_at')->nullable()->after('ledger_duo_pack_paid_at');
        });

        // Backfill: existing family members (parent_user_id IS NOT NULL) are viewers.
        DB::table('users')
            ->whereNotNull('parent_user_id')
            ->update(['family_role' => 'viewer']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['family_role', 'family_pack_paid_at']);
        });
    }
};
