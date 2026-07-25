<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendors';

    protected $fillable = [
        'category_id',
        'business_name',
        'city',
        'price_tier',
        'description',
        'phone',
        'whatsapp',
        'is_featured',
        'is_verified',
        'status',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(VendorCategory::class, 'category_id');
    }

    public function portfolioImages()
    {
        return $this->hasMany(VendorPortfolioImage::class, 'vendor_id');
    }

    public function leads()
    {
        return $this->hasMany(VendorLead::class, 'vendor_id');
    }
}
