<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    protected $fillable = [
        'name',
        'prefix',
        'language',
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
}
