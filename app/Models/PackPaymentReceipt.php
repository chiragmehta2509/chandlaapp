<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackPaymentReceipt extends Model
{
    protected $fillable = [
        'user_id',
        'pack_type',
        'razorpay_payment_id',
        'amount_paise',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
