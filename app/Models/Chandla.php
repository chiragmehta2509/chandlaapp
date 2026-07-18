<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chandla extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'giver_name',
        'giver_phone',
        'giver_email',
        'giver_address',
        'category',
        'payment_method',
        'gpay_image',
        'gpay_transaction_id',
        'amount',
        'change_amount',
        'change_status',
        'change_note_1',
        'change_note_2',
        'change_note_5',
        'change_note_10',
        'change_note_20',
        'change_note_50',
        'change_note_100',
        'change_note_200',
        'change_note_500',
        'description',
        'gift_item_name',
        'gift_received',
        'received_date',
        'receipt_number',
        'notes',
        'cash_note_1',
        'cash_note_2',
        'cash_note_5',
        'cash_note_10',
        'cash_note_20',
        'cash_note_50',
        'cash_note_100',
        'cash_note_200',
        'cash_note_500',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'received_date' => 'date',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'gift_received' => 'boolean',
        'change_note_1' => 'integer',
        'change_note_2' => 'integer',
        'change_note_5' => 'integer',
        'change_note_10' => 'integer',
        'change_note_20' => 'integer',
        'change_note_50' => 'integer',
        'change_note_100' => 'integer',
        'change_note_200' => 'integer',
        'change_note_500' => 'integer',
        'cash_note_1' => 'integer',
        'cash_note_2' => 'integer',
        'cash_note_5' => 'integer',
        'cash_note_10' => 'integer',
        'cash_note_20' => 'integer',
        'cash_note_50' => 'integer',
        'cash_note_100' => 'integer',
        'cash_note_200' => 'integer',
        'cash_note_500' => 'integer',
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('received_date', [$startDate, $endDate]);
    }

    public function scopeByEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    // Helper methods
    public function getCategoryLabelAttribute()
    {
        $labels = [
            'chandla' => 'Cash',
            'cover' => 'Cover',
            'gift' => 'Gift',
            'GPAY GPAY' => 'GPay (direct QR)',
        ];

        return $labels[$this->category] ?? ucfirst(str_replace('_', ' ', $this->category));
    }

    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            'hard_form' => 'Hard Form',
            'gpay' => 'GPay',
            'cash' => 'Cash',
            'other' => 'N/A',
        ];
        return $labels[$this->payment_method] ?? $this->payment_method;
    }
}
