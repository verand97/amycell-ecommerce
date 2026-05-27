@extends('customer.layouts.app')

@section('title', 'Servis Saya')

@section('content')

<section class="max-w-5xl mx-auto px-4 py-10">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-800">🔧 Servis Saya</h1>
            <p class="text-slate-500 mt-1">Lacak semua permintaan servis HP kamu</p>
        </div>
        <a href="{{ route('customer.service.create') }}" class="px-5 py-2.5 bg-linear-to-r from-orange-500 to-red-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-orange-200 transition-all hover:-translate-y-0.5 text-sm">
            + Ajukan Servis Baru
        </a>
    </div>

    {{-- Service Orders --}}
    @if($serviceOrders->count() > 0)
        <div class="space-y-4">
            @foreach($serviceOrders as $service)
                @php
                    $statusClasses = match($service->status_color) {
                        'yellow'  => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                        'blue'    => 'bg-blue-100 text-blue-700 border-blue-200',
                        'indigo'  => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                        'amber'   => 'bg-amber-100 text-amber-700 border-amber-200',
                        'purple'  => 'bg-purple-100 text-purple-700 border-purple-200',
                        'cyan'    => 'bg-cyan-100 text-cyan-700 border-cyan-200',
                        'green'   => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                        'emerald' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                        'red'     => 'bg-red-100 text-red-700 border-red-200',
                        default   => 'bg-slate-100 text-slate-700 border-slate-200',
                    };
                @endphp
                <a href="{{ route('customer.service.show', $service->id) }}" class="block bg-white border border-slate-100 rounded-2xl p-5 hover:shadow-lg hover:border-orange-200 transition-all group">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-linear-to-br from-orange-100 to-red-100 rounded-xl flex items-center justify-center text-xl shrink-0">
                                📱
                            </div>
                            <div>
                                <p class="font-mono text-xs text-slate-400 mb-0.5">{{ $service->service_number }}</p>
                                <p class="font-bold text-slate-800">{{ $service->device_brand }} {{ $service->device_model }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $service->damage_type_label }} — {{ $service->created_at->translatedFormat('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $statusClasses }}">
                                {{ $service->status_label }}
                            </span>
                            @if($service->estimated_cost)
                                <span class="text-sm font-bold text-orange-600">Rp {{ number_format($service->estimated_cost, 0, ',', '.') }}</span>
                            @endif
                            <svg class="w-4 h-4 text-slate-300 group-hover:text-orange-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $serviceOrders->links() }}
        </div>
    @else
        <div class="text-center py-20 bg-white rounded-3xl border border-slate-100">
            <div class="text-6xl mb-4">📱</div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Belum ada servis</h3>
            <p class="text-slate-500 mb-6">Kamu belum pernah mengajukan permintaan servis HP.</p>
            <a href="{{ route('customer.service.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-linear-to-r from-orange-500 to-red-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all">
                🔧 Ajukan Servis Pertama
            </a>
        </div>
    @endif

</section>

@endsection
