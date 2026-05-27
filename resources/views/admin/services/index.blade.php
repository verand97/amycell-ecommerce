@extends('admin.layouts.app')

@section('title', 'Servis HP')
@section('page-title', 'Manajemen Servis HP')
@section('page-subtitle', 'Kelola semua permintaan servis smartphone')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 hover:border-orange-500/30 transition-all">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-slate-400 font-medium uppercase tracking-wide">Total Servis</span>
            <div class="w-8 h-8 bg-orange-500/10 rounded-lg flex items-center justify-center">🔧</div>
        </div>
        <p class="text-2xl font-black text-white">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 hover:border-yellow-500/30 transition-all">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-slate-400 font-medium uppercase tracking-wide">Pending</span>
            <div class="w-8 h-8 bg-yellow-500/10 rounded-lg flex items-center justify-center">⏳</div>
        </div>
        <p class="text-2xl font-black text-yellow-400">{{ $stats['pending'] }}</p>
    </div>
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 hover:border-purple-500/30 transition-all">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-slate-400 font-medium uppercase tracking-wide">Dalam Proses</span>
            <div class="w-8 h-8 bg-purple-500/10 rounded-lg flex items-center justify-center">🔧</div>
        </div>
        <p class="text-2xl font-black text-purple-400">{{ $stats['in_progress'] }}</p>
    </div>
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 hover:border-emerald-500/30 transition-all">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-slate-400 font-medium uppercase tracking-wide">Selesai</span>
            <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center">✅</div>
        </div>
        <p class="text-2xl font-black text-emerald-400">{{ $stats['completed'] }}</p>
    </div>
</div>

{{-- Filters --}}
<div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 mb-6">
    <form method="GET" class="flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor servis, nama, merk..." class="flex-1 px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 placeholder-slate-500 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500/50 transition-all">
        <select name="status" class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500/50">
            <option value="">Semua Status</option>
            @foreach(['pending' => 'Menunggu', 'received' => 'Diterima', 'diagnosing' => 'Diagnosa', 'waiting_approval' => 'Menunggu Persetujuan', 'repairing' => 'Diperbaiki', 'testing' => 'Pengujian', 'completed' => 'Selesai', 'picked_up' => 'Sudah Diambil', 'cancelled' => 'Dibatalkan'] as $val => $label)
                <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-5 py-2 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-500 transition-all text-sm">Cari</button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.services.index') }}" class="px-5 py-2 bg-slate-800 text-slate-300 font-semibold rounded-xl hover:bg-slate-700 transition-all text-sm text-center">Reset</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">No. Servis</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Customer</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Perangkat</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Kerusakan</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Biaya</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Tanggal</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($serviceOrders as $svc)
                    @php
                        $statusClasses = match($svc->status_color) {
                            'yellow' => 'bg-yellow-500/20 text-yellow-400',
                            'blue' => 'bg-blue-500/20 text-blue-400',
                            'indigo' => 'bg-indigo-500/20 text-indigo-400',
                            'amber' => 'bg-amber-500/20 text-amber-400',
                            'purple' => 'bg-purple-500/20 text-purple-400',
                            'cyan' => 'bg-cyan-500/20 text-cyan-400',
                            'green' => 'bg-emerald-500/20 text-emerald-400',
                            'emerald' => 'bg-emerald-500/20 text-emerald-400',
                            'red' => 'bg-red-500/20 text-red-400',
                            default => 'bg-slate-700 text-slate-300',
                        };
                    @endphp
                    <tr class="hover:bg-slate-800/50 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-mono text-xs text-sky-400">{{ $svc->service_number }}</p>
                        </td>
                        <td class="px-3 py-3">
                            <p class="text-xs font-medium text-slate-200">{{ $svc->user->name }}</p>
                            <p class="text-[10px] text-slate-500">{{ $svc->contact_phone }}</p>
                        </td>
                        <td class="px-3 py-3">
                            <p class="text-xs text-slate-200">{{ $svc->device_brand }}</p>
                            <p class="text-[10px] text-slate-500">{{ $svc->device_model }}</p>
                        </td>
                        <td class="px-3 py-3">
                            <span class="text-xs text-slate-300">{{ $svc->damage_type_label }}</span>
                        </td>
                        <td class="px-3 py-3">
                            <span class="px-2 py-1 text-[10px] font-bold rounded-full {{ $statusClasses }}">{{ $svc->status_label }}</span>
                        </td>
                        <td class="px-3 py-3">
                            @if($svc->estimated_cost)
                                <p class="text-xs font-bold text-orange-400">Rp {{ number_format($svc->estimated_cost, 0, ',', '.') }}</p>
                            @else
                                <span class="text-[10px] text-slate-600">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-3">
                            <p class="text-[10px] text-slate-500">{{ $svc->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.services.show', $svc->id) }}" class="text-[10px] text-orange-400 hover:text-orange-300 font-semibold">Detail →</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-slate-500">
                            <div class="text-4xl mb-2">📱</div>
                            <p>Belum ada permintaan servis</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($serviceOrders->hasPages())
        <div class="px-5 py-3 border-t border-slate-800">
            {{ $serviceOrders->withQueryString()->links() }}
        </div>
    @endif
</div>

@endsection
