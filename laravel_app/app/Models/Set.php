<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Set extends Model
{
    protected $fillable = [
        'game_id',
        'name',
        'prefix',
        'language',
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

    public function cards()
    {
        return $this->hasManyThrough(Card::class, Edition::class);
    }

    // Additional relationship for products through editions
    public function products()
    {
        return $this->hasManyThrough(Product::class, Edition::class);
    }
}
