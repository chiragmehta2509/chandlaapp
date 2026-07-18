<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'entry_id',
        'invitation_code',
        'type',
        'template_id',
        'custom_message',
        'status',
        'sent_at',
        'opened_at',
        'responded_at',
        'open_count',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }

    public function shares()
    {
        return $this->hasMany(InvitationShare::class);
    }

    // Scopes
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeOpened($query)
    {
        return $query->where('status', 'opened');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }
}

