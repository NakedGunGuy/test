<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Card extends Model
{
    protected $fillable = [
        'game_id',
        'uuid',
        'element',
        'name',
        'slug',
        'effect',
        'effect_raw',
        'flavor',
        'cost_memory',
        'cost_reserve',
        'level',
        'power',
        'life',
        'durability',
        'speed',
        'last_update',
    ];

    protected $casts = [
        'last_update' => 'datetime',
    ];

    // Define relationships
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function editions(): HasMany
    {
        return $this->hasMany(Edition::class);
    }

    public function set()
    {
        return $this->hasOneThrough(Set::class, Edition::class);
    }

    public function subtypes(): BelongsToMany
    {
        return $this->belongsToMany(Subtype::class, 'card_subtypes');
    }

    public function types(): BelongsToMany
    {
        return $this->belongsToMany(Type::class, 'card_types');
    }

    public function cardClasses(): BelongsToMany
    {
        return $this->belongsToMany(CardClass::class, 'card_card_classes');
    }

    // Additional relationship for products through editions
    public function products()
    {
        return $this->hasManyThrough(Product::class, Edition::class);
    }
}
