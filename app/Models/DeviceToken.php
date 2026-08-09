<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class DeviceToken extends Model
{
    use HasFactory;

    protected $table = 'device_tokens';

    protected $fillable = [
        'user_id',
        'device_token',
        'token',
        'platform',
        'device_name',
        'app_version',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get active token column name dynamically (supports legacy 'token' and new 'device_token').
     */
    public static function getTokenColumn(): string
    {
        static $col = null;
        if ($col === null) {
            try {
                $col = Schema::hasColumn('device_tokens', 'device_token') ? 'device_token' : 'token';
            } catch (\Throwable $e) {
                $col = 'token';
            }
        }
        return $col;
    }

    /**
     * Get the user that owns the device token.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include active tokens.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
