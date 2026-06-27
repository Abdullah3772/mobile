<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'title', 'slug', 'description', 'type',
        'discount_percentage', 'discount_amount', 'banner_image',
        'starts_at', 'ends_at', 'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'offer_products')
            ->withPivot('offer_price')
            ->withTimestamps();
    }

    public function getIsActiveNowAttribute(): bool
    {
        return $this->is_active && now()->between($this->starts_at, $this->ends_at);
    }
}
