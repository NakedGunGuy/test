<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
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
    public function editions()
    {
        return $this->hasMany(Edition::class);
    }

    public function subtypes()
    {
        return $this->belongsToMany(Subtype::class, 'card_subtypes');
    }

    public function types()
    {
        return $this->belongsToMany(Type::class, 'card_types');
    }

    public function cardClasses()
    {
        return $this->belongsToMany(CardClass::class, 'card_card_classes');
    }
}
