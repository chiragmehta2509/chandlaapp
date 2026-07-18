<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PaymentTransaction extends Model
{
    // ── Status constants ─────────────────────────────────────────────────────
    public const STATUS_PENDING    = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SUCCESS    = 'success';
    public const STATUS_FAILED     = 'failed';
    public const STATUS_CANCELLED  = 'cancelled';
    public const STATUS_REFUNDED   = 'refunded';
    public const STATUS_EXPIRED    = 'expired';

    // ── Package key constants ─────────────────────────────────────────────────
    public const PKG_CELEBRATION       = 'celebration';
    public const PKG_LEDGER_DUO        = 'ledger_duo';
    public const PKG_FAMILY            = 'family';
    public const PKG_PREMIUM_BUNDLE    = 'premium_bundle';
    public const PKG_GUEST_PAY_SINGLE  = 'guest_pay_single';
    public const PKG_MARRIAGE_INVITATION = 'marriage_invitation';
    public const PKG_MATRIMONIAL_200   = 'matrimonial_200';
    public const PKG_MATRIMONIAL_500   = 'matrimonial_500';
    public const PKG_EVENT_PLAN        = 'event_plan_unlimited';
    public const PKG_DIRECT_GPAY       = 'direct_gpay_unlock';
    public const PKG_PROFESSIONAL      = 'professional';
    public const PKG_ENTERPRISE        = 'enterprise';

    protected $table = 'payment_transactions';

    protected $fillable = [
        'transaction_number',
        'user_id',
        'package_key',
        'package_name',
        'amount_inr',
        'currency',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'payment_method',
        'status',
        'failure_reason',
        'gateway_response',
        'reference_id',
        'paid_at',
        'metadata',
    ];

    protected $casts = [
        'amount_inr'       => 'decimal:2',
        'gateway_response' => 'array',
        'metadata'         => 'array',
        'paid_at'          => 'datetime',
    ];

    // ── Auto-generate transaction number on create ────────────────────────────
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->transaction_number)) {
                $model->transaction_number = static::generateTransactionNumber();
            }
        });
    }

    public static function generateTransactionNumber(): string
    {
        $date   = now()->format('Ymd');
        $prefix = "TXN-{$date}-";

        // Find the highest sequence for today
        $last = static::where('transaction_number', 'like', $prefix . '%')
            ->orderByDesc('transaction_number')
            ->value('transaction_number');

        $seq = 1;
        if ($last) {
            $seq = (int) Str::afterLast($last, '-') + 1;
        }

        return $prefix . str_pad((string) $seq, 6, '0', STR_PAD_LEFT);
    }

    // ── Relationships ─────────────────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function isPending(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING], true);
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function canRetry(): bool
    {
        return in_array($this->status, [
            self::STATUS_FAILED,
            self::STATUS_CANCELLED,
            self::STATUS_EXPIRED,
        ], true);
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_SUCCESS    => 'badge-success',
            self::STATUS_PENDING,
            self::STATUS_PROCESSING => 'badge-warning',
            self::STATUS_FAILED     => 'badge-danger',
            self::STATUS_REFUNDED   => 'badge-info',
            default                 => 'badge-secondary',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING    => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SUCCESS    => 'Success',
            self::STATUS_FAILED     => 'Failed',
            self::STATUS_CANCELLED  => 'Cancelled',
            self::STATUS_REFUNDED   => 'Refunded',
            self::STATUS_EXPIRED    => 'Expired',
            default                 => ucfirst($this->status),
        };
    }

    /** All valid package keys */
    public static function validPackageKeys(): array
    {
        return [
            self::PKG_CELEBRATION,
            self::PKG_LEDGER_DUO,
            self::PKG_FAMILY,
            self::PKG_PREMIUM_BUNDLE,
            self::PKG_GUEST_PAY_SINGLE,
            self::PKG_MARRIAGE_INVITATION,
            self::PKG_MATRIMONIAL_200,
            self::PKG_MATRIMONIAL_500,
            self::PKG_EVENT_PLAN,
            self::PKG_DIRECT_GPAY,
            self::PKG_PROFESSIONAL,
            self::PKG_ENTERPRISE,
        ];
    }

    /** Human-readable package name from key */
    public static function packageName(string $key): string
    {
        return match ($key) {
            self::PKG_CELEBRATION         => 'Celebration Pack',
            self::PKG_LEDGER_DUO          => 'Host Plus Plan',
            self::PKG_FAMILY              => 'Family Plan',
            self::PKG_PREMIUM_BUNDLE      => 'Premium Host',
            self::PKG_GUEST_PAY_SINGLE    => 'Guest Contribution — Single Event',
            self::PKG_MARRIAGE_INVITATION => 'Marriage Invitation Card',
            self::PKG_MATRIMONIAL_200     => 'Find Partner Plan (₹200)',
            self::PKG_MATRIMONIAL_500     => 'Find Partner Plan (₹500)',
            self::PKG_EVENT_PLAN          => 'Event Plan — Unlimited',
            self::PKG_DIRECT_GPAY         => 'Direct GPay QR Unlock',
            self::PKG_PROFESSIONAL        => 'Professional Plan',
            self::PKG_ENTERPRISE          => 'Enterprise Plan',
            default                       => ucfirst(str_replace('_', ' ', $key)),
        };
    }
}
