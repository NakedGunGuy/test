<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

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
    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }

    public function card()
    {
        return $this->hasOneThrough(Card::class, Edition::class);
    }

    public function set()
    {
        return $this->hasOneThrough(Set::class, Edition::class);
    }

    public function game()
    {
        return $this->hasOneThrough(Game::class, Card::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_items')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}
