<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('contacts', 'phones')) {
                $table->json('phones')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('contacts', 'emails')) {
                $table->json('emails')->nullable()->after('email');
            }
            if (!Schema::hasColumn('contacts', 'organization')) {
                $table->string('organization')->nullable()->after('relationship');
            }
            if (!Schema::hasColumn('contacts', 'title')) {
                $table->string('title')->nullable()->after('organization');
            }
            if (!Schema::hasColumn('contacts', 'birthday')) {
                $table->date('birthday')->nullable()->after('title');
            }
            if (!Schema::hasColumn('contacts', 'website')) {
                $table->string('website')->nullable()->after('birthday');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['phones', 'emails', 'organization', 'title', 'birthday', 'website'] as $col) {
                if (Schema::hasColumn('contacts', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
