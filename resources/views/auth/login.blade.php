<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — Toko Amycell</title>
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
            <div class="absolute top-20 left-10 w-72 h-72 bg-sky-500/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay:1s"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-sky-400/5 rounded-full blur-3xl"></div>
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
            <div class="text-8xl mb-6">🛒</div>
            <h2 class="text-4xl font-black text-white leading-tight mb-4">
                Selamat Datang<br>
                <span class="bg-linear-to-r from-sky-400 to-indigo-400 bg-clip-text text-transparent">Kembali!</span>
            </h2>
            <p class="text-slate-400 text-lg leading-relaxed max-w-sm mx-auto">
                Masuk untuk melanjutkan belanja pulsa, paket data, dan produk digital favoritmu.
            </p>

            {{-- Feature badges --}}
            <div class="flex flex-wrap justify-center gap-3 mt-8">
                <div class="flex items-center gap-2 bg-white/5 border border-white/10 rounded-full px-4 py-2 backdrop-blur-sm">
                    <span class="text-base">⚡</span>
                    <span class="text-xs text-slate-300 font-medium">Proses Instan</span>
                </div>
                <div class="flex items-center gap-2 bg-white/5 border border-white/10 rounded-full px-4 py-2 backdrop-blur-sm">
                    <span class="text-base">🔒</span>
                    <span class="text-xs text-slate-300 font-medium">100% Aman</span>
                </div>
                <div class="flex items-center gap-2 bg-white/5 border border-white/10 rounded-full px-4 py-2 backdrop-blur-sm">
                    <span class="text-base">💰</span>
                    <span class="text-xs text-slate-300 font-medium">Harga Terbaik</span>
                </div>
            </div>
        </div>

        {{-- Bottom --}}
        <div class="relative z-10 text-center">
            <p class="text-slate-600 text-xs">© {{ date('Y') }} Toko Amycell. Hak Cipta Dilindungi.</p>
        </div>
    </div>

    {{-- Right Panel — Form --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-md">

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
                <h1 class="text-3xl font-black text-slate-800">Masuk ke Akun</h1>
                <p class="text-slate-500 mt-2 text-sm">Belum punya akun?
                    <a href="{{ route('register') }}" class="text-sky-600 font-semibold hover:text-indigo-600 transition-colors">Daftar sekarang</a>
                </p>
            </div>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="mb-5 flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-700 text-sm">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('status') }}
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

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
                            autofocus
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
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-semibold text-slate-600 uppercase tracking-wide">
                            Password
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-sky-600 hover:text-indigo-600 font-medium transition-colors">
                                Lupa password?
                            </a>
                        @endif
                    </div>
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
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full pl-11 pr-12 py-3 bg-white border {{ $errors->has('password') ? 'border-red-400 focus:ring-red-400' : 'border-slate-200 focus:ring-sky-500 focus:border-sky-500' }} rounded-2xl text-sm text-slate-800 placeholder-slate-400 focus:ring-2 outline-none transition-all shadow-sm"
                        >
                        <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                            <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center gap-3">
                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                        class="w-4 h-4 rounded border-slate-300 text-sky-500 accent-sky-500 focus:ring-sky-500 cursor-pointer"
                    >
                    <label for="remember_me" class="text-sm text-slate-600 cursor-pointer select-none">
                        Ingat saya di perangkat ini
                    </label>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full py-3.5 bg-linear-to-r from-sky-500 to-indigo-600 text-white font-bold rounded-2xl hover:shadow-xl hover:shadow-sky-200 transition-all hover:-translate-y-0.5 text-sm tracking-wide"
                >
                    🔐 Masuk ke Akun
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-4 my-6">
                <div class="flex-1 h-px bg-slate-200"></div>
                <span class="text-xs text-slate-400 font-medium">atau</span>
                <div class="flex-1 h-px bg-slate-200"></div>
            </div>

            {{-- Register CTA --}}
            <div class="text-center">
                <p class="text-sm text-slate-500 mb-3">Belum punya akun Amycell?</p>
                <a href="{{ route('register') }}"
                   class="block w-full py-3 border-2 border-slate-200 text-slate-700 font-semibold rounded-2xl hover:border-sky-400 hover:text-sky-600 hover:bg-sky-50 transition-all text-sm">
                    Daftar Gratis Sekarang →
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
const toggleBtn = document.getElementById('toggle-password');
const passwordInput = document.getElementById('password');
const eyeIcon = document.getElementById('eye-icon');

toggleBtn.addEventListener('click', () => {
    const isPassword = passwordInput.type === 'password';
    passwordInput.type = isPassword ? 'text' : 'password';
    eyeIcon.innerHTML = isPassword
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>'
        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
});
</script>

</body>
</html>
