<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_user_id')->nullable()->after('referred_by');
            $table->boolean('must_change_password')->default(false)->after('parent_user_id');
            $table->index('parent_user_id');
            $table->foreign('parent_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['parent_user_id']);
            $table->dropIndex(['parent_user_id']);
            $table->dropColumn(['parent_user_id', 'must_change_password']);
        });
    }
};
