<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('entry_id')->nullable()->constrained()->onDelete('set null');
            $table->string('invitation_code')->unique();
            $table->enum('type', ['digital', 'pdf', 'image'])->default('digital');
            $table->string('template_id')->nullable();
            $table->text('custom_message')->nullable();
            $table->enum('status', ['draft', 'sent', 'opened', 'accepted', 'declined'])->default('draft');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->integer('open_count')->default(0);
            $table->timestamps();

            $table->index('event_id');
            $table->index('entry_id');
            $table->index('invitation_code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};

