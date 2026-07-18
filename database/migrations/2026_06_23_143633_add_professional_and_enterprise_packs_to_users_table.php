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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('professional_pack_paid_at')->nullable()->after('family_pack_paid_at');
            $table->timestamp('enterprise_pack_paid_at')->nullable()->after('professional_pack_paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['professional_pack_paid_at', 'enterprise_pack_paid_at']);
        });
    }
};
