<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Edition extends Model
{
    protected $fillable = [
        'uuid',
        'card_id',
        'card_uuid',
        'collector_number',
        'slug',
        'flavor',
        'illustrator',
        'rarity',
        'set_id',
        'last_update',
    ];

    protected $casts = [
        'last_update' => 'datetime',
    ];

    // Define relationships
    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function set()
    {
        return $this->belongsTo(Set::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
