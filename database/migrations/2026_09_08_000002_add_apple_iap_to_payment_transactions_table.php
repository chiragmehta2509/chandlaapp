<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            // Payment gateway identifier — default razorpay for backward compatibility
            if (!Schema::hasColumn('payment_transactions', 'payment_gateway')) {
                $table->string('payment_gateway')->default('razorpay')->index()
                      ->after('razorpay_signature')
                      ->comment("'razorpay' | 'apple_iap' | 'google_play'");
            }

            // Apple-specific transaction identifiers
            if (!Schema::hasColumn('payment_transactions', 'apple_transaction_id')) {
                $table->string('apple_transaction_id')->nullable()->unique()->index()
                      ->after('payment_gateway')
                      ->comment('JWS transactionId from Apple');
            }

            if (!Schema::hasColumn('payment_transactions', 'apple_original_transaction_id')) {
                $table->string('apple_original_transaction_id')->nullable()->index()
                      ->after('apple_transaction_id')
                      ->comment('originalTransactionId — same across renewals, restores, device transfers');
            }

            if (!Schema::hasColumn('payment_transactions', 'apple_environment')) {
                $table->string('apple_environment')->nullable()
                      ->after('apple_original_transaction_id')
                      ->comment("'Sandbox' or 'Production'");
            }

            // Refund / revocation tracking
            if (!Schema::hasColumn('payment_transactions', 'revoked_at')) {
                $table->timestamp('revoked_at')->nullable()
                      ->after('apple_environment')
                      ->comment('Timestamp when Apple revoked this transaction (family sharing revoke / refund)');
            }

            if (!Schema::hasColumn('payment_transactions', 'is_refunded')) {
                $table->boolean('is_refunded')->default(false)
                      ->after('revoked_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $columns = [
                'payment_gateway',
                'apple_transaction_id',
                'apple_original_transaction_id',
                'apple_environment',
                'revoked_at',
                'is_refunded',
            ];

            foreach ($columns as $col) {
                if (Schema::hasColumn('payment_transactions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
