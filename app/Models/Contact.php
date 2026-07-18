<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
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
        'relationship',
        'organization',
        'title',
        'birthday',
        'website',
        'notes',
        'avatar',
        'is_favorite',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
        'phones'      => 'array',
        'emails'      => 'array',
        'birthday'    => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
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
            ->orWhere('email', 'like', "%{$search}%");
    }
}

