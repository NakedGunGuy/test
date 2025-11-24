<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'abbreviation',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Define relationships
    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    public function sets(): HasMany
    {
        return $this->hasMany(Set::class);
    }

    public function editions()
    {
        return $this->hasManyThrough(Edition::class, Card::class);
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, Card::class);
    }
}