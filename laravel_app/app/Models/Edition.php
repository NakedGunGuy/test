<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function set(): BelongsTo
    {
        return $this->belongsTo(Set::class);
    }

    public function game()
    {
        return $this->hasOneThrough(Game::class, Card::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
