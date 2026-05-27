@extends('customer.layouts.app')

@section('title', 'Jasa Servis HP')

@section('content')

{{-- Hero Section --}}
<section class="relative overflow-hidden bg-linear-to-br from-slate-900 via-orange-950 to-red-950 text-white min-h-[80vh] flex items-center">
    {{-- Background decorations --}}
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-20 left-10 w-72 h-72 bg-orange-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-red-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-orange-400/5 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 py-20 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-orange-500/20 border border-orange-400/30 rounded-full text-orange-300 text-sm font-medium mb-6 backdrop-blur-sm">
            <span class="w-2 h-2 bg-orange-400 rounded-full animate-pulse"></span>
            Teknisi Berpengalaman • Garansi Servis • Harga Transparan
        </div>

        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black leading-tight mb-6">
            Jasa Servis
            <span class="bg-linear-to-r from-orange-400 to-red-400 bg-clip-text text-transparent block">Smartphone</span>
        </h1>
        <p class="text-slate-300 text-xl max-w-2xl mx-auto mb-10 leading-relaxed">
            HP rusak? Tenang, Amycell siap membantu! Layanan perbaikan profesional untuk semua merk smartphone dengan garansi dan harga terjangkau.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
            @auth
                <a href="{{ route('customer.service.create') }}" class="px-8 py-4 bg-linear-to-r from-orange-500 to-red-600 rounded-2xl text-white font-bold text-lg hover:shadow-2xl hover:shadow-orange-500/30 transition-all hover:-translate-y-1 hover:scale-105">
                    🔧 Ajukan Servis Sekarang
                </a>
                <a href="{{ route('customer.service') }}" class="px-8 py-4 bg-white/10 border border-white/20 rounded-2xl text-white font-semibold text-lg hover:bg-white/20 transition-all backdrop-blur-sm">
                    📋 Servis Saya
                </a>
            @else
                <a href="{{ route('login') }}" class="px-8 py-4 bg-linear-to-r from-orange-500 to-red-600 rounded-2xl text-white font-bold text-lg hover:shadow-2xl hover:shadow-orange-500/30 transition-all hover:-translate-y-1 hover:scale-105">
                    🔧 Ajukan Servis Sekarang
                </a>
            @endauth
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-4 max-w-lg mx-auto">
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-4">
                <p class="text-2xl font-black text-orange-400">500+</p>
                <p class="text-slate-400 text-xs mt-1">HP Diperbaiki</p>
            </div>
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-4">
                <p class="text-2xl font-black text-red-400">98%</p>
                <p class="text-slate-400 text-xs mt-1">Puas</p>
            </div>
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-4">
                <p class="text-2xl font-black text-amber-400">1-3</p>
                <p class="text-slate-400 text-xs mt-1">Hari Kerja</p>
            </div>
        </div>
    </div>
</section>

{{-- Service Types --}}
<section class="max-w-7xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-black text-slate-800">Layanan Perbaikan Kami</h2>
        <p class="text-slate-500 mt-2">Kami menangani berbagai jenis kerusakan smartphone</p>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @php
            $services = [
                ['icon' => '📱', 'title' => 'LCD / Layar', 'desc' => 'Ganti layar pecah, LCD bergaris, atau touchscreen tidak responsif', 'color' => 'sky'],
                ['icon' => '🔋', 'title' => 'Baterai', 'desc' => 'Ganti baterai bocor, cepat habis, atau tidak bisa charge', 'color' => 'emerald'],
                ['icon' => '🔌', 'title' => 'Port Charging', 'desc' => 'Perbaikan port USB longgar atau tidak terdeteksi', 'color' => 'purple'],
                ['icon' => '💻', 'title' => 'Software', 'desc' => 'Install ulang, hapus virus, bootloop, atau FRP bypass', 'color' => 'indigo'],
                ['icon' => '💧', 'title' => 'Water Damage', 'desc' => 'Penanganan HP terkena air atau cairan lainnya', 'color' => 'blue'],
                ['icon' => '🔊', 'title' => 'Speaker / Audio', 'desc' => 'Perbaikan speaker pecah, mic mati, atau audio bermasalah', 'color' => 'amber'],
                ['icon' => '📷', 'title' => 'Kamera', 'desc' => 'Ganti kamera depan/belakang blur atau tidak berfungsi', 'color' => 'rose'],
                ['icon' => '⚙️', 'title' => 'Lainnya', 'desc' => 'Tombol power, volume, WiFi, Bluetooth, dan lainnya', 'color' => 'slate'],
            ];
        @endphp

        @foreach($services as $svc)
        <div class="group p-6 bg-white rounded-2xl border border-slate-100 hover:border-{{ $svc['color'] }}-200 hover:shadow-xl hover:shadow-{{ $svc['color'] }}-100/50 transition-all hover:-translate-y-1">
            <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">{{ $svc['icon'] }}</div>
            <h3 class="font-bold text-slate-800 text-sm mb-1">{{ $svc['title'] }}</h3>
            <p class="text-slate-500 text-xs leading-relaxed">{{ $svc['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- How It Works --}}
<section class="bg-linear-to-br from-slate-800 to-slate-900 py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-white">Cara Kerja</h2>
            <p class="text-slate-400 mt-2">4 langkah mudah untuk memperbaiki HP kamu</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $steps = [
                    ['num' => '01', 'icon' => '📝', 'title' => 'Ajukan Servis', 'desc' => 'Isi form permintaan servis dengan detail kerusakan HP kamu', 'color' => 'from-orange-500 to-amber-500'],
                    ['num' => '02', 'icon' => '🔍', 'title' => 'Diagnosa', 'desc' => 'Teknisi kami akan memeriksa dan memberikan estimasi biaya', 'color' => 'from-sky-500 to-blue-500'],
                    ['num' => '03', 'icon' => '🔧', 'title' => 'Perbaikan', 'desc' => 'Setelah kamu setuju, proses perbaikan segera dilakukan', 'color' => 'from-purple-500 to-indigo-500'],
                    ['num' => '04', 'icon' => '✅', 'title' => 'Selesai', 'desc' => 'HP kamu sudah seperti baru! Ambil dan nikmati garansinya', 'color' => 'from-emerald-500 to-teal-500'],
                ];
            @endphp

            @foreach($steps as $step)
            <div class="relative group">
                <div class="bg-slate-800/50 border border-slate-700 rounded-2xl p-6 hover:border-slate-600 transition-all hover:-translate-y-1">
                    <div class="w-12 h-12 bg-linear-to-br {{ $step['color'] }} rounded-xl flex items-center justify-center text-2xl mb-4 shadow-lg group-hover:scale-110 transition-transform">
                        {{ $step['icon'] }}
                    </div>
                    <span class="text-xs font-bold text-slate-600 tracking-widest">STEP {{ $step['num'] }}</span>
                    <h3 class="font-bold text-white text-lg mt-1 mb-2">{{ $step['title'] }}</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">{{ $step['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="max-w-7xl mx-auto px-4 py-16">
    <div class="bg-linear-to-r from-orange-500 to-red-600 rounded-3xl p-8 md:p-12 text-center text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMTAiIGN5PSIxMCIgcj0iMS41IiBmaWxsPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMSkiLz48L3N2Zz4=')] opacity-50"></div>
        <div class="relative">
            <h2 class="text-3xl md:text-4xl font-black mb-4">HP Bermasalah? Servis Sekarang!</h2>
            <p class="text-orange-100 text-lg mb-8 max-w-xl mx-auto">Jangan biarkan HP rusak menghambat aktivitasmu. Dapatkan perbaikan cepat, berkualitas, dan bergaransi.</p>
            @auth
                <a href="{{ route('customer.service.create') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-orange-600 font-bold text-lg rounded-2xl hover:shadow-2xl hover:shadow-white/20 transition-all hover:-translate-y-1 hover:scale-105">
                    🔧 Ajukan Servis
                </a>
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-orange-600 font-bold text-lg rounded-2xl hover:shadow-2xl hover:shadow-white/20 transition-all hover:-translate-y-1 hover:scale-105">
                    🔧 Login & Ajukan Servis
                </a>
            @endauth
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="max-w-4xl mx-auto px-4 pb-16">
    <div class="text-center mb-10">
        <h2 class="text-3xl font-black text-slate-800">Pertanyaan Umum</h2>
    </div>
    <div class="space-y-3">
        @php
            $faqs = [
                ['q' => 'Berapa lama waktu pengerjaan servis?', 'a' => 'Rata-rata 1-3 hari kerja tergantung tingkat kerusakan. Kerusakan ringan bisa selesai dalam hitungan jam.'],
                ['q' => 'Apakah ada garansi setelah servis?', 'a' => 'Ya! Setiap perbaikan kami berikan garansi servis 30 hari untuk kerusakan yang sama.'],
                ['q' => 'Bagaimana cara mengetahui estimasi biaya?', 'a' => 'Setelah mengajukan servis, teknisi kami akan melakukan diagnosa dan memberikan estimasi biaya. Kamu bisa menyetujui atau membatalkan.'],
                ['q' => 'Apakah bisa servis untuk semua merk HP?', 'a' => 'Ya, kami melayani semua merk smartphone populer: Samsung, iPhone, Xiaomi, OPPO, Vivo, Realme, dan lainnya.'],
                ['q' => 'Bagaimana cara melacak status servis?', 'a' => 'Setelah login, kamu bisa melihat status servis secara real-time di halaman "Servis Saya" dengan timeline yang detail.'],
            ];
        @endphp

        @foreach($faqs as $i => $faq)
        <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden hover:shadow-md transition-all">
            <button onclick="toggleFaq(this)" data-index="{{ $i }}" class="w-full flex items-center justify-between p-5 text-left">
                <span class="font-semibold text-slate-800 text-sm pr-4">{{ $faq['q'] }}</span>
                <svg id="faq-icon-{{ $i }}" class="w-5 h-5 text-slate-400 shrink-0 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="faq-content-{{ $i }}" class="px-5 pb-5 text-sm text-slate-500 leading-relaxed hidden">
                {{ $faq['a'] }}
            </div>
        </div>
        @endforeach
    </div>
</section>

@endsection

@push('scripts')
<script>
function toggleFaq(btn) {
    const index = btn.getAttribute('data-index');
    const content = document.getElementById('faq-content-' + index);
    const icon = document.getElementById('faq-icon-' + index);

    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}
</script>
@endpush
