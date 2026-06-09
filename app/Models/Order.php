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
        'snap_token',
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

    public function syncWithMidtrans(): bool
    {
        if ($this->payment_method !== 'midtrans' || in_array($this->status, ['paid', 'processing', 'shipped', 'completed', 'cancelled', 'refunded'])) {
            return false;
        }

        try {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

            // Fetch transaction status from Midtrans API
            $status = \Midtrans\Transaction::status($this->order_number);
            if (!$status) {
                return false;
            }

            /** @var object $statusObj */
            $statusObj = (object) $status;

            // Convert raw object fields if they are inside StdClass
            $transactionStatus = $statusObj->transaction_status ?? null;
            $paymentType = $statusObj->payment_type ?? null;
            $transactionId = $statusObj->transaction_id ?? null;

            \Illuminate\Support\Facades\DB::beginTransaction();

            $transaction = $this->transaction()->first();
            $trxData = [
                'order_id'         => $this->id,
                'transaction_code' => $transactionId ?? ($transaction?->transaction_code ?? Transaction::generateCode()),
                'amount'           => $this->total_amount,
                'payment_method'   => 'midtrans',
                'bank_name'        => $paymentType,
                'account_name'     => $this->user->name,
                'status'           => 'pending',
            ];

            if (isset($statusObj->va_numbers[0]) && is_object($statusObj->va_numbers[0])) {
                $firstVa = $statusObj->va_numbers[0];
                if (isset($firstVa->bank)) {
                    $trxData['bank_name'] = strtoupper($firstVa->bank);
                }
            }

            if (!$transaction) {
                $transaction = \App\Models\Transaction::create($trxData);
            } else {
                $transaction->update($trxData);
            }

            $updated = false;

            if ($transactionStatus == 'capture') {
                $fraudStatus = $statusObj->fraud_status ?? null;
                if ($fraudStatus == 'challenge') {
                    $this->update(['status' => 'payment_uploaded']);
                    $transaction->update(['status' => 'pending']);
                    $updated = true;
                } else if ($fraudStatus == 'accept' || is_null($fraudStatus)) {
                    $this->update(['status' => 'paid', 'paid_at' => now()]);
                    $transaction->update(['status' => 'verified', 'verified_at' => now()]);
                    $updated = true;
                }
            } else if ($transactionStatus == 'settlement') {
                $this->update(['status' => 'paid', 'paid_at' => now()]);
                $transaction->update(['status' => 'verified', 'verified_at' => now()]);
                $updated = true;
            } else if ($transactionStatus == 'pending') {
                $this->update(['status' => 'payment_uploaded']);
                $transaction->update(['status' => 'pending']);
                $updated = true;
            } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $this->update(['status' => 'cancelled']);
                $transaction->update([
                    'status' => 'rejected',
                    'rejection_reason' => 'Pembayaran ' . $transactionStatus . ' via Midtrans'
                ]);
                $updated = true;
            }

            \Illuminate\Support\Facades\DB::commit();

            if ($updated) {
                try {
                    broadcast(new \App\Events\OrderStatusUpdated($this));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Broadcast OrderStatusUpdated gagal: ' . $e->getMessage());
                }
            }

            return $updated;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Midtrans sync failed for order ' . $this->order_number . ': ' . $e->getMessage());
            return false;
        }
    }
}
