@extends('customer.layouts.app')

@section('title', 'Ajukan Servis HP')

@section('content')

<section class="max-w-3xl mx-auto px-4 py-10">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('customer.service') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-orange-600 transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Servis Saya
        </a>
        <h1 class="text-3xl font-black text-slate-800">🔧 Ajukan Servis HP</h1>
        <p class="text-slate-500 mt-1">Isi form berikut dengan lengkap untuk mempercepat proses servis</p>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4">
            <p class="text-sm font-semibold text-red-700 mb-2">Mohon perbaiki kesalahan berikut:</p>
            <ul class="list-disc pl-5 text-xs text-red-600 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('customer.service.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Section 1: Device Info --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-linear-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center text-lg shadow-md">📱</div>
                <div>
                    <h2 class="font-bold text-slate-800">Informasi Perangkat</h2>
                    <p class="text-xs text-slate-500">Detail HP yang perlu diperbaiki</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Merk HP <span class="text-red-500">*</span></label>
                    <select name="device_brand" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400 transition-all">
                        <option value="">Pilih merk...</option>
                        @foreach(['Samsung', 'Apple (iPhone)', 'Xiaomi', 'OPPO', 'Vivo', 'Realme', 'Huawei', 'OnePlus', 'Asus', 'Sony', 'Nokia', 'Infinix', 'Tecno', 'Poco', 'Lainnya'] as $brand)
                            <option value="{{ $brand }}" {{ old('device_brand') === $brand ? 'selected' : '' }}>{{ $brand }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Model HP <span class="text-red-500">*</span></label>
                    <input type="text" name="device_model" value="{{ old('device_model') }}" required placeholder="Contoh: Galaxy S24 Ultra" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Warna HP</label>
                    <input type="text" name="device_color" value="{{ old('device_color') }}" placeholder="Contoh: Phantom Black" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor IMEI <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <input type="text" name="device_imei" value="{{ old('device_imei') }}" placeholder="*#06# untuk cek IMEI" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400 transition-all">
                </div>
            </div>
        </div>

        {{-- Section 2: Damage Info --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-linear-to-br from-red-400 to-pink-500 rounded-xl flex items-center justify-center text-lg shadow-md">⚠️</div>
                <div>
                    <h2 class="font-bold text-slate-800">Detail Kerusakan</h2>
                    <p class="text-xs text-slate-500">Jelaskan masalah yang dialami</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Kerusakan <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @php
                            $damageTypes = [
                                'lcd' => ['📱', 'LCD / Layar'],
                                'battery' => ['🔋', 'Baterai'],
                                'charging_port' => ['🔌', 'Port Charging'],
                                'software' => ['💻', 'Software'],
                                'water_damage' => ['💧', 'Water Damage'],
                                'speaker' => ['🔊', 'Speaker/Audio'],
                                'camera' => ['📷', 'Kamera'],
                                'button' => ['⚙️', 'Tombol'],
                                'other' => ['🔧', 'Lainnya'],
                            ];
                        @endphp
                        @foreach($damageTypes as $key => $dt)
                            <label class="flex items-center gap-2 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-orange-50 hover:border-orange-300 transition-all has-checked:bg-orange-50 has-checked:border-orange-400 has-checked:shadow-sm">
                                <input type="radio" name="damage_type" value="{{ $key }}" {{ old('damage_type') === $key ? 'checked' : '' }} required class="sr-only">
                                <span class="text-lg">{{ $dt[0] }}</span>
                                <span class="text-xs font-medium text-slate-700">{{ $dt[1] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi Kerusakan <span class="text-red-500">*</span></label>
                    <textarea name="damage_description" required rows="4" placeholder="Jelaskan detail kerusakan yang dialami..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400 transition-all resize-none">{{ old('damage_description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Foto Kondisi HP <span class="text-slate-400 font-normal">(opsional, max 5MB)</span></label>
                    <div class="relative">
                        <input type="file" name="device_image" accept="image/*" id="device-image-input" onchange="previewImage(event)" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm file:mr-3 file:px-3 file:py-1 file:bg-orange-100 file:text-orange-700 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:cursor-pointer hover:file:bg-orange-200 transition-all">
                    </div>
                    <div id="image-preview" class="mt-3 hidden">
                        <img id="preview-img" src="" alt="Preview" class="w-40 h-40 object-cover rounded-xl border border-slate-200">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Contact Info --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-linear-to-br from-sky-400 to-blue-500 rounded-xl flex items-center justify-center text-lg shadow-md">👤</div>
                <div>
                    <h2 class="font-bold text-slate-800">Informasi Kontak</h2>
                    <p class="text-xs text-slate-500">Agar kami bisa menghubungi kamu</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="contact_name" value="{{ old('contact_name', auth()->user()->name) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">No. HP Aktif <span class="text-red-500">*</span></label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', auth()->user()->phone) }}" required placeholder="08xxxxxxxxxx" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400 transition-all">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <textarea name="contact_address" rows="2" placeholder="Alamat lengkap..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400 transition-all resize-none">{{ old('contact_address', auth()->user()->address) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('customer.service') }}" class="text-sm text-slate-500 hover:text-slate-700 transition-colors">← Batal</a>
            <button type="submit" class="px-8 py-3 bg-linear-to-r from-orange-500 to-red-600 text-white font-bold rounded-xl hover:shadow-xl hover:shadow-orange-200 transition-all hover:-translate-y-0.5 text-sm">
                📤 Kirim Permintaan Servis
            </button>
        </div>
    </form>

</section>

@endsection

@push('scripts')
<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}

document.querySelectorAll('input[name="damage_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('input[name="damage_type"]').forEach(r => {
            const label = r.closest('label');
            if (r.checked) {
                label.classList.add('bg-orange-50', 'border-orange-400', 'shadow-sm');
                label.classList.remove('bg-slate-50', 'border-slate-200');
            } else {
                label.classList.remove('bg-orange-50', 'border-orange-400', 'shadow-sm');
                label.classList.add('bg-slate-50', 'border-slate-200');
            }
        });
    });
});
</script>
@endpush
