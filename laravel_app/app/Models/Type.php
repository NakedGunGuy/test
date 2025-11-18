<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $fillable = [
        'value',
    ];

    // Define relationships
    public function cards()
    {
        return $this->belongsToMany(Card::class, 'card_types');
    }
}
