<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar — Toko Amycell</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="font-[Inter] antialiased bg-slate-50 min-h-screen">

<div class="min-h-screen flex">

    {{-- Left Panel — Branding --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-linear-to-br from-slate-900 via-sky-950 to-indigo-950 flex-col justify-between p-12">

        {{-- Background decorations --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 right-10 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 left-10 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay:1.5s"></div>
            <div class="absolute top-1/3 left-1/2 -translate-x-1/2 w-[400px] h-[400px] bg-sky-400/5 rounded-full blur-3xl"></div>
        </div>

        {{-- Top Logo --}}
        <div class="relative z-10">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group w-fit">
                <div class="w-10 h-10 bg-linear-to-br from-sky-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                    <span class="text-white font-bold text-sm">AC</span>
                </div>
                <div>
                    <span class="font-bold text-white text-xl leading-none">Amycell</span>
                    <p class="text-[11px] text-sky-400 font-medium mt-0.5">Toko Digital Terpercaya</p>
                </div>
            </a>
        </div>

        {{-- Center Content --}}
        <div class="relative z-10 text-center">
            <div class="text-8xl mb-6">🎉</div>
            <h2 class="text-4xl font-black text-white leading-tight mb-4">
                Bergabung dengan<br>
                <span class="bg-linear-to-r from-sky-400 to-indigo-400 bg-clip-text text-transparent">Amycell!</span>
            </h2>
            <p class="text-slate-400 text-lg leading-relaxed max-w-sm mx-auto">
                Daftar gratis dan nikmati kemudahan belanja pulsa, paket data, token listrik, dan ribuan produk digital lainnya.
            </p>

            {{-- Benefits --}}
            <div class="mt-8 space-y-3 text-left max-w-xs mx-auto">
                @foreach([
                    ['icon' => '⚡', 'text' => 'Proses instan setelah pembayaran'],
                    ['icon' => '💰', 'text' => 'Harga terbaik & update setiap hari'],
                    ['icon' => '🛡️', 'text' => 'Transaksi aman & terjamin'],
                    ['icon' => '💬', 'text' => 'Live chat CS 24/7 siap membantu'],
                ] as $benefit)
                <div class="flex items-center gap-3 bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 backdrop-blur-sm">
                    <span class="text-lg">{{ $benefit['icon'] }}</span>
                    <span class="text-sm text-slate-300">{{ $benefit['text'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Bottom --}}
        <div class="relative z-10 text-center">
            <p class="text-slate-600 text-xs">© {{ date('Y') }} Toko Amycell. Hak Cipta Dilindungi.</p>
        </div>
    </div>

    {{-- Right Panel — Form --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 overflow-y-auto">
        <div class="w-full max-w-md py-6">

            {{-- Mobile Logo --}}
            <div class="flex justify-center mb-8 lg:hidden">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <div class="w-10 h-10 bg-linear-to-br from-sky-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-sm">AC</span>
                    </div>
                    <div>
                        <span class="font-bold text-slate-800 text-xl leading-none">Amycell</span>
                        <p class="text-[11px] text-sky-500 font-medium mt-0.5">Toko Digital Terpercaya</p>
                    </div>
                </a>
            </div>

            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-black text-slate-800">Buat Akun Baru</h1>
                <p class="text-slate-500 mt-2 text-sm">Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-sky-600 font-semibold hover:text-indigo-600 transition-colors">Masuk di sini</a>
                </p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-2">
                        Nama Lengkap
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Nama lengkap kamu"
                            class="w-full pl-11 pr-4 py-3 bg-white border {{ $errors->has('name') ? 'border-red-400 focus:ring-red-400' : 'border-slate-200 focus:ring-sky-500 focus:border-sky-500' }} rounded-2xl text-sm text-slate-800 placeholder-slate-400 focus:ring-2 outline-none transition-all shadow-sm"
                        >
                    </div>
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-2">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="username"
                            placeholder="nama@email.com"
                            class="w-full pl-11 pr-4 py-3 bg-white border {{ $errors->has('email') ? 'border-red-400 focus:ring-red-400' : 'border-slate-200 focus:ring-sky-500 focus:border-sky-500' }} rounded-2xl text-sm text-slate-800 placeholder-slate-400 focus:ring-2 outline-none transition-all shadow-sm"
                        >
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="Min. 8 karakter"
                            class="w-full pl-11 pr-12 py-3 bg-white border {{ $errors->has('password') ? 'border-red-400 focus:ring-red-400' : 'border-slate-200 focus:ring-sky-500 focus:border-sky-500' }} rounded-2xl text-sm text-slate-800 placeholder-slate-400 focus:ring-2 outline-none transition-all shadow-sm"
                        >
                        <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                            <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    {{-- Password strength indicator --}}
                    <div id="password-strength" class="mt-2 hidden">
                        <div class="flex gap-1 mb-1">
                            <div id="bar-1" class="h-1 flex-1 rounded-full bg-slate-200 transition-colors"></div>
                            <div id="bar-2" class="h-1 flex-1 rounded-full bg-slate-200 transition-colors"></div>
                            <div id="bar-3" class="h-1 flex-1 rounded-full bg-slate-200 transition-colors"></div>
                            <div id="bar-4" class="h-1 flex-1 rounded-full bg-slate-200 transition-colors"></div>
                        </div>
                        <p id="strength-text" class="text-[11px] text-slate-400"></p>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-2">
                        Konfirmasi Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Ulangi password"
                            class="w-full pl-11 pr-12 py-3 bg-white border {{ $errors->has('password_confirmation') ? 'border-red-400 focus:ring-red-400' : 'border-slate-200 focus:ring-sky-500 focus:border-sky-500' }} rounded-2xl text-sm text-slate-800 placeholder-slate-400 focus:ring-2 outline-none transition-all shadow-sm"
                        >
                        <button type="button" id="toggle-confirm" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                            <svg id="eye-icon-confirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    {{-- Match indicator --}}
                    <p id="match-indicator" class="mt-1.5 text-[11px] hidden"></p>
                    @error('password_confirmation')
                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Terms --}}
                <div class="flex items-start gap-3 p-4 bg-sky-50 border border-sky-100 rounded-2xl">
                    <svg class="w-5 h-5 text-sky-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-sky-700 leading-relaxed">
                        Dengan mendaftar, kamu menyetujui <span class="font-semibold">Syarat & Ketentuan</span> serta <span class="font-semibold">Kebijakan Privasi</span> Toko Amycell.
                    </p>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full py-3.5 bg-linear-to-r from-sky-500 to-indigo-600 text-white font-bold rounded-2xl hover:shadow-xl hover:shadow-sky-200 transition-all hover:-translate-y-0.5 text-sm tracking-wide"
                >
                    🎉 Buat Akun Sekarang
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-4 my-6">
                <div class="flex-1 h-px bg-slate-200"></div>
                <span class="text-xs text-slate-400 font-medium">atau</span>
                <div class="flex-1 h-px bg-slate-200"></div>
            </div>

            {{-- Login CTA --}}
            <div class="text-center">
                <p class="text-sm text-slate-500 mb-3">Sudah punya akun Amycell?</p>
                <a href="{{ route('login') }}"
                   class="block w-full py-3 border-2 border-slate-200 text-slate-700 font-semibold rounded-2xl hover:border-sky-400 hover:text-sky-600 hover:bg-sky-50 transition-all text-sm">
                    ← Masuk ke Akun
                </a>
            </div>

            {{-- Back to home --}}
            <div class="text-center mt-6">
                <a href="{{ route('home') }}" class="text-xs text-slate-400 hover:text-sky-600 transition-colors flex items-center justify-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Beranda
                </a>
            </div>

        </div>
    </div>
</div>

<script>
// Toggle password visibility
function makeToggle(btnId, inputId, iconId) {
    const btn = document.getElementById(btnId);
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!btn) return;
    btn.addEventListener('click', () => {
        const isPass = input.type === 'password';
        input.type = isPass ? 'text' : 'password';
        icon.innerHTML = isPass
            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>'
            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    });
}
makeToggle('toggle-password', 'password', 'eye-icon');
makeToggle('toggle-confirm', 'password_confirmation', 'eye-icon-confirm');

// Password strength meter
const passwordInput = document.getElementById('password');
const strengthDiv = document.getElementById('password-strength');
const bars = [document.getElementById('bar-1'), document.getElementById('bar-2'), document.getElementById('bar-3'), document.getElementById('bar-4')];
const strengthText = document.getElementById('strength-text');

passwordInput.addEventListener('input', () => {
    const val = passwordInput.value;
    if (!val) { strengthDiv.classList.add('hidden'); return; }
    strengthDiv.classList.remove('hidden');

    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const colors = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-emerald-400'];
    const labels = ['Sangat Lemah', 'Lemah', 'Cukup Kuat', 'Kuat'];
    const textColors = ['text-red-500', 'text-orange-500', 'text-yellow-600', 'text-emerald-600'];

    bars.forEach((bar, i) => {
        bar.className = `h-1 flex-1 rounded-full transition-colors ${i < score ? colors[score - 1] : 'bg-slate-200'}`;
    });
    strengthText.textContent = labels[score - 1] || '';
    strengthText.className = `text-[11px] ${textColors[score - 1] || 'text-slate-400'}`;
});

// Password match indicator
const confirmInput = document.getElementById('password_confirmation');
const matchIndicator = document.getElementById('match-indicator');

confirmInput.addEventListener('input', () => {
    if (!confirmInput.value) { matchIndicator.classList.add('hidden'); return; }
    matchIndicator.classList.remove('hidden');
    if (passwordInput.value === confirmInput.value) {
        matchIndicator.textContent = '✓ Password cocok';
        matchIndicator.className = 'mt-1.5 text-[11px] text-emerald-600';
    } else {
        matchIndicator.textContent = '✗ Password tidak cocok';
        matchIndicator.className = 'mt-1.5 text-[11px] text-red-500';
    }
});
</script>

</body>
</html>
