<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_code',
        'amount',
        'payment_method',
        'bank_name',
        'account_number',
        'account_name',
        'proof_image',
        'status',
        'verified_by',
        'verified_at',
        'notes',
        'rejection_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public static function generateCode(): string
    {
        return 'TRX-' . strtoupper(substr(uniqid(), -8));
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getProofImageUrlAttribute(): ?string
    {
        if ($this->proof_image) {
            return asset('storage/' . $this->proof_image);
        }
        return null;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
            default    => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'yellow',
            'verified' => 'green',
            'rejected' => 'red',
            default    => 'gray',
        };
    }
}
