<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationUser extends Model
{
    use HasFactory;

    protected $table = 'notification_users';

    // We only have created_at in the migration requirements, not updated_at.
    public $timestamps = false;

    protected $fillable = [
        'notification_id',
        'user_id',
        'is_read',
        'read_at',
        'created_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Boot function to automatically set created_at on creation.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->created_at)) {
                $model->created_at = $model->freshTimestamp();
            }
        });
    }

    /**
     * Get the parent notification record.
     */
    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    /**
     * Get the user targeted by this notification record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }
}
