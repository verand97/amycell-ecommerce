<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'subtotal',
        'shipping_cost',
        'total_amount',
        'status',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_postal_code',
        'payment_method',
        'payment_proof',
        'notes',
        'admin_notes',
        'paid_at',
        'shipped_at',
        'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public static function generateOrderNumber(): string
    {
        $prefix = 'AMY';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -5));
        return "{$prefix}-{$date}-{$random}";
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'          => 'Menunggu',
            'awaiting_payment' => 'Menunggu Pembayaran',
            'payment_uploaded' => 'Bukti Bayar Dikirim',
            'paid'             => 'Pembayaran Dikonfirmasi',
            'processing'       => 'Diproses',
            'shipped'          => 'Dikirim',
            'completed'        => 'Selesai',
            'cancelled'        => 'Dibatalkan',
            'refunded'         => 'Dikembalikan',
            default            => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending', 'awaiting_payment' => 'yellow',
            'payment_uploaded'            => 'blue',
            'paid', 'processing'          => 'indigo',
            'shipped'                     => 'purple',
            'completed'                   => 'green',
            'cancelled', 'refunded'       => 'red',
            default                       => 'gray',
        };
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        if ($this->payment_proof) {
            return asset('storage/' . $this->payment_proof);
        }
        return null;
    }
}
