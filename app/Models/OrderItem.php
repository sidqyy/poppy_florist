<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'product_name', 'is_rented', 'rental_duration', 'qty', 'price', 'subtotal'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function components()
    {
        return $this->hasMany(OrderItemComponent::class);
    }
}
