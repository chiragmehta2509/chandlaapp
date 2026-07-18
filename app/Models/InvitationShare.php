<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitationShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id',
        'platform',
        'recipient',
        'shared_at',
    ];

    protected $casts = [
        'shared_at' => 'datetime',
    ];

    // Relationships
    public function invitation()
    {
        return $this->belongsTo(Invitation::class);
    }
}

