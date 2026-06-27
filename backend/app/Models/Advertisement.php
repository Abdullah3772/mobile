<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'ad_package_id', 'title', 'banner_image', 'link',
        'position', 'status', 'starts_at', 'ends_at', 'clicks', 'impressions',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function adPackage()
    {
        return $this->belongsTo(AdPackage::class);
    }
}
