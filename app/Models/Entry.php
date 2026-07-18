<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'contact_id',
        'guest_name',
        'guest_phone',
        'guest_email',
        'adults_count',
        'children_count',
        'status',
        'notes',
        'confirmed_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    // Scopes
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDeclined($query)
    {
        return $query->where('status', 'declined');
    }
}

