<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPack extends Model
{
    use HasFactory;

    protected $table = 'subscription_packs';

    protected $fillable = [
        'slug',
        'name',
        'amount_inr',
        'min_level',
        'description',
        'badge',
        'is_popular',
        'features',
        'limits',
        'live_payment_url',
        'test_payment_url',
    ];

    protected $casts = [
        'amount_inr' => 'float',
        'min_level' => 'integer',
        'is_popular' => 'boolean',
        'features' => 'array',
        'limits' => 'array',
    ];
}
