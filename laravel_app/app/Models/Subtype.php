<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subtype extends Model
{
    protected $fillable = [
        'value',
    ];

    // Define relationships
    public function cards(): BelongsToMany
    {
        return $this->belongsToMany(Card::class, 'card_subtypes');
    }
}
