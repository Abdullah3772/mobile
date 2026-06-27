<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id', 'category_id', 'brand_id', 'title', 'slug', 'description',
        'model', 'condition', 'storage', 'ram', 'color', 'price', 'discount_price',
        'warranty', 'warranty_type', 'network_type', 'imei', 'trcsl_approved',
        'box_available', 'accessories_included', 'stock_quantity', 'battery_health',
        'scratches', 'face_id_working', 'original_display', 'repair_history',
        'cash_price', 'card_price', 'emi_available', 'camera', 'battery',
        'processor', 'screen_size', 'five_g_support', 'status', 'is_featured',
        'views_count', 'favorites_count', 'reservations_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'cash_price' => 'decimal:2',
        'card_price' => 'decimal:2',
        'trcsl_approved' => 'boolean',
        'box_available' => 'boolean',
        'face_id_working' => 'boolean',
        'original_display' => 'boolean',
        'emi_available' => 'boolean',
        'five_g_support' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function videos()
    {
        return $this->hasMany(ProductVideo::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function offers()
    {
        return $this->belongsToMany(Offer::class, 'offer_products')->withPivot('offer_price')->withTimestamps();
    }

    public function getEffectivePriceAttribute()
    {
        return $this->discount_price ?? $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->discount_price && $this->price > 0) {
            return round((($this->price - $this->discount_price) / $this->price) * 100);
        }
        return 0;
    }
}
