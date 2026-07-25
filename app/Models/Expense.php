<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'title',
        'description',
        'category',
        'amount',
        'expense_date',
        'payee_name',
        'payee_phone',
        'payee_upi',
        'payment_method',
        'transaction_id',
        'receipt_number',
        'receipt_image',
        'notes',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'expense_date' => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeByEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

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
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Standard expense categories.
     */
    public static function categories(): array
    {
        return [
            'decoration',
            'food',
            'music',
            'photography',
            'venue',
            'transport',
            'clothing',
            'invitation',
            'pooja',
            'catering',
            'lighting',
            'other',
        ];
    }
}
