<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreWeddingSetting extends Model
{
    protected $fillable = [
        'user_id',
        'wedding_date',
        'custom_text',
    ];

    protected $casts = [
        'wedding_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
