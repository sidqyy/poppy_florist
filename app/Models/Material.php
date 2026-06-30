<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'name',
        'type',
        'unit',
        'price',
        'price_stem',
        'price_arrangement',
        'stock',
        'min_stock',
        'freshness_days',
        'image',
        'is_active',
    ];

    public function stockMutations()
    {
        return $this->hasMany(StockMutation::class);
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedPriceStemAttribute()
    {
        return 'Rp ' . number_format($this->price_stem, 0, ',', '.');
    }

    public function getFormattedPriceArrangementAttribute()
    {
        return 'Rp ' . number_format($this->price_arrangement, 0, ',', '.');
    }
}