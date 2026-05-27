<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_name', 'customer_phone', 'recipient_name', 'recipient_phone',
        'delivery_method', 'delivery_address', 'delivery_distance', 'delivery_fee', 'discount', 
        'delivery_lat', 'delivery_lng', 'scheduled_at', 'status', 'payment_status', 
        'total_amount', 'notes', 'user_id', 'reference_image', 'budget', 'source',
        'is_urgent', 'estimated_time', 'florist_notes', 'started_at', 'completed_at', 'handled_by',
        'external_id', 'greeting_card', 'payment_proof', 'product_name'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'is_urgent' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function images()
    {
        return $this->hasMany(OrderImage::class);
    }

    // Accessors untuk pembayaran
    public function getTotalDibayarAttribute()
    {
        return $this->payments()->where('status', 'verified')->sum('amount');
    }

    public function getSisaTagihanAttribute()
    {
        return max(0, $this->total_amount - $this->total_dibayar);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
