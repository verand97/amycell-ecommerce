<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'admin_id',
        'subject',
        'status',
        'queue_position',
        'accepted_at',
        'closed_at',
        'unread_count_admin',
        'unread_count_customer',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    // === FIFO Scope ===
    public function scopeWaiting(Builder $query)
    {
        return $query->where('status', 'waiting')->orderBy('created_at', 'asc');
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFifoQueue(Builder $query)
    {
        return $query->where('status', 'waiting')
            ->orderBy('created_at', 'asc'); // FIFO: First In, First Out
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'session_id')->orderBy('created_at', 'asc');
    }

    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class, 'session_id')->latestOfMany();
    }

    public function getQueuePositionAttribute(): int
    {
        if ($this->status !== 'waiting') return 0;

        return self::where('status', 'waiting')
            ->where('created_at', '<', $this->created_at)
            ->count() + 1;
    }

    public function getWaitingTimeAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'waiting' => 'Dalam Antrian',
            'active'  => 'Aktif',
            'closed'  => 'Selesai',
            default   => ucfirst($this->status),
        };
    }
}
