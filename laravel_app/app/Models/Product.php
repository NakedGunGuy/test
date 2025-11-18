<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'edition_id',
        'name',
        'price',
        'quantity',
        'is_foil',
        'is_used',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_foil' => 'boolean',
        'is_used' => 'boolean',
        'quantity' => 'integer',
    ];

    // Define relationships
    public function edition()
    {
        return $this->belongsTo(Edition::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
