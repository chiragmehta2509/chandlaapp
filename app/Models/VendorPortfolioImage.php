<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPortfolioImage extends Model
{
    use HasFactory;

    protected $table = 'vendor_portfolio_images';

    protected $fillable = [
        'vendor_id',
        'image_url',
        'sort_order',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
