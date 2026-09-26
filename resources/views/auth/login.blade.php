@extends('layouts.guest')

@section('title', 'Masuk ke Portal SIPRA-C4.5')

@section('content')
<div class="w-full max-w-md mx-auto my-auto space-y-6">

    <!-- Top Branding & Institutional Header -->
    <div class="text-center space-y-2">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-white border border-slate-200/90 shadow-2xs text-brand-600 mb-1">
            <i data-lucide="brain-circuit" class="w-6 h-6"></i>
        </div>
        <div class="space-y-1">
            <div class="flex items-center justify-center space-x-2">
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight font-heading">SIPRA-C4.5</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-brand-50 text-brand-700 border border-brand-200 font-mono">v1.0</span>
            </div>
            <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                Portal Prediksi Akademik Mahasiswa Berbasis Decision Tree C4.5
            </p>
        </div>
        <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 border border-slate-200 text-[11px] text-slate-600 font-medium">
            <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
            <span>UIN Raden Intan Lampung • FST</span>
        </div>
    </div>

    <!-- Main Login Card -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-5">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h2 class="text-base font-bold text-slate-900 font-heading">Masuk ke Akun</h2>
                <p class="text-xs text-slate-500">Gunakan Email, NPM, atau NIP terdaftar</p>
            </div>
            <button type="button" onclick="openHelpModal()" class="text-slate-400 hover:text-brand-700 p-1.5 rounded-lg hover:bg-slate-50 transition" title="Pusat Bantuan">
                <i data-lucide="help-circle" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Dynamic Demo Notification Toast -->
        <div id="demo-toast" class="hidden items-center justify-between p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs transition-all shadow-2xs">
            <div class="flex items-center space-x-2">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                <span id="demo-toast-text" class="font-semibold">Kredensial demo terisi otomatis!</span>
            </div>
            <button type="button" onclick="document.getElementById('demo-toast').classList.add('hidden')" class="text-emerald-600 hover:text-emerald-900">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>

        <!-- 1-Click Quick Demo Account Selector (Minimalist Style) -->
        <div class="p-3 rounded-lg bg-slate-50/80 border border-slate-200/70 space-y-2">
            <div class="flex items-center justify-between text-[11px]">
                <span class="font-bold text-slate-700 flex items-center space-x-1.5">
                    <i data-lucide="zap" class="w-3.5 h-3.5 text-amber-500"></i>
                    <span>Akun Demo Cepat</span>
                </span>
                <span class="text-[10px] text-slate-400">1-Klik Isi Form</span>
            </div>

            <div class="grid grid-cols-4 gap-1.5">
                <!-- Admin -->
                <button 
                    type="button" 
                    onclick="fillDemoAccount('admin')" 
                    id="demo-pill-admin"
                    class="demo-pill py-1.5 px-1 rounded-md bg-white hover:bg-slate-100/80 border border-slate-200 hover:border-slate-300 text-slate-700 text-[11px] font-semibold text-center transition shadow-2xs cursor-pointer"
                    title="Masuk sebagai Administrator"
                >
                    Admin
                </button>

                <!-- Kaprodi -->
                <button 
                    type="button" 
                    onclick="fillDemoAccount('kaprodi')" 
                    id="demo-pill-kaprodi"
                    class="demo-pill py-1.5 px-1 rounded-md bg-white hover:bg-slate-100/80 border border-slate-200 hover:border-slate-300 text-slate-700 text-[11px] font-semibold text-center transition shadow-2xs cursor-pointer"
                    title="Masuk sebagai Ketua Program Studi"
                >
                    Kaprodi
                </button>

                <!-- Dosen PA -->
                <button 
                    type="button" 
                    onclick="fillDemoAccount('dosen_pa')" 
                    id="demo-pill-dosen_pa"
                    class="demo-pill py-1.5 px-1 rounded-md bg-white hover:bg-slate-100/80 border border-slate-200 hover:border-slate-300 text-slate-700 text-[11px] font-semibold text-center transition shadow-2xs cursor-pointer"
                    title="Masuk sebagai Dosen PA"
                >
                    Dosen PA
                </button>

                <!-- Mahasiswa -->
                <button 
                    type="button" 
                    onclick="fillDemoAccount('mahasiswa')" 
                    id="demo-pill-mahasiswa"
                    class="demo-pill py-1.5 px-1 rounded-md bg-white hover:bg-slate-100/80 border border-slate-200 hover:border-slate-300 text-slate-700 text-[11px] font-semibold text-center transition shadow-2xs cursor-pointer"
                    title="Masuk sebagai Mahasiswa (2271020052)"
                >
                    Mahasiswa
                </button>
            </div>
        </div>

        <!-- Flash Server Alerts -->
        @if(session('error'))
            <div class="p-3.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start space-x-2.5">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 flex-shrink-0 mt-0.5"></i>
                <div class="leading-relaxed font-medium">{{ session('error') }}</div>
            </div>
        @endif

        @if(session('success'))
            <div class="p-3.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-start space-x-2.5">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                <div class="leading-relaxed font-medium">{{ session('success') }}</div>
            </div>
        @endif

        <!-- Form Authentication -->
        <form method="POST" action="{{ route('login.post') }}" id="loginForm" class="space-y-4">
            @csrf

            <!-- Identifier Input -->
            <div class="space-y-1.5">
                <label for="login_identifier" class="block text-xs font-semibold text-slate-700">
                    Email / NPM / NIP <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
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
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('login_identifier') border-rose-300 bg-rose-50/30 @else border-slate-200 @enderror rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition shadow-2xs"
                    >
                </div>
                @error('login_identifier')
                    <p class="text-[11px] text-rose-600 font-medium flex items-center space-x-1 mt-1">
                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5 flex-shrink-0"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <!-- Password Input with Toggle -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-xs font-semibold text-slate-700">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </div>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        placeholder="••••••••"
                        class="w-full pl-10 pr-10 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('password') border-rose-300 bg-rose-50/30 @else border-slate-200 @enderror rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition shadow-2xs font-mono"
                    >
                    <button 
                        type="button" 
                        id="togglePasswordBtn"
                        onclick="togglePasswordVisibility()" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none transition cursor-pointer"
                        title="Tampilkan / Sembunyikan Kata Sandi"
                    >
                        <i id="passwordEyeIcon" data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-[11px] text-rose-600 font-medium flex items-center space-x-1 mt-1">
                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5 flex-shrink-0"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center space-x-2 text-xs text-slate-600 cursor-pointer select-none">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        id="remember" 
                        class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500/30 transition cursor-pointer"
                    >
                    <span>Ingat saya</span>
                </label>

                <button type="button" onclick="openHelpModal()" class="text-xs font-semibold text-brand-700 hover:text-brand-800 hover:underline cursor-pointer">
                    Bantuan masuk?
                </button>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                id="submitBtn"
                class="w-full py-2.5 px-4 rounded-lg bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-xs hover:shadow-sm transition flex items-center justify-center space-x-2 cursor-pointer mt-2"
            >
                <span id="submitBtnText">Masuk ke Sistem</span>
                <i id="submitBtnIcon" data-lucide="arrow-right" class="w-4 h-4"></i>
                <svg id="submitBtnSpinner" class="hidden animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </form>

    </div>

    <!-- Minimalist Footer / Academic Credits -->
    <div class="text-center space-y-1 pt-1 text-slate-500 text-xs">
        <p class="font-medium">
            Penelitian Skripsi: <strong class="text-slate-700">Pingky Hera Veliyanti</strong> <span class="font-mono text-[11px]">(NPM: 2271020052)</span>
        </p>
        <p class="text-[11px] text-slate-400">
            Program Studi Sistem Informasi • Fakultas Sains dan Teknologi
        </p>
        <p class="text-[11px] text-slate-400">
            © {{ date('Y') }} <strong>SIPRA-C4.5</strong> • UIN Raden Intan Lampung
        </p>
    </div>

</div>

<!-- Minimalist Help Modal -->
<div id="helpModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 backdrop-blur-2xs p-4 transition-opacity">
    <div class="bg-white rounded-xl max-w-md w-full p-6 space-y-4 border border-slate-200 shadow-xl relative">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center font-bold text-xs">
                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Bantuan Akses Portal</h3>
                    <p class="text-[11px] text-slate-500">Panduan login SIPRA-C4.5 UIN RIL</p>
                </div>
            </div>
            <button type="button" onclick="closeHelpModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="space-y-3 text-xs text-slate-600 leading-relaxed">
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1.5">
                <p class="font-bold text-slate-800">Format Kredensial Masuk:</p>
                <ul class="list-disc list-inside space-y-1 text-slate-600 pl-1">
                    <li><strong>Dosen PA & Kaprodi</strong>: Menggunakan NIP resmi atau email <code>@uinril.ac.id</code>.</li>
                    <li><strong>Mahasiswa</strong>: Menggunakan NPM aktif (contoh: <code>2271020052</code>) atau email student.</li>
                    <li><strong>Admin</strong>: Menggunakan akun berwenang pengelola sistem.</li>
                </ul>
            </div>

            <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1">
                <p class="font-bold text-slate-800">Kendala Akun atau Lupa Sandi:</p>
                <p class="text-slate-600">
                    Silakan hubungi Administrator Akademik Program Studi Sistem Informasi FST UIN Raden Intan Lampung untuk verifikasi identitas dan reset kata sandi akun Anda.
                </p>
            </div>
        </div>

        <div class="pt-2 flex justify-end">
            <button type="button" onclick="closeHelpModal()" class="px-3.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    // Demo account definitions
    const demoAccounts = {
        admin: { id: 'admin@uinril.ac.id', pass: 'admin123', label: 'Administrator' },
        kaprodi: { id: 'kaprodi@uinril.ac.id', pass: 'prodi123', label: 'Ketua Prodi' },
        dosen_pa: { id: 'dosenpa@uinril.ac.id', pass: 'dosen123', label: 'Dosen PA' },
        mahasiswa: { id: '2271020052', pass: 'password', label: 'Mahasiswa (Pingky)' }
    };

    function fillDemoAccount(role) {
        const acc = demoAccounts[role];
        if (!acc) return;

        const idInput = document.getElementById('login_identifier');
        const passInput = document.getElementById('password');

        idInput.value = acc.id;
        passInput.value = acc.pass;

        // Reset all pills
        document.querySelectorAll('.demo-pill').forEach(pill => {
            pill.classList.remove('bg-brand-50', 'border-brand-500', 'text-brand-800', 'ring-2', 'ring-brand-500/20');
            pill.classList.add('bg-white', 'border-slate-200', 'text-slate-700');
        });

        // Highlight selected pill
        const activePill = document.getElementById('demo-pill-' + role);
        if (activePill) {
            activePill.classList.remove('bg-white', 'border-slate-200', 'text-slate-700');
            activePill.classList.add('bg-brand-50', 'border-brand-500', 'text-brand-800', 'ring-2', 'ring-brand-500/20');
        }

        // Show toast
        const toast = document.getElementById('demo-toast');
        const toastText = document.getElementById('demo-toast-text');
        if (toast && toastText) {
            toastText.textContent = `Kredensial ${acc.label} terisi otomatis!`;
            toast.classList.remove('hidden');
            toast.classList.add('flex');
            setTimeout(() => {
                toast.classList.add('hidden');
                toast.classList.remove('flex');
            }, 3000);
        }

        passInput.focus();
    }

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

    // Submit loading state
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const btnText = document.getElementById('submitBtnText');
        const btnIcon = document.getElementById('submitBtnIcon');
        const btnSpinner = document.getElementById('submitBtnSpinner');

        btn.disabled = true;
        btn.classList.add('opacity-80', 'cursor-not-allowed');
        btnText.textContent = 'Memverifikasi...';
        btnIcon.classList.add('hidden');
        btnSpinner.classList.remove('hidden');
    });

    // Help Modal
    function openHelpModal() {
        const modal = document.getElementById('helpModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeHelpModal() {
        const modal = document.getElementById('helpModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Close on outside click
    document.getElementById('helpModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeHelpModal();
        }
    });
</script>
@endsection
