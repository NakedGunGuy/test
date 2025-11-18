<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailQueue extends Model
{
    protected $fillable = [
        'to_email',
        'subject',
        'template',
        'data',
        'from_email',
        'from_name',
        'status',
        'attempts',
        'max_attempts',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'data' => 'array',
        'sent_at' => 'datetime',
        'attempts' => 'integer',
        'max_attempts' => 'integer',
    ];

    // No specific relationships for EmailQueue model
}
