<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_number',
        'user_id',
        'device_brand',
        'device_model',
        'device_color',
        'device_imei',
        'damage_type',
        'damage_description',
        'device_image',
        'contact_name',
        'contact_phone',
        'contact_address',
        'status',
        'estimated_cost',
        'final_cost',
        'admin_notes',
        'diagnosis_notes',
        'estimated_completion',
        'completed_at',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'final_cost' => 'decimal:2',
        'estimated_completion' => 'date',
        'completed_at' => 'datetime',
    ];

    public static function generateServiceNumber(): string
    {
        $prefix = 'SRV';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -5));
        return "{$prefix}-{$date}-{$random}";
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'           => 'Menunggu',
            'received'          => 'Diterima',
            'diagnosing'        => 'Diagnosa',
            'waiting_approval'  => 'Menunggu Persetujuan',
            'repairing'         => 'Diperbaiki',
            'testing'           => 'Pengujian',
            'completed'         => 'Selesai',
            'picked_up'         => 'Sudah Diambil',
            'cancelled'         => 'Dibatalkan',
            default             => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'           => 'yellow',
            'received'          => 'blue',
            'diagnosing'        => 'indigo',
            'waiting_approval'  => 'amber',
            'repairing'         => 'purple',
            'testing'           => 'cyan',
            'completed'         => 'green',
            'picked_up'         => 'emerald',
            'cancelled'         => 'red',
            default             => 'gray',
        };
    }

    public function getStatusStepAttribute(): int
    {
        return match ($this->status) {
            'pending'           => 1,
            'received'          => 2,
            'diagnosing'        => 3,
            'waiting_approval'  => 3,
            'repairing'         => 4,
            'testing'           => 5,
            'completed'         => 6,
            'picked_up'         => 7,
            'cancelled'         => 0,
            default             => 0,
        };
    }

    public function getDamageTypeLabelAttribute(): string
    {
        return match ($this->damage_type) {
            'lcd'            => 'LCD / Layar',
            'battery'        => 'Baterai',
            'charging_port'  => 'Port Charging',
            'software'       => 'Software / Sistem',
            'water_damage'   => 'Terkena Air',
            'speaker'        => 'Speaker / Audio',
            'camera'         => 'Kamera',
            'button'         => 'Tombol',
            'other'          => 'Lainnya',
            default          => ucfirst($this->damage_type),
        };
    }

    public function getDeviceImageUrlAttribute(): ?string
    {
        if ($this->device_image) {
            return asset('storage/' . $this->device_image);
        }
        return null;
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'picked_up', 'cancelled']);
    }

    public function getFifoPositionAttribute(): ?int
    {
        if (in_array($this->status, ['completed', 'picked_up', 'cancelled'])) {
            return null;
        }

        $statuses = [];
        if ($this->status === 'pending') {
            $statuses = ['pending'];
        } else {
            $statuses = ['received', 'diagnosing', 'waiting_approval', 'repairing', 'testing'];
        }

        return self::whereIn('status', $statuses)
            ->where('created_at', '<', $this->created_at)
            ->count() + 1;
    }
}
