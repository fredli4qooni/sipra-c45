@extends('layouts.app')

@section('title', 'Detail Model C4.5: ' . $c45->nama_model)
@section('subtitle', 'Evaluasi Confusion Matrix, Aturan Klasifikasi IF-THEN & Log Perhitungan Entropy')

@section('content')
<div class="space-y-6">

    <!-- Top Action Toolbar -->
    <div class="flex items-center justify-between">
        <a href="{{ auth()->user()->isAdmin() ? route('admin.c45.index') : route('prodi.c45.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1.5"></i>
            Kembali ke Daftar Model
        </a>

        <div class="flex items-center space-x-2">
            @if(auth()->user()->isAdmin() && !$c45->is_active)
                <form method="POST" action="{{ route('admin.c45.activate', $c45) }}">
                    @csrf
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-xs font-bold text-white shadow-xs transition flex items-center">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5 mr-1.5"></i>
                        Jadikan Model Aktif
                    </button>
                </form>
            @endif

            <a href="{{ auth()->user()->isAdmin() ? route('admin.tree.show', ['c45' => $c45->id]) : route('prodi.tree.show', ['c45' => $c45->id]) }}" class="px-3.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition flex items-center">
                <i data-lucide="git-merge" class="w-3.5 h-3.5 mr-1.5 text-brand-600"></i>
                Buka Pohon Visual
            </a>
        </div>
    </div>

    <!-- Model Header Card -->
    <div class="bg-white p-6 sm:p-8 rounded-xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center space-x-2">
                @if($c45->is_active)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500 mr-1.5"></span>
                        Model Utama Aktif
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-600">
                        Arsip Model
                    </span>
                @endif
                <span class="text-xs text-slate-500 font-mono">Split: {{ $c45->split_ratio }}</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono bg-slate-100 text-slate-600 border border-slate-200">
                    <i data-lucide="dice-5" class="w-3 h-3 mr-1 text-slate-400"></i>
                    Seed: {{ $c45->random_seed ?? 42 }}
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Stratified Sampling
                </span>
            </div>
            <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $c45->nama_model }}</h3>
            <p class="text-xs text-slate-600 max-w-2xl leading-relaxed">{{ $c45->deskripsi ?? 'Tidak ada deskripsi tambahan.' }}</p>
            <p class="text-[11px] text-slate-400 pt-1">
                Dilatih oleh <strong>{{ $c45->creator->name ?? 'Admin' }}</strong> pada {{ $c45->train_date->format('d F Y, H:i') }} WIB • Dataset: {{ $c45->total_training_samples }} Latih / {{ $c45->total_testing_samples }} Uji • Seed: {{ $c45->random_seed ?? 42 }} (Deterministik & Dapat Direproduksi)
            </p>
        </div>

        <div class="p-5 rounded-lg bg-brand-50/70 border border-brand-200/80 text-center min-w-[150px]">
            <p class="text-[10px] text-brand-700 uppercase font-bold tracking-wider">Akurasi Uji</p>
            <h4 class="text-3xl font-black text-brand-800 mt-0.5">{{ $c45->accuracy }}%</h4>
            <span class="text-[11px] text-brand-600 font-semibold">{{ count($c45->rules) }} Aturan Dihasilkan</span>
        </div>
    </div>

    <!-- Evaluation Metrics Ribbon Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4">
        <!-- Akurasi -->
        <div class="bg-white p-4 rounded-lg border border-slate-200/80 shadow-xs text-center space-y-1">
            <p class="text-[10px] font-bold text-slate-500 uppercase">Akurasi (Accuracy)</p>
            <h5 class="text-xl font-black text-brand-700">{{ $c45->accuracy }}%</h5>
            <p class="text-[10px] text-slate-400">Ketepatan Total</p>
        </div>

        <!-- Presisi -->
        <div class="bg-white p-4 rounded-lg border border-slate-200/80 shadow-xs text-center space-y-1">
            <p class="text-[10px] font-bold text-slate-500 uppercase">Presisi (Precision)</p>
            <h5 class="text-xl font-black text-blue-700">{{ $c45->precision }}%</h5>
            <p class="text-[10px] text-slate-400">Rata-rata Kelas</p>
        </div>

        <!-- Recall -->
        <div class="bg-white p-4 rounded-lg border border-slate-200/80 shadow-xs text-center space-y-1">
            <p class="text-[10px] font-bold text-slate-500 uppercase">Recall (Sensitivitas)</p>
            <h5 class="text-xl font-black text-teal-700">{{ $c45->recall }}%</h5>
            <p class="text-[10px] text-slate-400">Identifikasi Risiko</p>
        </div>

        <!-- Spesifisitas -->
        <div class="bg-white p-4 rounded-lg border border-slate-200/80 shadow-xs text-center space-y-1">
            <p class="text-[10px] font-bold text-slate-500 uppercase">Spesifisitas</p>
            <h5 class="text-xl font-black text-amber-700">{{ $c45->specificity }}%</h5>
            <p class="text-[10px] text-slate-400">True Negative Rate</p>
        </div>

        <!-- F1-Score -->
        <div class="bg-white p-4 rounded-lg border border-slate-200/80 shadow-xs text-center space-y-1 col-span-2 sm:col-span-1">
            <p class="text-[10px] font-bold text-slate-500 uppercase">F1-Score</p>
            <h5 class="text-xl font-black text-purple-700">{{ $c45->f1_score }}%</h5>
            <p class="text-[10px] text-slate-400">Harmonic Mean</p>
        </div>
    </div>

    <!-- Pedoman Kriteria Diskretisasi Akademik (Proposal Skripsi Pinky) -->
    <details class="group bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
        <summary class="cursor-pointer px-5 py-3.5 bg-slate-50/70 hover:bg-slate-100/80 flex items-center justify-between transition select-none list-none [&::-webkit-details-marker]:hidden">
            <div class="flex items-center space-x-2.5">
                <div class="w-7 h-7 rounded-lg bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-xs border border-teal-200/60">
                    <i data-lucide="binary" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-900">Aturan Diskretisasi & Batas Kategori Fitur (Option A - Proposal Skripsi)</h4>
                    <p class="text-[11px] text-slate-500">Transformasi variabel kontinu ke nominal diskrit sebelum pembentukan pohon keputusan C4.5</p>
                </div>
            </div>
            <div class="flex items-center space-x-2 text-slate-400">
                <span class="text-[11px] font-medium hidden sm:inline text-teal-700 group-open:hidden">Lihat Ambang Diskretisasi</span>
                <span class="text-[11px] font-medium hidden sm:inline text-slate-500 hidden group-open:inline">Sembunyikan Pedoman</span>
                <i data-lucide="chevron-down" class="w-4 h-4 transform transition-transform group-open:rotate-180 text-slate-400"></i>
            </div>
        </summary>

        <div class="p-5 border-t border-slate-200 bg-white grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- IPK & IPS -->
            <div class="p-3.5 rounded-lg bg-slate-50/80 border border-slate-200/80 space-y-2.5">
                <div class="flex items-center space-x-2 text-xs font-bold text-slate-800">
                    <i data-lucide="award" class="w-3.5 h-3.5 text-brand-600"></i>
                    <span>IPK & IPS</span>
                </div>
                <div class="space-y-1.5 text-[11px]">
                    <div class="flex justify-between items-center text-slate-600">
                        <span class="font-mono text-slate-700">&lt; 2.75</span>
                        <span class="px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 font-bold border border-rose-200 text-[10px]">Rendah</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600">
                        <span class="font-mono text-slate-700">2.75 – 3.25</span>
                        <span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 font-bold border border-amber-200 text-[10px]">Cukup</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600">
                        <span class="font-mono text-slate-700">&gt; 3.25</span>
                        <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 font-bold border border-emerald-200 text-[10px]">Tinggi</span>
                    </div>
                </div>
            </div>

            <!-- Kehadiran -->
            <div class="p-3.5 rounded-lg bg-slate-50/80 border border-slate-200/80 space-y-2.5">
                <div class="flex items-center space-x-2 text-xs font-bold text-slate-800">
                    <i data-lucide="calendar-check" class="w-3.5 h-3.5 text-teal-600"></i>
                    <span>Kehadiran Kuliah</span>
                </div>
                <div class="space-y-1.5 text-[11px]">
                    <div class="flex justify-between items-center text-slate-600">
                        <span class="font-mono text-slate-700">&lt; 75.0%</span>
                        <span class="px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 font-bold border border-rose-200 text-[10px]">Kurang</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600">
                        <span class="font-mono text-slate-700">75.0% – 85.0%</span>
                        <span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 font-bold border border-amber-200 text-[10px]">Cukup</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600">
                        <span class="font-mono text-slate-700">&gt; 85.0%</span>
                        <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 font-bold border border-emerald-200 text-[10px]">Baik</span>
                    </div>
                </div>
            </div>

            <!-- SKS Diambil -->
            <div class="p-3.5 rounded-lg bg-slate-50/80 border border-slate-200/80 space-y-2.5">
                <div class="flex items-center space-x-2 text-xs font-bold text-slate-800">
                    <i data-lucide="book-open" class="w-3.5 h-3.5 text-indigo-600"></i>
                    <span>Beban SKS Semester</span>
                </div>
                <div class="space-y-1.5 text-[11px]">
                    <div class="flex justify-between items-center text-slate-600">
                        <span class="font-mono text-slate-700">&lt; 18 SKS</span>
                        <span class="px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 font-bold border border-rose-200 text-[10px]">Kurang</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600">
                        <span class="font-mono text-slate-700">18 – 21 SKS</span>
                        <span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 font-bold border border-amber-200 text-[10px]">Cukup</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600">
                        <span class="font-mono text-slate-700">&gt; 21 SKS</span>
                        <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 font-bold border border-emerald-200 text-[10px]">Sangat Baik</span>
                    </div>
                </div>
            </div>

            <!-- Kondisi Khusus -->
            <div class="p-3.5 rounded-lg bg-slate-50/80 border border-slate-200/80 space-y-2.5">
                <div class="flex items-center space-x-2 text-xs font-bold text-slate-800">
                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-amber-600"></i>
                    <span>Kondisi Khusus</span>
                </div>
                <div class="space-y-1.5 text-[11px]">
                    <div class="flex justify-between items-center text-slate-600">
                        <span>Status Cuti</span>
                        <span class="px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 font-bold border border-rose-200 text-[10px]">Ya / Tidak</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600">
                        <span>SKS Mengulang</span>
                        <span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 font-bold border border-amber-200 text-[10px]">Ada / Tidak</span>
                    </div>
                    <p class="text-[10px] text-slate-500 pt-0.5">
                        *Diselaraskan dengan batasan metodologi penelitian Pinky.
                    </p>
                </div>
            </div>
        </div>
    </details>

    <!-- Confusion Matrix Heatmap -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-2.5">
                <i data-lucide="grid" class="w-4 h-4 text-brand-600"></i>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Confusion Matrix 3x3 (Hasil Uji Klasifikasi)</h4>
            </div>
            <span class="text-xs text-slate-500">Sampel Uji: {{ $c45->total_testing_samples > 0 ? $c45->total_testing_samples : $c45->total_training_samples }} Data</span>
        </div>

        @php
            $matrix = $evaluation['confusion_matrix'] ?? [
                'Risiko Rendah' => ['Risiko Rendah' => 0, 'Risiko Sedang' => 0, 'Risiko Tinggi' => 0],
                'Risiko Sedang' => ['Risiko Rendah' => 0, 'Risiko Sedang' => 0, 'Risiko Tinggi' => 0],
                'Risiko Tinggi' => ['Risiko Rendah' => 0, 'Risiko Sedang' => 0, 'Risiko Tinggi' => 0],
            ];
            $classes = ['Risiko Rendah', 'Risiko Sedang', 'Risiko Tinggi'];
        @endphp

        <div class="overflow-x-auto">
            <table class="w-full text-center text-xs border-collapse">
                <thead>
                    <tr>
                        <th colspan="2" rowspan="2" class="p-2.5 border border-slate-200 bg-slate-50 font-semibold text-slate-500">
                            Matriks Evaluasi
                        </th>
                        <th colspan="3" class="p-2.5 border border-slate-200 bg-brand-50/70 font-bold text-brand-800">
                            Kelas Prediksi (Predicted Class)
                        </th>
                    </tr>
                    <tr>
                        <th class="p-2.5 border border-slate-200 bg-slate-50 font-bold text-brand-700">Risiko Rendah</th>
                        <th class="p-2.5 border border-slate-200 bg-slate-50 font-bold text-amber-700">Risiko Sedang</th>
                        <th class="p-2.5 border border-slate-200 bg-slate-50 font-bold text-rose-700">Risiko Tinggi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classes as $actualClass)
                        <tr>
                            @if($loop->first)
                                <th rowspan="3" class="p-3 border border-slate-200 bg-slate-100 font-bold text-slate-700 transform -rotate-90 w-12 text-center">
                                    Aktual
                                </th>
                            @endif
                            <th class="p-3 border border-slate-200 bg-slate-50 font-bold text-left
                                @if($actualClass === 'Risiko Rendah') text-brand-700
                                @elseif($actualClass === 'Risiko Sedang') text-amber-700
                                @else text-rose-700
                                @endif">
                                {{ $actualClass }}
                            </th>
                            @foreach($classes as $predClass)
                                @php
                                    $val = $matrix[$actualClass][$predClass] ?? 0;
                                    $isDiagonal = ($actualClass === $predClass);
                                @endphp
                                <td class="p-3 border border-slate-200 font-bold text-sm
                                    @if($isDiagonal && $val > 0) bg-brand-50 text-brand-800
                                    @elseif(!$isDiagonal && $val > 0) bg-rose-50 text-rose-800
                                    @else bg-white text-slate-400
                                    @endif">
                                    {{ $val }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Extracted IF-THEN Rules Table -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-2.5">
                <i data-lucide="list-tree" class="w-4 h-4 text-amber-600"></i>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Himpunan Aturan Klasifikasi (IF - THEN Rules)</h4>
            </div>
            <span class="text-xs text-slate-500 font-medium">{{ count($c45->rules) }} Aturan Diekstrak</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100">
                        <th class="pb-3 font-semibold w-16">Kode</th>
                        <th class="pb-3 font-semibold">Aturan Logika Pohon Keputusan (IF - THEN)</th>
                        <th class="pb-3 font-semibold">Keputusan Risiko</th>
                        <th class="pb-3 font-semibold text-center">Confidence</th>
                        <th class="pb-3 font-semibold text-center">Dukungan Sampel</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono">
                    @forelse($c45->rules as $rule)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 font-bold text-amber-700">{{ $rule->rule_code }}</td>
                            <td class="py-3 font-sans text-slate-800 text-xs">
                                @php
                                    $formatted = str_replace(
                                        ['IF ', ' AND ', ' THEN '],
                                        ['<strong class="text-purple-700 font-bold">IF</strong> ', ' <strong class="text-teal-700 font-bold">AND</strong> ', ' <strong class="text-amber-700 font-bold">THEN</strong> '],
                                        e($rule->rule_text)
                                    );
                                @endphp
                                {!! $formatted !!}
                            </td>
                            <td class="py-3 font-sans">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold
                                    @if($rule->decision === 'Risiko Rendah') bg-brand-50 text-brand-700 border border-brand-200/60
                                    @elseif($rule->decision === 'Risiko Sedang') bg-amber-50 text-amber-700 border border-amber-200/60
                                    @else bg-rose-50 text-rose-700 border border-rose-200/60
                                    @endif">
                                    {{ $rule->decision }}
                                </span>
                            </td>
                            <td class="py-3 text-center text-slate-900 font-sans font-bold">{{ $rule->confidence }}%</td>
                            <td class="py-3 text-center text-slate-500 font-sans">{{ $rule->support_samples }} data</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400 font-sans">Belum ada aturan yang diekstrak.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Step-by-Step Mathematical Calculation Logs -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-2.5">
                <i data-lucide="calculator" class="w-4 h-4 text-teal-600"></i>
                <div>
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Log Perhitungan Matematis C4.5 per Node</h4>
                    <p class="text-[11px] text-slate-500">Rincian Entropy, Information Gain, Split Information, dan Gain Ratio setiap iterasi cabang</p>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($calculationLogs as $idx => $step)
                <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-2">
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-0.5 rounded-md bg-purple-100 text-purple-800 font-mono text-[10px] font-bold">Node #{{ $idx + 1 }} (Depth {{ $step['depth'] ?? 1 }})</span>
                            <span class="text-xs font-bold text-slate-900">Jalur: <span class="font-mono text-brand-700">{{ $step['node_path'] ?? 'Root' }}</span></span>
                        </div>
                        <div class="text-xs text-slate-500">
                            Total Sampel: <strong class="text-slate-900">{{ $step['samples_count'] ?? 0 }}</strong> • Entropy Parent: <strong class="text-brand-700">{{ $step['entropy_parent'] ?? 0 }}</strong>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-[11px]">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-200">
                                    <th class="pb-2 font-semibold">Atribut Kandidat</th>
                                    <th class="pb-2 font-semibold">Information Gain</th>
                                    <th class="pb-2 font-semibold">Split Information</th>
                                    <th class="pb-2 font-semibold">Gain Ratio</th>
                                    <th class="pb-2 font-semibold text-right">Status Terpilih</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach(($step['calculations'] ?? []) as $attr => $calc)
                                    @php $isBest = ($attr === ($step['best_attribute'] ?? '')); @endphp
                                    <tr class="{{ $isBest ? 'bg-brand-50/70 font-semibold' : '' }}">
                                        <td class="py-2 {{ $isBest ? 'text-brand-900 font-bold' : 'text-slate-700' }}">{{ $attr }}</td>
                                        <td class="py-2 text-slate-600">{{ $calc['gain'] ?? 0 }}</td>
                                        <td class="py-2 text-slate-500">{{ $calc['split_info'] ?? 0 }}</td>
                                        <td class="py-2 {{ $isBest ? 'text-brand-700 font-bold' : 'text-slate-700' }}">{{ $calc['gain_ratio'] ?? 0 }}</td>
                                        <td class="py-2 text-right">
                                            @if($isBest)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] bg-brand-100 text-brand-800 font-bold border border-brand-200">
                                                    <i data-lucide="check" class="w-3 h-3 text-brand-700 flex-shrink-0"></i>
                                                    <span>Terpilih (Gain Ratio Tertinggi)</span>
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-[10px]">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 py-3 text-center">Log kalkulasi tidak tersedia.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
