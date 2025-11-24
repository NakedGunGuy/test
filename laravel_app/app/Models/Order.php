<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
                    ->withPivot('quantity', 'price', 'name')
                    ->withTimestamps();
    }

    public function shippingTier(): BelongsTo
    {
        return $this->belongsTo(ShippingWeightTier::class);
    }
}
