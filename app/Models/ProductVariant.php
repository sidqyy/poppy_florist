<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_size_id',
        'variant_name',
        'price',
        'image',
        'is_active'
    ];

    public function size()
    {
        return $this->belongsTo(ProductSize::class,'product_size_id');
    }
}