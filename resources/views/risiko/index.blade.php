@extends('layouts.app')

@section('title', 'Kelola Teks & Kategori Risiko')
@section('subtitle', 'Kustomisasi pesan peringatan, rekomendasi rencana studi, dan panduan bimbingan Dosen PA')

@section('content')
<div class="space-y-6">

    <!-- Header Information Banner -->
    <div class="p-6 rounded-xl bg-white border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                    <i data-lucide="sliders" class="w-3.5 h-3.5 mr-1.5 text-purple-600"></i>
                    Modul Pengaturan Risiko
                </span>
            </div>
            <h3 class="text-xl font-extrabold text-slate-900 tracking-tight font-display">Kustomisasi Pesan Peringatan & Rekomendasi</h3>
            <p class="text-xs text-slate-500 max-w-3xl leading-relaxed">
                Admin dapat menyesuaikan kalimat peringatan, saran rencana studi, daftar poin konsultasi ke Dosen PA, serta format draf chat WhatsApp untuk setiap kategori hasil klasifikasi (Tinggi, Sedang, Rendah). Pesan yang dikustomisasi akan langsung tampil di portal mahasiswa.
            </p>
        </div>

        <div class="flex items-center space-x-2 self-start md:self-auto flex-shrink-0">
            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                <i data-lucide="layers" class="w-4 h-4 mr-1.5 text-slate-500"></i>
                3 Kategori Aktif
            </span>
        </div>
    </div>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between shadow-2xs">
            <div class="flex items-center space-x-2.5">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center space-x-2.5 shadow-2xs">
            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 flex-shrink-0"></i>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    <!-- 3 Risk Configuration Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @foreach($kategoriRisikos as $item)
            @php
                $isHigh = $item->kode === 'tinggi';
                $isMedium = $item->kode === 'sedang';
                $isLow = $item->kode === 'rendah';

                $badgeColor = match($item->warna) {
                    'rose' => 'bg-rose-50 border-rose-200 text-rose-800',
                    'amber' => 'bg-amber-50 border-amber-200 text-amber-800',
                    default => 'bg-brand-50 border-brand-200 text-brand-800',
                };

                $iconColor = match($item->warna) {
                    'rose' => 'bg-rose-500 text-white',
                    'amber' => 'bg-amber-500 text-white',
                    default => 'bg-brand-600 text-white',
                };

                $cardBorder = match($item->warna) {
                    'rose' => 'border-rose-200/90 hover:border-rose-300',
                    'amber' => 'border-amber-200/90 hover:border-amber-300',
                    default => 'border-brand-200/90 hover:border-brand-300',
                };
            @endphp

            <div class="bg-white rounded-xl border {{ $cardBorder }} shadow-xs flex flex-col justify-between overflow-hidden transition hover:shadow-sm">
                
                <!-- Card Header -->
                <div class="p-6 border-b border-slate-100 bg-slate-50/40 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl {{ $iconColor }} flex items-center justify-center flex-shrink-0 shadow-2xs">
                                <i data-lucide="{{ $item->icon }}" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-base font-extrabold text-slate-900 font-display">{{ $item->nama_risiko }}</h4>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $badgeColor }} border mt-0.5">
                                    {{ $item->label_badge }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($item->deskripsi_singkat)
                        <p class="text-xs text-slate-500 leading-relaxed">
                            {{ $item->deskripsi_singkat }}
                        </p>
                    @endif
                </div>

                <!-- Preview Content Body -->
                <div class="p-6 space-y-4 flex-1 text-xs">
                    
                    <!-- 1. Pesan Peringatan Mahasiswa -->
                    <div class="space-y-1.5">
                        <div class="flex items-center space-x-1.5 text-slate-700 font-bold">
                            <i data-lucide="bell" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span class="text-[11px] uppercase tracking-wider">Teks Pesan Peringatan:</span>
                        </div>
                        <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200/90 text-slate-700 leading-relaxed max-h-36 overflow-y-auto">
                            "{{ $item->pesan_peringatan }}"
                        </div>
                    </div>

                    <!-- 2. Rekomendasi Rencana Studi -->
                    <div class="space-y-1.5">
                        <div class="flex items-center space-x-1.5 text-slate-700 font-bold">
                            <i data-lucide="lightbulb" class="w-3.5 h-3.5 text-amber-500"></i>
                            <span class="text-[11px] uppercase tracking-wider">Rekomendasi Rencana Studi:</span>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-slate-600 leading-relaxed max-h-28 overflow-y-auto">
                            "{{ $item->rekomendasi_studi }}"
                        </div>
                    </div>

                    <!-- 3. Poin Panduan Konsultasi Dosen PA -->
                    <div class="space-y-1.5">
                        <div class="flex items-center space-x-1.5 text-slate-700 font-bold">
                            <i data-lucide="check-square" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span class="text-[11px] uppercase tracking-wider">Poin Bimbingan Dosen PA ({{ count($item->panduan_konsultasi_pa ?? []) }} Poin):</span>
                        </div>
                        <ul class="list-disc pl-4 space-y-1 text-[11px] text-slate-600 leading-relaxed max-h-32 overflow-y-auto bg-slate-50 p-3 rounded-lg border border-slate-200">
                            @forelse($item->panduan_konsultasi_pa ?? [] as $panduan)
                                <li>{{ $panduan }}</li>
                            @empty
                                <li class="text-slate-400 italic">Belum ada poin panduan khusus.</li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- 4. Template WhatsApp Info -->
                    @if($item->template_wa)
                        <div class="space-y-1 pt-1">
                            <span class="text-[10px] text-emerald-700 font-semibold flex items-center gap-1">
                                <i data-lucide="message-circle" class="w-3 h-3 text-emerald-600"></i>
                                Template WhatsApp aktif terpasang
                            </span>
                        </div>
                    @endif

                </div>

                <!-- Card Footer Actions -->
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                    <form method="POST" action="{{ route('admin.risiko.reset', $item) }}" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan teks {{ $item->nama_risiko }} ke pengaturan standar default?')">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-white hover:bg-slate-100 border border-slate-200 text-slate-600 hover:text-slate-900 text-xs font-semibold transition flex items-center space-x-1 shadow-2xs" title="Kembalikan ke Teks Standar">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span>Reset Default</span>
                        </button>
                    </form>

                    <a href="{{ route('admin.risiko.edit', $item) }}" class="px-4 py-1.5 rounded-lg bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-xs hover:shadow-sm transition flex items-center space-x-1.5">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        <span>Edit Teks Peringatan</span>
                    </a>
                </div>

            </div>
        @endforeach
    </div>

</div>
@endsection
