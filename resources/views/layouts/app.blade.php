<!DOCTYPE html>
<html lang="id" class="h-full bg-[#f8fafc]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIPRA-C4.5') — Prediksi Risiko Akademik UIN RIL</title>

    <!-- Google Fonts: Outfit (Display & Headings) + Plus Jakarta Sans (Body) + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                borderRadius: {
                    'none': '0px',
                    'xs': '2px',
                    'sm': '3px',
                    'DEFAULT': '4px',
                    'md': '6px',
                    'lg': '8px',
                    'xl': '10px',
                    '2xl': '12px',
                    '3xl': '14px',
                    'full': '9999px',
                },
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
        }
        h1, h2, h3, h4, h5, h6, .font-heading, .font-display {
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.025em;
        }
        /* Custom subtle scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-[#f8fafc] text-slate-800 antialiased selection:bg-brand-100 selection:text-brand-900">

    <div class="min-h-full flex">
        
        <!-- SIDEBAR (Reference Minimalist Style) -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-72 bg-white border-r border-slate-200/80 flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 -translate-x-full">
            
            <!-- Top Section: User Profile Card (Matching Reference Design) -->
            <div class="p-4 border-b border-slate-100">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 hover:border-slate-300 transition space-y-3">
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 min-w-0">
                            <!-- Initials Avatar -->
                            @php
                                $name = auth()->user()->name ?? 'User';
                                $words = explode(' ', $name);
                                $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                            @endphp
                            <div class="w-10 h-10 rounded-lg bg-brand-600 text-white font-bold text-xs flex items-center justify-center shadow-xs flex-shrink-0">
                                {{ $initials }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ $name }}</p>
                                <p class="text-[11px] text-slate-500 truncate font-mono">
                                    {{ auth()->user()->nim_nip ?? auth()->user()->email }}
                                </p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider
                            @if(auth()->user()->isAdmin()) bg-purple-50 text-purple-700 border border-purple-200/60
                            @elseif(auth()->user()->isProdi()) bg-blue-50 text-blue-700 border border-blue-200/60
                            @elseif(auth()->user()->isDosenPa()) bg-amber-50 text-amber-700 border border-amber-200/60
                            @else bg-brand-50 text-brand-700 border border-brand-200/60
                            @endif">
                            {{ auth()->user()->role_label }}
                        </span>
                    </div>

                    <!-- Quick Action Mini Pills (Profile, Bantuan, Logout) -->
                    <div class="grid grid-cols-3 gap-1.5 pt-1 border-t border-slate-200/60">
                        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center p-1.5 rounded-lg bg-white hover:bg-slate-100/80 border border-slate-200/60 text-slate-600 hover:text-slate-900 text-[10px] font-semibold transition">
                            <i data-lucide="user" class="w-3.5 h-3.5 mb-0.5 text-slate-500"></i>
                            <span>Profil</span>
                        </a>
                        <a href="{{ route('bantuan') }}" class="flex flex-col items-center justify-center p-1.5 rounded-lg bg-white hover:bg-slate-100/80 border border-slate-200/60 text-slate-600 hover:text-slate-900 text-[10px] font-semibold transition">
                            <i data-lucide="help-circle" class="w-3.5 h-3.5 mb-0.5 text-slate-500"></i>
                            <span>Bantuan</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="w-full h-full flex flex-col items-center justify-center p-1.5 rounded-lg bg-white hover:bg-rose-50 border border-slate-200/60 text-slate-600 hover:text-rose-600 text-[10px] font-semibold transition">
                                <i data-lucide="log-out" class="w-3.5 h-3.5 mb-0.5 text-slate-500 group-hover:text-rose-500"></i>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-3.5 py-3 space-y-5">

                <!-- Section: Utama -->
                <div class="space-y-1">
                    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Utama</p>

                    @php
                        $dashRoute = route('dashboard');
                        $isDash = request()->routeIs('*dashboard');
                    @endphp
                    <a href="{{ $dashRoute }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ $isDash ? 'bg-brand-50 text-brand-800 border border-brand-200/80 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i data-lucide="layout-grid" class="w-4 h-4 {{ $isDash ? 'text-brand-600' : 'text-slate-400' }}"></i>
                        <span>Overview Dasbor</span>
                    </a>
                </div>

                <!-- Section: Master Data (Admin Only) -->
                @if(auth()->user()->isAdmin())
                    <div class="space-y-1">
                        <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Master Data</p>
                        
                        <a href="{{ route('admin.mahasiswa.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('admin.mahasiswa.*') ? 'bg-brand-50 text-brand-800 border border-brand-200/80 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            <i data-lucide="users" class="w-4 h-4 {{ request()->routeIs('admin.mahasiswa.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                            <span>Data Mahasiswa</span>
                        </a>

                        <a href="{{ route('admin.akademik.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('admin.akademik.*') ? 'bg-brand-50 text-brand-800 border border-brand-200/80 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            <i data-lucide="graduation-cap" class="w-4 h-4 {{ request()->routeIs('admin.akademik.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                            <span>Nilai & Akademik</span>
                        </a>
                    </div>
                @endif

                <!-- Section: Data Mining Decision Tree C4.5 -->
                @if(auth()->user()->hasAnyRole(['admin', 'prodi', 'dosen_pa']))
                    <div class="space-y-1">
                        <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Data Mining C4.5</p>

                        @php
                            $c45Route = auth()->user()->isAdmin() ? route('admin.c45.index') : route('prodi.c45.index');
                            $treeRoute = auth()->user()->isAdmin() ? route('admin.tree.show') : route('prodi.tree.show');
                            $rulesRoute = auth()->user()->isAdmin() ? route('admin.rules.index') : route('prodi.rules.index');
                        @endphp

                        <a href="{{ $c45Route }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('*c45.*') ? 'bg-brand-50 text-brand-800 border border-brand-200/80 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            <i data-lucide="cpu" class="w-4 h-4 {{ request()->routeIs('*c45.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                            <span>Training & Evaluasi Model</span>
                        </a>

                        <a href="{{ $treeRoute }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('*tree.*') ? 'bg-brand-50 text-brand-800 border border-brand-200/80 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            <i data-lucide="git-merge" class="w-4 h-4 {{ request()->routeIs('*tree.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                            <span>Pohon Keputusan</span>
                        </a>

                        <a href="{{ $rulesRoute }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('*rules.*') ? 'bg-brand-50 text-brand-800 border border-brand-200/80 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            <i data-lucide="list-tree" class="w-4 h-4 {{ request()->routeIs('*rules.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                            <span>Aturan Klasifikasi IF-THEN</span>
                        </a>
                    </div>

                    <!-- Section: Prediksi & EWS -->
                    <div class="space-y-1">
                        <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Prediksi & EWS</p>

                        @php
                            $prediksiRoute = auth()->user()->isAdmin() ? route('admin.prediksi.index') : route('prodi.prediksi.index');
                            $ewsRoute = auth()->user()->isAdmin() ? route('admin.ews.index') : route('prodi.ews.index');
                            $laporanRoute = auth()->user()->isAdmin() ? route('admin.laporan.index') : route('prodi.laporan.index');
                            $ewsCount = \App\Models\DataAkademik::where('label_risiko_aktual', 'Risiko Tinggi')->count();
                        @endphp

                        <a href="{{ $prediksiRoute }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('*prediksi.*') ? 'bg-brand-50 text-brand-800 border border-brand-200/80 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            <i data-lucide="play-circle" class="w-4 h-4 {{ request()->routeIs('*prediksi.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                            <span>Simulasi & Prediksi Risiko</span>
                        </a>

                        <a href="{{ $ewsRoute }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('*ews.*') ? 'bg-brand-50 text-brand-800 border border-brand-200/80 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="bell-ring" class="w-4 h-4 {{ request()->routeIs('*ews.*') ? 'text-rose-600' : 'text-slate-400' }}"></i>
                                <span>Early Warning System</span>
                            </div>
                            @if($ewsCount > 0)
                                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                    {{ $ewsCount }}
                                </span>
                            @endif
                        </a>

                        <a href="{{ $laporanRoute }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('*laporan.*') ? 'bg-brand-50 text-brand-800 border border-brand-200/80 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            <i data-lucide="file-text" class="w-4 h-4 {{ request()->routeIs('*laporan.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                            <span>Laporan & Rekapitulasi</span>
                        </a>
                    </div>
                @endif

                <!-- Section: Mahasiswa Personal -->
                @if(auth()->user()->isMahasiswa())
                    <div class="space-y-1">
                        <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Akademik Pribadi</p>
                        <a href="{{ route('mahasiswa.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('mahasiswa.*') ? 'bg-brand-50 text-brand-800 border border-brand-200/80 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            <i data-lucide="shield-alert" class="w-4 h-4 text-brand-600"></i>
                            <span>Status & Rekomendasi</span>
                        </a>
                    </div>
                @endif

            </nav>

            <!-- Bottom Brand Stamp -->
            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-[11px] text-slate-500">
                <div class="flex items-center space-x-2">
                    <div class="w-5 h-5 rounded-md bg-brand-600 text-white flex items-center justify-center font-bold text-[9px]">
                        SP
                    </div>
                    <span class="font-bold text-slate-800">SIPRA-C4.5</span>
                </div>
                <span class="font-mono text-[10px] text-slate-400">v1.0 • UIN RIL</span>
            </div>

        </aside>

        <!-- MAIN WRAPPER -->
        <div class="flex-1 flex flex-col lg:pl-72 min-w-0">
            
            <!-- TOPBAR (Matching Reference Header Layout) -->
            <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-4 sm:px-8 py-3.5 flex items-center justify-between gap-4">
                
                <!-- Left: Mobile Toggle & Brand/Breadcrumb -->
                <div class="flex items-center space-x-3 min-w-0">
                    <button id="sidebar-toggle" class="lg:hidden p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 transition">
                        <i data-lucide="menu" class="w-4 h-4"></i>
                    </button>

                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-lg bg-brand-50 border border-brand-200 flex items-center justify-center text-brand-700 shadow-xs">
                            <i data-lucide="brain-circuit" class="w-4 h-4"></i>
                        </div>
                        <div class="hidden sm:block">
                            <h1 class="text-sm font-bold text-slate-900 tracking-tight leading-none">SIPRA-C4.5</h1>
                            <p class="text-[10px] text-slate-500 font-medium">Sistem Informasi Prediksi Risiko Akademik</p>
                        </div>
                    </div>
                </div>

                <!-- Center: Global Quick Search Box -->
                <div class="hidden md:flex flex-1 max-w-md mx-4">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </div>
                        <input 
                            type="text" 
                            placeholder="Cari mahasiswa, NIM, atau menu..." 
                            class="w-full pl-9 pr-4 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                        >
                    </div>
                </div>

                <!-- Right Action Icons & Direct Buttons (Reference Header) -->
                <div class="flex items-center space-x-2">
                    
                    @if(auth()->user()->hasAnyRole(['admin', 'prodi', 'dosen_pa']))
                        @php
                            $quickPrediksiRoute = auth()->user()->isAdmin() ? route('admin.prediksi.single') : route('prodi.prediksi.single');
                        @endphp
                        <a href="{{ $quickPrediksiRoute }}" class="inline-flex items-center px-3.5 py-1.5 rounded-lg bg-brand-600 hover:bg-brand-500 text-xs font-semibold text-white shadow-xs transition">
                            <i data-lucide="plus" class="w-3.5 h-3.5 mr-1.5"></i>
                            <span>Simulasi Prediksi</span>
                        </a>
                    @endif



                    <!-- User Initials Pill & Logout -->
                    <div class="flex items-center pl-2 space-x-1.5 border-l border-slate-200">
                        <a href="{{ route('profile.edit') }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-brand-50 text-slate-700 hover:text-brand-700 border border-slate-200 font-bold text-xs flex items-center justify-center transition" title="Edit Profil">
                            {{ $initials }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" title="Keluar dari Sistem" class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>

                </div>

            </header>

            <!-- PAGE CONTENT (Full Width Responsive with Balanced Padding) -->
            <main class="flex-1 px-4 sm:px-8 py-6 w-full space-y-6 animate-fade-in">
                
                <!-- Breadcrumbs & Title Bar -->
                @hasSection('title')
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2">
                        <div>
                            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">@yield('title')</h2>
                            @hasSection('subtitle')
                                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">@yield('subtitle')</p>
                            @endif
                        </div>
                        @yield('header_actions')
                    </div>
                @endif

                <!-- Flash Alert Messages -->
                @if(session('success'))
                    <div class="p-4 rounded-lg bg-brand-50 border border-brand-200/80 text-brand-900 flex items-start space-x-3 shadow-xs">
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-brand-600 flex-shrink-0 mt-0.5"></i>
                        <div class="text-xs font-medium leading-relaxed">
                            <strong class="font-bold">Berhasil!</strong> {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 rounded-lg bg-rose-50 border border-rose-200/80 text-rose-900 flex items-start space-x-3 shadow-xs">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5"></i>
                        <div class="text-xs font-medium leading-relaxed">
                            <strong class="font-bold">Perhatian:</strong> {{ session('error') }}
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-4 rounded-lg bg-rose-50 border border-rose-200/80 text-rose-900 space-y-1 text-xs shadow-xs">
                        <div class="flex items-center space-x-2 font-bold">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600"></i>
                            <span>Terdapat kesalahan pada formulir:</span>
                        </div>
                        <ul class="list-disc pl-5 space-y-0.5 text-slate-700">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Slot Content -->
                @yield('content')

            </main>

            <!-- FOOTER -->
            <footer class="bg-white border-t border-slate-200/80 px-6 py-4 text-center text-xs text-slate-500">
                <p>© {{ date('Y') }} <strong>SIPRA-C4.5</strong> • Program Studi Sistem Informasi, Fakultas Sains dan Teknologi, UIN Raden Intan Lampung.</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Penelitian Skripsi: Pingky Hera Veliyanti (NIM: 2271020052)</p>
            </footer>

        </div>

    </div>

    <!-- Mobile Drawer Overlay Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/20 backdrop-blur-xs z-30 hidden lg:hidden"></div>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Mobile Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebar-toggle');
        const backdrop = document.getElementById('sidebar-backdrop');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', toggleSidebar);
        }
        if (backdrop) {
            backdrop.addEventListener('click', toggleSidebar);
        }
    </script>
    @stack('scripts')
</body>
</html>
