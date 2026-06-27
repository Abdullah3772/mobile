<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'slug', 'owner_name', 'business_registration_number',
        'nic', 'address', 'district', 'phone', 'whatsapp', 'email',
        'google_map_lat', 'google_map_lng', 'logo', 'cover_image', 'about',
        'opening_hours', 'status', 'rejection_reason', 'is_verified',
        'subscription_plan', 'subscription_expires_at', 'rating', 'total_reviews',
        'total_products', 'total_views',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'is_verified' => 'boolean',
        'subscription_expires_at' => 'datetime',
        'rating' => 'decimal:2',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documents()
    {
        return $this->hasMany(ShopDocument::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'shop_followers')->withTimestamps();
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function advertisements()
    {
        return $this->hasMany(Advertisement::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
