@extends('layouts.guest')

@section('title', 'Masuk ke Portal SIPRA-C4.5')

@section('content')
<div class="min-h-screen lg:grid lg:grid-cols-12 bg-slate-950 font-sans">

    <!-- ========================================== -->
    <!-- LEFT PANEL: Brand, AI Engine & Research Showcase -->
    <!-- ========================================== -->
    <div class="relative hidden lg:flex lg:col-span-5 xl:col-span-6 bg-gradient-to-br from-slate-950 via-[#031d16] to-slate-900 text-white p-8 sm:p-12 xl:p-16 flex-col justify-between overflow-hidden border-r border-slate-800/80">
        
        <!-- Ambient Glowing Background Blobs -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-500/20 rounded-full blur-[120px] pointer-events-none animate-pulse-slow"></div>
        <div class="absolute bottom-10 right-0 w-80 h-80 bg-teal-500/15 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute inset-0 opacity-15 pointer-events-none" style="background-image: radial-gradient(rgba(16, 185, 129, 0.4) 1px, transparent 1px); background-size: 28px 28px;"></div>

        <!-- Top Header & University Identity -->
        <div class="relative z-10 space-y-4">
            <div class="flex items-center space-x-3.5">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-brand-600 to-emerald-400 p-0.5 shadow-lg shadow-emerald-500/25 flex items-center justify-center flex-shrink-0">
                    <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center text-emerald-400">
                        <i data-lucide="brain-circuit" class="w-6 h-6"></i>
                    </div>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h1 class="text-xl font-extrabold text-white tracking-tight font-heading">SIPRA-C4.5</h1>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">v1.0 Release</span>
                    </div>
                    <p class="text-xs text-slate-300 font-medium">UIN Raden Intan Lampung</p>
                </div>
            </div>

            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-slate-900/80 border border-emerald-500/30 backdrop-blur-md text-[11px] text-emerald-300">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span>Fakultas Sains dan Teknologi • Program Studi Sistem Informasi</span>
            </div>
        </div>

        <!-- Center Showcase Content -->
        <div class="relative z-10 my-auto py-8 space-y-6 max-w-xl">
            <div class="space-y-3">
                <h2 class="text-3xl xl:text-4xl font-black text-white tracking-tight leading-tight font-heading">
                    Deteksi Dini Risiko Akademik Mahasiswa <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-emerald-200">Secara Saintifik</span>
                </h2>
                <p class="text-xs xl:text-sm text-slate-300 leading-relaxed">
                    Sistem pendukung keputusan cerdas berbasis algoritma Data Mining <strong>Decision Tree C4.5</strong> untuk memprediksi potensi keterlambatan studi dan mendukung intervensi bimbingan akademik terarah.
                </p>
            </div>

            <!-- Bento Feature Glass Cards -->
            <div class="space-y-3 pt-2">
                <!-- Card 1 -->
                <div class="p-3.5 rounded-xl bg-slate-900/70 border border-slate-700/60 backdrop-blur-md hover:border-emerald-500/40 transition flex items-start space-x-3.5 shadow-sm">
                    <div class="w-9 h-9 rounded-lg bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i data-lucide="git-fork" class="w-4 h-4"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-white">Algoritma C4.5 Terintegrasi</h4>
                            <span class="text-[10px] font-semibold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">Entropy & Gain Ratio</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-0.5 leading-normal">
                            Menghitung bobot atribut (IPK, IPS, Kehadiran, SKS, Cuti) secara otomatis untuk menghasilkan aturan klasifikasi transparan.
                        </p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="p-3.5 rounded-xl bg-slate-900/70 border border-slate-700/60 backdrop-blur-md hover:border-amber-500/40 transition flex items-start space-x-3.5 shadow-sm">
                    <div class="w-9 h-9 rounded-lg bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i data-lucide="bell-ring" class="w-4 h-4"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-white">Early Warning System (EWS)</h4>
                            <span class="text-[10px] font-semibold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">Intervensi PA</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-0.5 leading-normal">
                            Kategorisasi risiko otomatis (Tinggi, Sedang, Rendah) dengan alur bimbingan dan pencatatan intervensi konseling dosen.
                        </p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="p-3.5 rounded-xl bg-slate-900/70 border border-slate-700/60 backdrop-blur-md hover:border-teal-500/40 transition flex items-start space-x-3.5 shadow-sm">
                    <div class="w-9 h-9 rounded-lg bg-teal-500/15 border border-teal-500/30 text-teal-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-white">Explainable AI (XAI) Decision Path</h4>
                            <span class="text-[10px] font-semibold text-teal-400 bg-teal-500/10 px-2 py-0.5 rounded border border-teal-500/20">Transparan</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-0.5 leading-normal">
                            Setiap hasil prediksi dapat dilacak alur pohon keputusannya langkah demi langkah secara visual dan naratif.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Left Panel Footer: Academic Research Metadata -->
        <div class="relative z-10 pt-6 border-t border-slate-800/80 flex items-center justify-between text-slate-400 text-xs">
            <div class="space-y-0.5">
                <p class="text-[11px] font-bold text-slate-200">Penelitian Skripsi:</p>
                <p class="text-[12px] font-semibold text-emerald-400">Pingky Hera Veliyanti <span class="text-slate-400 font-mono">(NPM: 2271020052)</span></p>
            </div>
            <div class="text-right text-[11px]">
                <p class="text-slate-300 font-medium">Sistem Informasi</p>
                <p class="text-slate-500 font-mono">FST UIN RIL © {{ date('Y') }}</p>
            </div>
        </div>

    </div>


    <!-- ========================================== -->
    <!-- RIGHT PANEL: Sleek Modern Authentication Hub -->
    <!-- ========================================== -->
    <div class="lg:col-span-7 xl:col-span-6 bg-[#f8fafc] flex flex-col justify-between p-6 sm:p-10 lg:p-12 xl:p-16 min-h-screen">
        
        <!-- Mobile Header (Visible only on smaller screens) -->
        <div class="lg:hidden flex items-center justify-between pb-6 border-b border-slate-200/80 mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-brand-600 text-white flex items-center justify-center font-bold shadow-xs">
                    <i data-lucide="brain-circuit" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 tracking-tight font-heading">SIPRA-C4.5</h2>
                    <p class="text-[10px] text-slate-500">UIN Raden Intan Lampung</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-brand-50 text-brand-700 border border-brand-200">
                Portal Masuk
            </span>
        </div>

        <!-- Top Status Bar (Desktop) -->
        <div class="hidden lg:flex items-center justify-between text-xs text-slate-500 mb-6">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="font-medium text-slate-600">Sistem Berjalan Normal</span>
            </div>
            <button type="button" onclick="openHelpModal()" class="inline-flex items-center space-x-1.5 text-slate-600 hover:text-brand-700 font-medium transition text-xs">
                <i data-lucide="help-circle" class="w-3.5 h-3.5"></i>
                <span>Panduan & Bantuan</span>
            </button>
        </div>

        <!-- Center Login Container -->
        <div class="w-full max-w-md mx-auto my-auto space-y-6">

            <!-- Title & Welcome Message -->
            <div class="space-y-1.5 text-center sm:text-left">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-brand-50 text-brand-700 border border-brand-200/80 mb-1 shadow-2xs">
                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight font-heading">Masuk ke Akun Anda</h3>
                <p class="text-xs text-slate-500">
                    Portal Prediksi Akademik Mahasiswa Berbasis Decision Tree C4.5. Silakan autentikasi menggunakan akun terdaftar atau gunakan opsi demo di bawah.
                </p>
            </div>

            <!-- Demo Notification Toast (Dynamic) -->
            <div id="demo-toast" class="hidden items-center justify-between p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs transition-all shadow-xs">
                <div class="flex items-center space-x-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                    <span id="demo-toast-text" class="font-semibold">Kredensial demo terisi otomatis!</span>
                </div>
                <button type="button" onclick="document.getElementById('demo-toast').classList.add('hidden')" class="text-emerald-600 hover:text-emerald-900">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>

            <!-- 1-Click Quick Demo Account Selector (Sidang / Demo Helper) -->
            <div class="p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-xs space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider flex items-center space-x-1.5">
                        <i data-lucide="zap" class="w-3.5 h-3.5 text-amber-500"></i>
                        <span>Pilih Akun Demo (1-Klik Isi)</span>
                    </span>
                    <span class="text-[10px] text-slate-400">Klik untuk mengisi</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5">
                    <!-- Admin -->
                    <button 
                        type="button" 
                        onclick="fillDemoAccount('admin')" 
                        id="demo-pill-admin"
                        class="demo-pill group flex flex-col items-center justify-center p-2 rounded-lg bg-slate-50 hover:bg-purple-50 border border-slate-200/80 hover:border-purple-300 text-slate-700 transition"
                        title="Masuk sebagai Administrator Sistem"
                    >
                        <span class="text-[11px] font-bold group-hover:text-purple-700">Admin</span>
                        <span class="text-[9px] text-slate-400 group-hover:text-purple-600">Akademik</span>
                    </button>

                    <!-- Kaprodi -->
                    <button 
                        type="button" 
                        onclick="fillDemoAccount('kaprodi')" 
                        id="demo-pill-kaprodi"
                        class="demo-pill group flex flex-col items-center justify-center p-2 rounded-lg bg-slate-50 hover:bg-blue-50 border border-slate-200/80 hover:border-blue-300 text-slate-700 transition"
                        title="Masuk sebagai Ketua Program Studi"
                    >
                        <span class="text-[11px] font-bold group-hover:text-blue-700">Kaprodi</span>
                        <span class="text-[9px] text-slate-400 group-hover:text-blue-600">Sistem Info</span>
                    </button>

                    <!-- Dosen PA -->
                    <button 
                        type="button" 
                        onclick="fillDemoAccount('dosen_pa')" 
                        id="demo-pill-dosen_pa"
                        class="demo-pill group flex flex-col items-center justify-center p-2 rounded-lg bg-slate-50 hover:bg-amber-50 border border-slate-200/80 hover:border-amber-300 text-slate-700 transition"
                        title="Masuk sebagai Dosen Pembimbing Akademik"
                    >
                        <span class="text-[11px] font-bold group-hover:text-amber-700">Dosen PA</span>
                        <span class="text-[9px] text-slate-400 group-hover:text-amber-600">Konseling</span>
                    </button>

                    <!-- Mahasiswa -->
                    <button 
                        type="button" 
                        onclick="fillDemoAccount('mahasiswa')" 
                        id="demo-pill-mahasiswa"
                        class="demo-pill group flex flex-col items-center justify-center p-2 rounded-lg bg-slate-50 hover:bg-emerald-50 border border-slate-200/80 hover:border-emerald-300 text-slate-700 transition"
                        title="Masuk sebagai Mahasiswa (NPM: 2271020052)"
                    >
                        <span class="text-[11px] font-bold group-hover:text-emerald-700">Mahasiswa</span>
                        <span class="text-[9px] text-slate-400 group-hover:text-emerald-600">Uji Mandiri</span>
                    </button>
                </div>
            </div>

            <!-- Server Flash Errors / Messages -->
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

            <!-- Main Authentication Form -->
            <form method="POST" action="{{ route('login.post') }}" id="loginForm" class="space-y-4">
                @csrf

                <!-- Login Identifier (Email / NPM / NIP) -->
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
                            class="w-full pl-10 pr-4 py-2.5 bg-white border @error('login_identifier') border-rose-300 bg-rose-50/30 @else border-slate-200/90 @enderror rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 transition shadow-2xs"
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
                            class="w-full pl-10 pr-10 py-2.5 bg-white border @error('password') border-rose-300 bg-rose-50/30 @else border-slate-200/90 @enderror rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/25 focus:border-brand-500 transition shadow-2xs font-mono"
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

                <!-- Remember Me & Forgot Password Link -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center space-x-2 text-xs text-slate-600 cursor-pointer select-none">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            id="remember" 
                            class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500/30 transition cursor-pointer"
                        >
                        <span>Ingat saya di perangkat ini</span>
                    </label>

                    <button type="button" onclick="openHelpModal()" class="text-xs font-semibold text-brand-700 hover:text-brand-800 hover:underline">
                        Lupa sandi?
                    </button>
                </div>

                <!-- Submit Button with Spinner -->
                <button 
                    type="submit" 
                    id="submitBtn"
                    class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-700 hover:from-emerald-500 hover:via-emerald-600 hover:to-teal-600 text-white text-xs font-bold shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center space-x-2 cursor-pointer mt-2"
                >
                    <span id="submitBtnText">Masuk Sekarang</span>
                    <i id="submitBtnIcon" data-lucide="arrow-right" class="w-4 h-4"></i>
                    <svg id="submitBtnSpinner" class="hidden animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>

            <!-- Bottom Security Note -->
            <div class="pt-4 border-t border-slate-200/80 text-center">
                <p class="text-[11px] text-slate-400 flex items-center justify-center space-x-1.5">
                    <i data-lucide="shield" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Terenkripsi aman dengan protokol HTTPS & Hashing Bcrypt</span>
                </p>
            </div>

        </div>

        <!-- Right Footer Info -->
        <footer class="pt-6 border-t border-slate-200/80 text-center text-xs text-slate-400">
            <p>© {{ date('Y') }} <strong>SIPRA-C4.5</strong> • Universitas Islam Negeri Raden Intan Lampung</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Program Studi Sistem Informasi • Fakultas Sains dan Teknologi</p>
        </footer>

    </div>

</div>

<!-- ========================================== -->
<!-- MODAL: Panduan & Bantuan Masuk -->
<!-- ========================================== -->
<div id="helpModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 backdrop-blur-xs p-4 transition-opacity">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 sm:p-7 space-y-5 border border-slate-200 shadow-2xl relative">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center font-bold text-xs">
                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900">Pusat Bantuan & Autentikasi</h4>
                    <p class="text-[11px] text-slate-500">Ketentuan login portal SIPRA-C4.5 UIN Raden Intan Lampung</p>
                </div>
            </div>
            <button type="button" onclick="closeHelpModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="space-y-3.5 text-xs text-slate-600 leading-relaxed">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 space-y-1.5">
                <p class="font-bold text-slate-900 flex items-center gap-1.5">
                    <i data-lucide="key" class="w-3.5 h-3.5 text-brand-600"></i>
                    Format Kredensial Masuk
                </p>
                <ul class="list-disc list-inside space-y-1 text-slate-600 pl-1">
                    <li><strong>Dosen PA & Kaprodi</strong>: Gunakan NIP resmi atau alamat email berdomain <code>@uinril.ac.id</code>.</li>
                    <li><strong>Mahasiswa</strong>: Gunakan NPM aktif (contoh: <code>2271020052</code>) atau email student.</li>
                    <li><strong>Admin</strong>: Menggunakan akun berwenang pengelola portal SIPRA.</li>
                </ul>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 space-y-1.5">
                <p class="font-bold text-slate-900 flex items-center gap-1.5">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-amber-600"></i>
                    Lupa Kata Sandi / Kendala Akses?
                </p>
                <p>
                    Silakan hubungi <strong>Administrator Akademik Fakultas Sains dan Teknologi</strong> atau unit Puskom UIN Raden Intan Lampung untuk verifikasi identitas dan pengaturan ulang kata sandi akun Anda.
                </p>
            </div>
        </div>

        <div class="pt-2 flex justify-end">
            <button type="button" onclick="closeHelpModal()" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                Tutup Panduan
            </button>
        </div>
    </div>
</div>

<script>
    // Demo account definitions
    const demoAccounts = {
        admin: { id: 'admin@uinril.ac.id', pass: 'admin123', label: 'Administrator Akademik' },
        kaprodi: { id: 'kaprodi@uinril.ac.id', pass: 'prodi123', label: 'Ketua Program Studi' },
        dosen_pa: { id: 'dosenpa@uinril.ac.id', pass: 'dosen123', label: 'Dosen Pembimbing Akademik' },
        mahasiswa: { id: '2271020052', pass: 'password', label: 'Mahasiswa (Pingky Hera Veliyanti)' }
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
            pill.classList.remove('bg-emerald-50', 'border-emerald-500', 'ring-2', 'ring-emerald-500/25');
        });

        // Highlight selected pill
        const activePill = document.getElementById('demo-pill-' + role);
        if (activePill) {
            activePill.classList.add('bg-emerald-50', 'border-emerald-500', 'ring-2', 'ring-emerald-500/25');
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
            }, 3500);
        }

        // Re-focus password or identifier
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
        btnText.textContent = 'Memverifikasi Kredensial...';
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
