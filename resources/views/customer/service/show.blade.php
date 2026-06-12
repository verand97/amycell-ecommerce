@extends('customer.layouts.app')

@section('title', 'Detail Servis')

@section('content')

<section class="max-w-4xl mx-auto px-4 py-10">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('customer.service') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-orange-600 transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-black text-slate-800">Detail Servis</h1>
                <p class="text-sm text-slate-500 font-mono mt-1">{{ $serviceOrder->service_number }}</p>
                @if($serviceOrder->fifo_position)
                    <div class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 bg-orange-50 text-orange-700 text-xs font-bold rounded-xl border border-orange-100 animate-pulse">
                        ⏳ Posisi Antrean FIFO: #{{ $serviceOrder->fifo_position }}
                    </div>
                @endif
            </div>
            @php
                $sc = match($serviceOrder->status_color) {
                    'yellow' => 'bg-yellow-100 text-yellow-700', 'blue' => 'bg-blue-100 text-blue-700',
                    'indigo' => 'bg-indigo-100 text-indigo-700', 'amber' => 'bg-amber-100 text-amber-700',
                    'purple' => 'bg-purple-100 text-purple-700', 'cyan' => 'bg-cyan-100 text-cyan-700',
                    'green' => 'bg-emerald-100 text-emerald-700', 'emerald' => 'bg-emerald-100 text-emerald-700',
                    'red' => 'bg-red-100 text-red-700', default => 'bg-slate-100 text-slate-700',
                };
            @endphp
            <span class="px-4 py-1.5 text-sm font-bold rounded-full {{ $sc }}">{{ $serviceOrder->status_label }}</span>
        </div>
    </div>

    {{-- Status Timeline --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-6 shadow-sm">
        <h3 class="font-bold text-slate-800 mb-5">📍 Status Tracking</h3>
        @php
            $currentStep = $serviceOrder->status_step;
            $steps = [
                ['step' => 1, 'label' => 'Diajukan', 'icon' => '📝'],
                ['step' => 2, 'label' => 'Diterima', 'icon' => '📥'],
                ['step' => 3, 'label' => 'Diagnosa', 'icon' => '🔍'],
                ['step' => 4, 'label' => 'Perbaikan', 'icon' => '🔧'],
                ['step' => 5, 'label' => 'Pengujian', 'icon' => '🧪'],
                ['step' => 6, 'label' => 'Selesai', 'icon' => '✅'],
                ['step' => 7, 'label' => 'Diambil', 'icon' => '🎉'],
            ];
        @endphp

        @if($serviceOrder->status === 'cancelled')
            <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl">
                <span class="text-2xl">❌</span>
                <div>
                    <p class="font-bold text-red-700">Servis Dibatalkan</p>
                    <p class="text-xs text-red-500">Permintaan servis ini telah dibatalkan.</p>
                </div>
            </div>
        @else
            <div class="flex items-center justify-between overflow-x-auto pb-2">
                @foreach($steps as $i => $s)
                    <div class="flex items-center {{ $i < count($steps) - 1 ? 'flex-1' : '' }}">
                        <div class="flex flex-col items-center min-w-[60px]">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg border-2 transition-all
                                {{ $currentStep >= $s['step'] ? 'bg-orange-500 border-orange-500 text-white shadow-lg shadow-orange-200' : 'bg-slate-100 border-slate-200 text-slate-400' }}">
                                {{ $currentStep >= $s['step'] ? $s['icon'] : $s['step'] }}
                            </div>
                            <span class="text-[10px] font-medium mt-1.5 {{ $currentStep >= $s['step'] ? 'text-orange-600' : 'text-slate-400' }} text-center whitespace-nowrap">{{ $s['label'] }}</span>
                        </div>
                        @if($i < count($steps) - 1)
                            <div class="flex-1 h-0.5 mx-1 rounded {{ $currentStep > $s['step'] ? 'bg-orange-400' : 'bg-slate-200' }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Approval Card --}}
    @if($serviceOrder->status === 'waiting_approval' && $serviceOrder->estimated_cost)
        <div class="bg-linear-to-r from-amber-50 to-orange-50 border-2 border-amber-300 rounded-2xl p-6 mb-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-2xl shrink-0">💰</div>
                <div class="flex-1">
                    <h3 class="font-bold text-amber-800 text-lg">Persetujuan Estimasi Biaya</h3>
                    <p class="text-sm text-amber-700 mt-1">Teknisi telah mendiagnosa HP kamu. Estimasi biaya perbaikan:</p>
                    <p class="text-3xl font-black text-orange-600 my-3">Rp {{ number_format($serviceOrder->estimated_cost, 0, ',', '.') }}</p>
                    @if($serviceOrder->diagnosis_notes)
                        <p class="text-sm text-amber-700 bg-amber-100/50 rounded-lg p-3 mb-4">💬 {{ $serviceOrder->diagnosis_notes }}</p>
                    @endif
                    <div class="flex gap-3">
                        <form method="POST" action="{{ route('customer.service.approve', $serviceOrder->id) }}">
                            @csrf
                            <button type="submit" class="px-6 py-2.5 bg-linear-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-xl hover:shadow-lg transition-all text-sm">✅ Setuju & Lanjutkan</button>
                        </form>
                        <form method="POST" action="{{ route('customer.service.cancel', $serviceOrder->id) }}" onsubmit="return confirm('Yakin ingin membatalkan servis?')">
                            @csrf
                            <button type="submit" class="px-6 py-2.5 bg-slate-200 text-slate-700 font-bold rounded-xl hover:bg-red-100 hover:text-red-700 transition-all text-sm">❌ Batalkan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Device Info --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">📱 Informasi Perangkat</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Merk</dt><dd class="font-semibold text-slate-800">{{ $serviceOrder->device_brand }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Model</dt><dd class="font-semibold text-slate-800">{{ $serviceOrder->device_model }}</dd></div>
                @if($serviceOrder->device_color)
                <div class="flex justify-between"><dt class="text-slate-500">Warna</dt><dd class="font-semibold text-slate-800">{{ $serviceOrder->device_color }}</dd></div>
                @endif
                @if($serviceOrder->device_imei)
                <div class="flex justify-between"><dt class="text-slate-500">IMEI</dt><dd class="font-mono text-slate-800">{{ $serviceOrder->device_imei }}</dd></div>
                @endif
            </dl>
            @if($serviceOrder->device_image_url)
                <div class="mt-4">
                    <img src="{{ $serviceOrder->device_image_url }}" alt="Device" class="rounded-xl border border-slate-200 max-h-48 object-cover">
                </div>
            @endif
        </div>

        {{-- Damage & Cost --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">⚠️ Detail Kerusakan</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Jenis</dt><dd class="font-semibold text-slate-800">{{ $serviceOrder->damage_type_label }}</dd></div>
                <div><dt class="text-slate-500 mb-1">Deskripsi</dt><dd class="text-slate-700 bg-slate-50 rounded-lg p-3">{{ $serviceOrder->damage_description }}</dd></div>
                @if($serviceOrder->estimated_cost)
                <div class="flex justify-between"><dt class="text-slate-500">Estimasi Biaya</dt><dd class="font-bold text-orange-600">Rp {{ number_format($serviceOrder->estimated_cost, 0, ',', '.') }}</dd></div>
                @endif
                @if($serviceOrder->final_cost)
                <div class="flex justify-between"><dt class="text-slate-500">Biaya Final</dt><dd class="font-bold text-emerald-600">Rp {{ number_format($serviceOrder->final_cost, 0, ',', '.') }}</dd></div>
                @endif
                @if($serviceOrder->estimated_completion)
                <div class="flex justify-between"><dt class="text-slate-500">Est. Selesai</dt><dd class="font-semibold text-slate-800">{{ $serviceOrder->estimated_completion->translatedFormat('d M Y') }}</dd></div>
                @endif
            </dl>
            @if($serviceOrder->diagnosis_notes && $serviceOrder->status !== 'waiting_approval')
                <div class="mt-4 p-3 bg-sky-50 border border-sky-200 rounded-xl">
                    <p class="text-xs font-bold text-sky-700 mb-1">Catatan Diagnosa:</p>
                    <p class="text-sm text-sky-600">{{ $serviceOrder->diagnosis_notes }}</p>
                </div>
            @endif
            @if($serviceOrder->admin_notes)
                <div class="mt-3 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                    <p class="text-xs font-bold text-slate-600 mb-1">Catatan Admin:</p>
                    <p class="text-sm text-slate-500">{{ $serviceOrder->admin_notes }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Cancel button --}}
    @if(!in_array($serviceOrder->status, ['completed', 'picked_up', 'cancelled', 'waiting_approval']))
        <div class="mt-6 text-center">
            <form method="POST" action="{{ route('customer.service.cancel', $serviceOrder->id) }}" onsubmit="return confirm('Yakin ingin membatalkan servis ini?')">
                @csrf
                <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-medium transition-colors">Batalkan Servis →</button>
            </form>
        </div>
    @endif

</section>
@endsection
