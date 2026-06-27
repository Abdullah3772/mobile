<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'duration_days',
        'homepage_banner', 'top_search_placement', 'featured_badge',
        'max_products', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'homepage_banner' => 'boolean',
        'top_search_placement' => 'boolean',
        'featured_badge' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function advertisements()
    {
        return $this->hasMany(Advertisement::class);
    }
}
