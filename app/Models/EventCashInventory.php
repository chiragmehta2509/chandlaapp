<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventCashInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'note_1',
        'note_2',
        'note_5',
        'note_10',
        'note_20',
        'note_50',
        'note_100',
        'note_200',
        'note_500',
    ];

    protected $casts = [
        'note_1' => 'integer',
        'note_2' => 'integer',
        'note_5' => 'integer',
        'note_10' => 'integer',
        'note_20' => 'integer',
        'note_50' => 'integer',
        'note_100' => 'integer',
        'note_200' => 'integer',
        'note_500' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
