<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MatrimonialProfile extends Model
{
    protected $fillable = [
        'user_id',
        'display_name',
        'age',
        'gender',
        'city',
        'religion',
        'caste',
        'sub_caste',
        'education',
        'profession',
        'income',
        'family_details',
        'about_me',
        'partner_preferences',
        'photo_path',
        'phone_visible_to_matches',
        'interests_receiving_enabled',
        'is_complete',
    ];

    protected $casts = [
        'is_complete' => 'boolean',
        'phone_visible_to_matches' => 'boolean',
        'interests_receiving_enabled' => 'boolean',
        'age' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function photoUrl(): ?string
    {
        if (!$this->photo_path) {
            return null;
        }

        return Storage::disk('public')->url($this->photo_path);
    }
}
