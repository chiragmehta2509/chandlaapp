<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorLead extends Model
{
    use HasFactory;

    protected $table = 'vendor_leads';

    protected $fillable = [
        'vendor_id',
        'event_id',
        'host_name',
        'host_phone',
        'message',
        'source',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
