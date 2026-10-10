<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarriageInvitation extends Model
{
    protected $fillable = [
        'user_id',
        'template_key',
        'data',
        'amount',
        'upi_transaction_id',
        'paid_at',
    ];

    protected $casts = [
        'data' => 'array',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function upiTransaction(): BelongsTo
    {
        return $this->belongsTo(UPITransaction::class, 'upi_transaction_id');
    }

    public function isUnlocked(): bool
    {
        return true;
    }

    /**
     * Broad unlock for UX (includes APP_ENV dev bypass via config dev_unlock_all).
     */
    public function isUnlockedForUser(?Authenticatable $user = null): bool
    {
        return true;
    }

    /**
     * HTML / PNG / video exports — verified payment on this row, Celebration Plan, or bypass email only.
     * Does NOT grant exports when only {@see config('marriage_invitations.dev_unlock_all')} is true.
     */
    public function exportsUnlockedForUser(?Authenticatable $user = null): bool
    {
        return true;
    }

    public function hasPendingPayment(): bool
    {
        return $this->upi_transaction_id !== null
            && $this->paid_at === null
            && $this->upiTransaction
            && $this->upiTransaction->status === 'pending';
    }
}
