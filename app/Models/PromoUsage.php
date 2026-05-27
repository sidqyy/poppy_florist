<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoUsage extends Model
{
    protected $fillable = ['promo_id', 'order_id', 'discount_amount'];

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
