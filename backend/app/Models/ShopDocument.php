<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopDocument extends Model
{
    use HasFactory;

    protected $fillable = ['shop_id', 'title', 'file_path', 'file_type'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
