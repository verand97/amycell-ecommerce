@extends('admin.layouts.app')

@section('title', 'Detail Servis')
@section('page-title', 'Detail Servis HP')
@section('page-subtitle', $serviceOrder->service_number)

@section('content')

<div class="mb-4">
    <a href="{{ route('admin.services.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-400 hover:text-orange-400 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Daftar
    </a>
</div>

<div class="grid lg:grid-cols-3 gap-6">

    {{-- Left: Service Info --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Device Info --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
            <h3 class="font-bold text-white mb-4 flex items-center gap-2">📱 Informasi Perangkat</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-slate-500">Merk</span><p class="text-slate-200 font-semibold">{{ $serviceOrder->device_brand }}</p></div>
                <div><span class="text-slate-500">Model</span><p class="text-slate-200 font-semibold">{{ $serviceOrder->device_model }}</p></div>
                <div><span class="text-slate-500">Warna</span><p class="text-slate-200 font-semibold">{{ $serviceOrder->device_color ?? '-' }}</p></div>
                <div><span class="text-slate-500">IMEI</span><p class="text-slate-200 font-mono">{{ $serviceOrder->device_imei ?? '-' }}</p></div>
            </div>
            @if($serviceOrder->device_image_url)
                <div class="mt-4">
                    <span class="text-xs text-slate-500 mb-2 block">Foto Perangkat:</span>
                    <img src="{{ $serviceOrder->device_image_url }}" alt="Device" class="rounded-xl border border-slate-700 max-h-48 object-cover">
                </div>
            @endif
        </div>

        {{-- Damage Info --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
            <h3 class="font-bold text-white mb-4 flex items-center gap-2">⚠️ Detail Kerusakan</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-slate-500">Jenis Kerusakan</span>
                    <p class="text-slate-200 font-semibold">{{ $serviceOrder->damage_type_label }}</p>
                </div>
                <div>
                    <span class="text-slate-500">Deskripsi</span>
                    <p class="text-slate-300 bg-slate-800 rounded-lg p-3 mt-1">{{ $serviceOrder->damage_description }}</p>
                </div>
            </div>
        </div>

        {{-- Customer Info --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
            <h3 class="font-bold text-white mb-4 flex items-center gap-2">👤 Informasi Customer</h3>
            <div class="flex items-center gap-3 mb-4">
                <img src="{{ $serviceOrder->user->avatar_url }}" class="w-10 h-10 rounded-full object-cover" alt="Avatar">
                <div>
                    <p class="text-sm font-semibold text-slate-200">{{ $serviceOrder->user->name }}</p>
                    <p class="text-xs text-slate-500">{{ $serviceOrder->user->email }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-slate-500">Nama Kontak</span><p class="text-slate-200 font-semibold">{{ $serviceOrder->contact_name }}</p></div>
                <div><span class="text-slate-500">No. HP</span><p class="text-slate-200 font-semibold">{{ $serviceOrder->contact_phone }}</p></div>
                @if($serviceOrder->contact_address)
                <div class="col-span-2"><span class="text-slate-500">Alamat</span><p class="text-slate-200">{{ $serviceOrder->contact_address }}</p></div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right: Actions --}}
    <div class="space-y-6">

        {{-- Current Status --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
            <h3 class="font-bold text-white mb-3">Status Saat Ini</h3>
            @php
                $statusClasses = match($serviceOrder->status_color) {
                    'yellow' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                    'blue' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                    'indigo' => 'bg-indigo-500/20 text-indigo-400 border-indigo-500/30',
                    'amber' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                    'purple' => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
                    'cyan' => 'bg-cyan-500/20 text-cyan-400 border-cyan-500/30',
                    'green' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                    'emerald' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                    'red' => 'bg-red-500/20 text-red-400 border-red-500/30',
                    default => 'bg-slate-700 text-slate-300 border-slate-600',
                };
            @endphp
            <div class="p-3 rounded-xl border {{ $statusClasses }} text-center">
                <p class="font-bold text-lg">{{ $serviceOrder->status_label }}</p>
                @if($serviceOrder->fifo_position)
                    <p class="text-xs mt-1.5 font-bold uppercase tracking-wider">Antrean FIFO: #{{ $serviceOrder->fifo_position }}</p>
                @endif
            </div>
            <div class="mt-3 text-xs text-slate-500 space-y-1">
                <p>Dibuat: {{ $serviceOrder->created_at->translatedFormat('d M Y, H:i') }}</p>
                @if($serviceOrder->estimated_completion)
                    <p>Est. Selesai: {{ $serviceOrder->estimated_completion->translatedFormat('d M Y') }}</p>
                @endif
                @if($serviceOrder->completed_at)
                    <p>Selesai: {{ $serviceOrder->completed_at->translatedFormat('d M Y, H:i') }}</p>
                @endif
            </div>
              {{-- Cost Summary --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
            <h3 class="font-bold text-white mb-3">Biaya</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Estimasi</span>
                    <span class="text-orange-400 font-bold">{{ $serviceOrder->estimated_cost ? 'Rp ' . number_format($serviceOrder->estimated_cost, 0, ',', '.') : '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Final</span>
                    <span class="text-emerald-400 font-bold">{{ $serviceOrder->final_cost ? 'Rp ' . number_format($serviceOrder->final_cost, 0, ',', '.') : '—' }}</span>
                </div>
            </div>
        </div>

        {{-- Payment Info Card --}}
        @if($serviceOrder->status === 'completed' || $serviceOrder->status === 'picked_up' || $serviceOrder->payment_status !== 'unpaid')
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
            <h3 class="font-bold text-white mb-3">Informasi Pembayaran</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Metode</span>
                    <span class="text-slate-200 font-bold">{{ $serviceOrder->payment_method_label }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Status</span>
                    @if($serviceOrder->payment_status === 'paid')
                        <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-bold rounded-lg">Lunas</span>
                    @elseif($serviceOrder->payment_status === 'pending')
                        <span class="px-2.5 py-0.5 bg-amber-500/20 text-amber-400 border border-amber-500/30 text-xs font-bold rounded-lg">Menunggu Verifikasi</span>
                    @else
                        <span class="px-2.5 py-0.5 bg-red-500/20 text-red-400 border border-red-500/30 text-xs font-bold rounded-lg">Belum Dibayar</span>
                    @endif
                </div>
                @if($serviceOrder->paid_at)
                <div class="flex justify-between">
                    <span class="text-slate-500">Waktu Pembayaran</span>
                    <span class="text-slate-200 font-mono">{{ $serviceOrder->paid_at->translatedFormat('d M Y, H:i') }}</span>
                </div>
                @endif

                @if($serviceOrder->payment_proof)
                    <div class="pt-2 border-t border-slate-800">
                        <span class="text-xs text-slate-500 block mb-2">Bukti Transfer:</span>
                        <a href="{{ $serviceOrder->payment_proof_url }}" target="_blank" class="block rounded-lg overflow-hidden border border-slate-800 hover:border-slate-700 transition-colors">
                            <img src="{{ $serviceOrder->payment_proof_url }}" alt="Bukti Transfer" class="w-full max-h-40 object-cover">
                        </a>
                    </div>
                    @if($serviceOrder->payment_status === 'pending')
                        <form method="POST" action="{{ route('admin.services.verify-payment', $serviceOrder->id) }}" class="mt-3">
                            @csrf
                            <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition-colors shadow-lg shadow-emerald-900/20">
                                ✓ Verifikasi Pembayaran
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
        @endif

        {{-- Update Form --}}
        <div class="bg-slate-900 border border-orange-500/20 rounded-2xl p-5">
            <h3 class="font-bold text-white mb-4 flex items-center gap-2">🔄 Update Status</h3>
            <form method="POST" action="{{ route('admin.services.update-status', $serviceOrder->id) }}" class="space-y-4">
                @csrf
 
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500/50">
                        @foreach(['pending' => 'Menunggu', 'received' => 'Diterima', 'diagnosing' => 'Diagnosa', 'waiting_approval' => 'Menunggu Persetujuan', 'repairing' => 'Diperbaiki', 'testing' => 'Pengujian', 'completed' => 'Selesai', 'picked_up' => 'Sudah Diambil', 'cancelled' => 'Dibatalkan'] as $val => $label)
                            <option value="{{ $val }}" {{ $serviceOrder->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Status Pembayaran</label>
                    <select name="payment_status" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500/50">
                        @foreach(['unpaid' => 'Belum Dibayar', 'pending' => 'Menunggu Verifikasi', 'paid' => 'Lunas'] as $val => $label)
                            <option value="{{ $val }}" {{ $serviceOrder->payment_status === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Metode Pembayaran</label>
                    <select name="payment_method" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500/50">
                        <option value="" {{ is_null($serviceOrder->payment_method) ? 'selected' : '' }}>Belum Ditentukan</option>
                        @foreach(['cash' => 'Tunai (Cash)', 'transfer' => 'Transfer Bank (Manual)', 'midtrans' => 'Online (Midtrans)'] as $val => $label)
                            <option value="{{ $val }}" {{ $serviceOrder->payment_method === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Estimasi Biaya (Rp)</label>
                    <input type="number" name="estimated_cost" value="{{ $serviceOrder->estimated_cost }}" step="1000" min="0" placeholder="Contoh: 250000" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500/50">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Biaya Final (Rp)</label>
                    <input type="number" name="final_cost" value="{{ $serviceOrder->final_cost }}" step="1000" min="0" placeholder="Isi saat selesai" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500/50">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Estimasi Selesai</label>
                    <input type="date" name="estimated_completion" value="{{ $serviceOrder->estimated_completion?->format('Y-m-d') }}" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500/50">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Catatan Diagnosa</label>
                    <textarea name="diagnosis_notes" rows="2" placeholder="Hasil diagnosa..." class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500/50 resize-none">{{ $serviceOrder->diagnosis_notes }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Catatan Admin</label>
                    <textarea name="admin_notes" rows="2" placeholder="Catatan internal..." class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500/50 resize-none">{{ $serviceOrder->admin_notes }}</textarea>
                </div>

                <button type="submit" class="w-full py-2.5 bg-linear-to-r from-orange-500 to-red-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-orange-900/30 transition-all text-sm">
                    💾 Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

</div>

@endsection
