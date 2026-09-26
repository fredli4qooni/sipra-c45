@extends('layouts.guest')

@section('title', 'Masuk ke Sistem')

@section('content')
<div class="space-y-6">

    <!-- Header Logo & Branding -->
    <div class="text-center space-y-2">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-brand-50 border border-brand-200 text-brand-600 shadow-xs mb-1">
            <i data-lucide="brain-circuit" class="w-6 h-6"></i>
        </div>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">SIPRA-C4.5</h2>
        <p class="text-xs text-slate-500 max-w-xs mx-auto leading-relaxed">
            Portal Prediksi Akademik Mahasiswa Berbasis Decision Tree C4.5
        </p>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-50 text-brand-700 border border-brand-200">
            UIN Raden Intan Lampung
        </span>
    </div>

    <!-- Main Login Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
        
        <div class="border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Masuk ke Akun</h3>
            <p class="text-xs text-slate-500">Gunakan Email, NPM, atau NIP terdaftar</p>
        </div>

        @if(session('error'))
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start space-x-2.5">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 flex-shrink-0 mt-0.5"></i>
                <div class="leading-relaxed">{{ session('error') }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf

            <!-- Identifier Input -->
            <div class="space-y-1.5">
                <label for="login_identifier" class="block text-xs font-semibold text-slate-700">
                    Email / NPM / NIP <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </div>
                    <input 
                        type="text" 
                        name="login_identifier" 
                        id="login_identifier" 
                        value="{{ old('login_identifier') }}" 
                        required 
                        autofocus
                        placeholder="contoh: admin@uinril.ac.id atau 2271020052"
                        class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                    >
                </div>
                @error('login_identifier')
                    <p class="text-[11px] text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Input -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-xs font-semibold text-slate-700">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </div>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        placeholder="••••••••"
                        class="w-full pl-9 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                    >
                    <button 
                        type="button" 
                        id="togglePasswordBtn"
                        onclick="togglePasswordVisibility()" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none transition"
                        title="Tampilkan / Sembunyikan Kata Sandi"
                    >
                        <i id="passwordEyeIcon" data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-[11px] text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                class="w-full py-2.5 px-4 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-xs hover:shadow-sm transition flex items-center justify-center space-x-2"
            >
                <span>Masuk Sekarang</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </form>

    </div>

</div>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('passwordEyeIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.setAttribute('data-lucide', 'eye-off');
        } else {
            passwordInput.type = 'password';
            eyeIcon.setAttribute('data-lucide', 'eye');
        }
        
        if (window.lucide) {
            lucide.createIcons();
        }
    }
</script>
@endsection
