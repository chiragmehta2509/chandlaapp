<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'phones',
        'email',
        'emails',
        'address',
        'city',
        'relationship',
        'notes',
        'avatar',
        'is_favorite',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
        'phones'      => 'array',
        'emails'      => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeFavorite($query)
    {
        return $query->where('is_favorite', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('city', 'like', "%{$search}%")
            ->orWhere('relationship', 'like', "%{$search}%");
    }
}
