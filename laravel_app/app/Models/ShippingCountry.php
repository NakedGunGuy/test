<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingCountry extends Model
{
    protected $fillable = [
        'country_code',
        'country_name',
        'estimated_days_min',
        'estimated_days_max',
        'is_enabled',
    ];

    protected $casts = [
        'estimated_days_min' => 'integer',
        'estimated_days_max' => 'integer',
        'is_enabled' => 'boolean',
    ];

    // Define relationships
    public function shippingWeightTiers(): HasMany
    {
        return $this->hasMany(ShippingWeightTier::class);
    }
}
