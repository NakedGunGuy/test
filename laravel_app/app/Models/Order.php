<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'total_amount',
        'tracking_number',
        'shipping_address',
        'notes',
        'shipping_country',
        'shipping_weight_grams',
        'shipping_cost',
        'shipping_tier_id',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_weight_grams' => 'integer',
        'shipping_cost' => 'decimal:2',
    ];

    // Define relationships
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingTier()
    {
        return $this->belongsTo(ShippingWeightTier::class);
    }
}
