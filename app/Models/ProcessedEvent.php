<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessedEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'processed_at',
    ];
}
