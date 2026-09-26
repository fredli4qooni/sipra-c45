@extends('layouts.app')

@section('title', 'Training Model C4.5 Baru')
@section('subtitle', 'Konfigurasi parameter data mining pohon keputusan C4.5')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Dataset Availability Info -->
    <div class="p-4 rounded-xl bg-brand-50 border border-brand-200 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-lg bg-brand-600 text-white flex items-center justify-center font-bold text-xs">
                <i data-lucide="database" class="w-4 h-4"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-900">Total Dataset Tersedia: {{ $totalDataset }} Baris Data Akademik</p>
                <p class="text-[11px] text-brand-800">Dataset ini akan digunakan untuk proses training pohon keputusan dan evaluasi matriks.</p>
            </div>
        </div>
        <a href="{{ route('admin.akademik.index') }}" class="text-xs font-bold text-brand-700 hover:underline inline-flex items-center gap-1">
            <span>Lihat Dataset</span>
            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
        </a>
    </div>

    <!-- Training Form Card -->
    <div class="bg-white p-6 sm:p-8 rounded-xl border border-slate-200/80 shadow-xs space-y-6">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center">
                    <i data-lucide="brain-circuit" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Konfigurasi Pelatihan Algoritma</h3>
                    <p class="text-xs text-slate-500">Tentukan nama model, rasio pembagian data, dan fitur prediktor</p>
                </div>
            </div>
            <a href="{{ route('admin.c45.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-100 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                Kembali
            </a>
        </div>

        <form method="POST" action="{{ route('admin.c45.store') }}" class="space-y-5">
            @csrf

            <!-- Nama Model -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Model C4.5 <span class="text-rose-500">*</span></label>
                <input 
                    type="text" 
                    name="nama_model" 
                    value="{{ old('nama_model', 'Model C4.5 Evaluasi ' . date('d/m/Y H:i')) }}" 
                    required 
                    placeholder="contoh: Model C4.5 Skripsi 2026" 
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                >
                @error('nama_model') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan / Deskripsi Eksperimen</label>
                <textarea 
                    name="deskripsi" 
                    rows="2" 
                    placeholder="Deskripsi tujuan eksperimen, karakteristik dataset, dll." 
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                >{{ old('deskripsi') }}</textarea>
            </div>

            <!-- Stratified Data Splitting Ratio & Random State Seed -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">
                        Rasio Pembagian Data (Data Splitting) <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        @foreach(['80:20' => '80% Latih / 20% Uji (Standar)', '70:30' => '70% Latih / 30% Uji', '90:10' => '90% Latih / 10% Uji', '100:0' => '100% Seluruh Dataset'] as $ratio => $desc)
                            <label class="p-3 rounded-lg border border-slate-200 bg-slate-50/50 hover:bg-brand-50/50 hover:border-brand-200 cursor-pointer flex flex-col justify-between transition">
                                <div class="flex items-center space-x-2">
                                    <input type="radio" name="split_ratio" value="{{ $ratio }}" {{ old('split_ratio', '80:20') == $ratio ? 'checked' : '' }} class="w-4 h-4 text-brand-600 focus:ring-brand-500 border-slate-300">
                                    <span class="font-bold text-xs text-slate-900">{{ $ratio }}</span>
                                </div>
                                <span class="text-[10px] text-slate-500 mt-1.5">{{ $desc }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-semibold text-slate-700">
                            Random Seed <span class="text-[10px] text-brand-600 font-normal">(Reproducibility)</span>
                        </label>
                        <span class="text-[10px] font-mono text-slate-400">Default: 42</span>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="dice-5" class="w-4 h-4"></i>
                        </div>
                        <input 
                            type="number" 
                            name="random_seed" 
                            id="random_seed"
                            value="{{ old('random_seed', 42) }}" 
                            min="1" 
                            max="999999"
                            class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                        >
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1.5 leading-relaxed">
                        Kunci acak deterministik berstrata (<em>Stratified Split</em>) agar pohon keputusan & evaluasi dapat direproduksi konsisten sesuai kaidah penelitian.
                    </p>
                    @error('random_seed') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Feature Selection Checklist -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-semibold text-slate-700">Pilih Fitur Atribut Prediktor <span class="text-rose-500">*</span></label>
                    <span class="text-[10px] text-slate-400">Minimal 2 fitur</span>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 p-4 rounded-lg bg-slate-50 border border-slate-200">
                    @foreach($availableFeatures as $key => $label)
                        <label class="flex items-start space-x-2.5 p-2 rounded-lg bg-white border border-slate-200 hover:border-brand-200 cursor-pointer transition">
                            <input type="checkbox" name="features[]" value="{{ $key }}" checked class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300 mt-0.5">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">{{ $label }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $key }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('features') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Set as active model checkbox -->
            <div class="pt-1">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-xs font-semibold text-slate-700">Jadikan model ini sebagai Model Aktif Sistem setelah selesai dilatih</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                <a href="{{ route('admin.c45.index') }}" class="px-4 py-2.5 rounded-lg bg-slate-100 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-lg text-xs font-bold text-white bg-brand-600 hover:bg-brand-500 shadow-xs transition flex items-center">
                    <i data-lucide="play" class="w-4 h-4 mr-2"></i>
                    Mulai Training C4.5 Sekarang
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
