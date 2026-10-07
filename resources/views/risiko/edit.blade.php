@extends('layouts.app')

@section('title', 'Edit Teks Peringatan: ' . $risiko->nama_risiko)
@section('subtitle', 'Kustomisasi pesan deteksi dini, arahan konsultasi Dosen PA, dan rekomendasi studi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action Toolbar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.risiko.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1.5"></i>
            Kembali ke Daftar Risiko
        </a>

        <div class="flex items-center space-x-2">
            <form method="POST" action="{{ route('admin.risiko.reset', $risiko) }}" onsubmit="return confirm('Kembalikan seluruh teks formulir ini ke standar pabrik / default?')">
                @csrf
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition flex items-center">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 mr-1.5 text-slate-500"></i>
                    Reset ke Teks Standar
                </button>
            </form>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
        
        <!-- Header Info -->
        <div class="flex items-center space-x-3.5 pb-5 border-b border-slate-100">
            @php
                $iconColor = match($risiko->warna) {
                    'rose' => 'bg-rose-500 text-white',
                    'amber' => 'bg-amber-500 text-white',
                    default => 'bg-brand-600 text-white',
                };
            @endphp
            <div class="w-12 h-12 rounded-xl {{ $iconColor }} flex items-center justify-center flex-shrink-0 shadow-xs">
                <i data-lucide="{{ $risiko->icon }}" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-lg font-extrabold text-slate-900 font-display">Pengaturan Teks {{ $risiko->nama_risiko }}</h3>
                <p class="text-xs text-slate-500">Sesuaikan narasi dan panduan yang akan dibaca oleh mahasiswa saat terdeteksi pada status ini</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.risiko.update', $risiko) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Row 1: Label Badge & Deskripsi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <div class="space-y-1.5">
                    <label for="label_badge" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Label Badge Kategori <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="label_badge" 
                        id="label_badge" 
                        value="{{ old('label_badge', $risiko->label_badge) }}" 
                        required
                        placeholder="contoh: Perhatian Khusus / Waspada / Aman"
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('label_badge') border-rose-300 bg-rose-50/30 @else border-slate-200 @enderror rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition shadow-2xs font-semibold"
                    >
                    @error('label_badge')
                        <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="deskripsi_singkat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Deskripsi Singkat Indikator
                    </label>
                    <input 
                        type="text" 
                        name="deskripsi_singkat" 
                        id="deskripsi_singkat" 
                        value="{{ old('deskripsi_singkat', $risiko->deskripsi_singkat) }}" 
                        placeholder="contoh: Mahasiswa terindikasi memiliki kendala evaluasi studi..."
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('deskripsi_singkat') border-rose-300 bg-rose-50/30 @else border-slate-200 @enderror rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition shadow-2xs"
                    >
                    @error('deskripsi_singkat')
                        <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Field 2: Teks Pesan Peringatan Utama -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="pesan_peringatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Teks Pesan Peringatan Utama (Tampil di Dasbor Mahasiswa) <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[11px] text-slate-400">Kotak peringatan dini portal mahasiswa</span>
                </div>
                <textarea 
                    name="pesan_peringatan" 
                    id="pesan_peringatan" 
                    rows="4" 
                    required
                    class="w-full p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('pesan_peringatan') border-rose-300 bg-rose-50/30 @else border-slate-200 @enderror rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition shadow-2xs leading-relaxed"
                >{{ old('pesan_peringatan', $risiko->pesan_peringatan) }}</textarea>
                @error('pesan_peringatan')
                    <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-slate-500">Pesan ini langsung dibaca oleh mahasiswa di dalam kotak panduan Dosen PA saat status deteksi dini aktif.</p>
            </div>

            <!-- Field 3: Rekomendasi Rencana Studi -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="rekomendasi_studi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Teks Rekomendasi Rencana Studi <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[11px] text-slate-400">Tampil pada kartu rekomendasi studi</span>
                </div>
                <textarea 
                    name="rekomendasi_studi" 
                    id="rekomendasi_studi" 
                    rows="3" 
                    required
                    class="w-full p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white border @error('rekomendasi_studi') border-rose-300 bg-rose-50/30 @else border-slate-200 @enderror rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition shadow-2xs leading-relaxed"
                >{{ old('rekomendasi_studi', $risiko->rekomendasi_studi) }}</textarea>
                @error('rekomendasi_studi')
                    <p class="text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Field 4: Poin Checklist Panduan Konsultasi Dosen PA -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="panduan_konsultasi_pa" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Poin-Poin Panduan Konsultasi ke Dosen PA
                    </label>
                    <span class="text-[11px] text-slate-400">1 baris = 1 poin checklist</span>
                </div>
                <textarea 
                    name="panduan_konsultasi_pa" 
                    id="panduan_konsultasi_pa" 
                    rows="5" 
                    placeholder="Tuliskan tiap poin bimbingan pada baris baru..."
                    class="w-full p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-200 rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition shadow-2xs font-mono leading-relaxed"
                >{{ old('panduan_konsultasi_pa', $panduanText) }}</textarea>
                <p class="text-[11px] text-slate-500">Tiap baris teks baru akan secara otomatis diubah menjadi butir daftar checklist (bullet list) pada portal mahasiswa.</p>
            </div>

            <!-- Field 5: Template Draf Pesan WhatsApp ke Dosen PA -->
            <div class="space-y-2 p-4 rounded-xl bg-emerald-50/50 border border-emerald-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="message-circle" class="w-4 h-4 text-emerald-600"></i>
                        <label for="template_wa" class="block text-xs font-bold text-emerald-950 uppercase tracking-wider">
                            Template Draf Pesan WhatsApp Otomatis ke Dosen PA
                        </label>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">WhatsApp Click-to-Chat</span>
                </div>

                <textarea 
                    name="template_wa" 
                    id="template_wa" 
                    rows="4" 
                    class="w-full p-3 bg-white border border-emerald-300/80 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition shadow-2xs leading-relaxed"
                >{{ old('template_wa', $risiko->template_wa) }}</textarea>

                <div class="space-y-1">
                    <p class="text-[11px] font-bold text-slate-700">Variabel Dinamis yang Tersedia (Otomatis Terisi):</p>
                    <div class="flex flex-wrap gap-1.5 text-[10px] font-mono">
                        <span class="px-2 py-0.5 rounded bg-white border border-slate-200 text-slate-700 font-semibold" title="Nama Mahasiswa">{nama}</span>
                        <span class="px-2 py-0.5 rounded bg-white border border-slate-200 text-slate-700 font-semibold" title="Nomor Pokok Mahasiswa">{nim}</span>
                        <span class="px-2 py-0.5 rounded bg-white border border-slate-200 text-slate-700 font-semibold" title="Nama Dosen Pembimbing">{dosen_pa}</span>
                        <span class="px-2 py-0.5 rounded bg-white border border-slate-200 text-slate-700 font-semibold" title="Status Risiko">{status_risiko}</span>
                        <span class="px-2 py-0.5 rounded bg-white border border-slate-200 text-slate-700 font-semibold" title="Program Studi">{prodi}</span>
                    </div>
                </div>
            </div>

            <!-- Form Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.risiko.index') }}" class="px-4 py-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs shadow-xs hover:shadow-sm transition flex items-center space-x-1.5 cursor-pointer">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Perubahan Teks</span>
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
