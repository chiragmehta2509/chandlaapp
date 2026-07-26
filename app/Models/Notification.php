<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'title',
        'message',
        'image',
        'action_type',
        'action_value',
        'created_by',
        'send_to',
        'schedule_at',
        'status',
    ];

    protected $casts = [
        'schedule_at' => 'datetime',
    ];

    /**
     * Get the admin user who created/sent the notification.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the individual user delivery statuses.
     */
    public function notificationUsers()
    {
        return $this->hasMany(NotificationUser::class);
    }

    /**
     * Get users targeted by this notification.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'notification_users')
            ->withPivot('is_read', 'read_at', 'created_at');
    }
}
