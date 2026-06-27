<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'image_path', 'is_primary', 'is_360', 'sort_order'];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_360' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
