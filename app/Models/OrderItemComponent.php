<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemComponent extends Model
{
    protected $fillable = ['order_item_id', 'material_name', 'qty', 'unit_price', 'subtotal'];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
