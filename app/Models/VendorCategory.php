<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorCategory extends Model
{
    use HasFactory;

    protected $table = 'vendor_categories';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function vendors()
    {
        return $this->hasMany(Vendor::class, 'category_id');
    }
}
