<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::saving(function ($event) {
            if ($event->event_type_id !== null) {
                switch ($event->event_type_id) {
                    case 1:
                        $event->event_type = 'wedding';
                        break;
                    case 2:
                        $event->event_type = 'birthday';
                        break;
                    case 3:
                        $event->event_type = 'anniversary';
                        break;
                    default:
                        $event->event_type = 'other';
                        break;
                }
            } elseif ($event->event_type !== null) {
                switch ($event->event_type) {
                    case 'wedding':
                        $event->event_type_id = 1;
                        break;
                    case 'birthday':
                        $event->event_type_id = 2;
                        break;
                    case 'anniversary':
                        $event->event_type_id = 3;
                        break;
                    case 'other':
                    default:
                        $event->event_type_id = 6;
                        break;
                }
            }
        });
    }

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'event_date',
        'event_time',
        'venue',
        'upi_id',
        'gpay_qr_image',
        'cover_image',
        'event_type_id',
        'event_type', // Keep for backward compatibility
        'pricing_plan',
        'free_entry_limit',
        'per_entry_price',
        'unlimited_price',
        'unlimited_purchased_at',
        'is_archived',
        'archived_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime',
        'free_entry_limit' => 'integer',
        'per_entry_price' => 'decimal:2',
        'unlimited_price' => 'decimal:2',
        'unlimited_purchased_at' => 'datetime',
        'archived_at' => 'datetime',
        'is_archived' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function upiTransactions()
    {
        return $this->hasMany(UPITransaction::class);
    }

    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'event_collaborators')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function chandlas()
    {
        return $this->hasMany(Chandla::class);
    }

    public function cashInventory()
    {
        return $this->hasOne(EventCashInventory::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Organiser may use guest Direct GPay flow for this event (paid unlock per event, or legacy account-wide).
     */
    public function hasDirectGpayQrUnlocked(): bool
    {
        if ($this->user && $this->user->planLevel() >= 2) {
            return true;
        }

        $uid = $this->user_id;
        if (UPITransaction::where('user_id', $uid)
            ->where('status', 'completed')
            ->where('metadata->type', 'direct_gpay_unlock')
            ->whereNull('event_id')
            ->exists()) {
            return true;
        }

        return UPITransaction::where('user_id', $uid)
            ->where('event_id', $this->id)
            ->where('status', 'completed')
            ->where('metadata->type', 'direct_gpay_unlock')
            ->exists();
    }

    /**
     * Single-event ₹400 pack: unlimited chandla rows for this event only (Direct GPay unlock included).
     */
    public function hasGuestPayPackChandlaUnlimited(): bool
    {
        if ($this->user && $this->user->planLevel() >= 2) {
            return true;
        }

        return UPITransaction::where('user_id', $this->user_id)
            ->where('event_id', $this->id)
            ->where('status', 'completed')
            ->where('metadata->type', 'direct_gpay_unlock')
            ->where('metadata->source', 'guest_pay_single_pack')
            ->exists();
    }

    public function hasDirectGpayUnlockPending(): bool
    {
        $uid = $this->user_id;
        if (UPITransaction::where('user_id', $uid)
            ->where('status', 'pending')
            ->where('metadata->type', 'direct_gpay_unlock')
            ->whereNull('event_id')
            ->exists()) {
            return true;
        }

        return UPITransaction::where('user_id', $uid)
            ->where('event_id', $this->id)
            ->where('status', 'pending')
            ->where('metadata->type', 'direct_gpay_unlock')
            ->exists();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now()->toDateString());
    }

    public function scopePast($query)
    {
        return $query->where('event_date', '<', now()->toDateString());
    }
}

