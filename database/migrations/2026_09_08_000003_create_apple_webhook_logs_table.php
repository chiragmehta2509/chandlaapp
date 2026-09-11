<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('apple_webhook_logs')) {
            Schema::create('apple_webhook_logs', function (Blueprint $table) {
                $table->id();

                // Every Apple V2 notification carries a unique UUID — used for idempotency
                $table->string('notification_uuid')->unique()->index();

                // Notification type (e.g., SUBSCRIBED, DID_RENEW, EXPIRED, REFUND, REVOKE)
                $table->string('notification_type')->index();

                // Subtype (e.g., INITIAL_BUY, RESUBSCRIBE, VOLUNTARY, etc.)
                $table->string('subtype')->nullable();

                // Full decoded payload stored as JSON for debugging / reprocessing
                $table->json('payload');

                // Processing status
                // 'pending'   → received, not yet processed
                // 'processed' → successfully handled
                // 'ignored'   → duplicate or no-op event
                // 'failed'    → processing threw an exception
                $table->string('status')->default('pending')->index();

                $table->text('error_message')->nullable();
                $table->timestamp('processed_at')->nullable();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('apple_webhook_logs');
    }
};
