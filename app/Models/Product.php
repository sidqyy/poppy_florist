<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'image', 'price_type', 'total_price', 'max_price', 'is_active', 'availability', 'is_rentable', 'rental_price_per_day', 'has_flexible_components', 'max_flexible_components'];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function components()
    {
        return $this->hasMany(ProductComponent::class);
    }

    public function getFormattedPriceAttribute()
    {
        if ($this->price_type === 'range' && $this->max_price) {
            return 'Rp ' . number_format($this->total_price, 0, ',', '.') . ' - Rp ' . number_format($this->max_price, 0, ',', '.');
        }
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }
    public function sizes()
{
    return $this->hasMany(ProductSize::class);
}
}
