<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
    ];

    // Define relationships
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
