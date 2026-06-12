<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'type',
        'link',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Scope for unread notifications.
     */
    public function scopeUnread(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->whereNull('read_at');
    }
}
