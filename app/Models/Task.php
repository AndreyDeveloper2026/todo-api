<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_DONE = 'done';

    protected $fillable = [
        'title',
        'description',
        'status',
        'user_id',
    ];

    public function scopeStatus($query, $status)
    {
        return $query->when(
            $status,
            fn ($q) => $q->where('status', $status)
        );
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
