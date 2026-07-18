<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatrimonialPlan extends Model
{
    protected $fillable = [
        'user_id',
        'upi_transaction_id',
        'plan_type',
        'price',
        'start_date',
        'expiry_date',
        'payment_id',
        'razorpay_order_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
        'price' => 'float',
    ];

    public const TYPE_FREE = 'free';

    public const TYPE_6M = '6m';

    public const TYPE_12M = '12m';

    public const TYPE_500 = '500';

    public const TYPE_200 = '200';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function upiTransaction(): BelongsTo
    {
        return $this->belongsTo(UPITransaction::class, 'upi_transaction_id');
    }
}
