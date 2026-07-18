<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Stores all phone numbers as JSON: [{"label":"mobile","number":"9876543210"}, ...]
            $table->json('phones')->nullable()->after('phone');
            // Stores all email addresses as JSON: [{"label":"home","address":"x@y.com"}, ...]
            $table->json('emails')->nullable()->after('email');
            // Extra VCF fields
            $table->string('organization')->nullable()->after('relationship');
            $table->string('title')->nullable()->after('organization');
            $table->date('birthday')->nullable()->after('title');
            $table->string('website')->nullable()->after('birthday');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['phones', 'emails', 'organization', 'title', 'birthday', 'website']);
        });
    }
};
