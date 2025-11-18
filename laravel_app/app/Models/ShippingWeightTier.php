<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingWeightTier extends Model
{
    protected $fillable = [
        'country_id',
        'tier_name',
        'max_weight_kg',
        'price',
        'is_enabled',
        'sort_order',
    ];

    protected $casts = [
        'max_weight_kg' => 'decimal:2',
        'price' => 'decimal:2',
        'is_enabled' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Define relationships
    public function country()
    {
        return $this->belongsTo(ShippingCountry::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'shipping_tier_id');
    }
}
