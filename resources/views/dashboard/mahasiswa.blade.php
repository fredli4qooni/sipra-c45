@extends('layouts.app')

@section('title', 'Portal Akademik Mahasiswa')
@section('subtitle', 'Evaluasi Diri & Deteksi Dini Risiko Akademik')

@section('content')
<div class="space-y-6">

    @if(!$mahasiswa)
        <div class="p-6 rounded-3xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-center space-x-3">
            <i data-lucide="alert-triangle" class="w-6 h-6 flex-shrink-0 text-amber-600"></i>
            <div>
                <h4 class="font-bold">Data Mahasiswa Belum Ditautkan</h4>
                <p class="text-xs text-slate-600 mt-0.5">Akun Anda belum terhubung dengan data master mahasiswa. Silakan hubungi Admin Akademik untuk sinkronisasi NIM.</p>
            </div>
        </div>
    @else
        <!-- Student Info Header Card -->
        <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center md:items-start justify-between gap-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-5 text-center sm:text-left">
                <div class="w-16 h-16 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 font-extrabold text-2xl flex items-center justify-center shadow-xs flex-shrink-0">
                    {{ strtoupper(substr($mahasiswa->nama, 0, 2)) }}
                </div>
                <div>
                    <h3 class="text-xl font-extrabold text-slate-900">{{ $mahasiswa->nama }}</h3>
                    <p class="text-sm font-mono text-brand-700 font-bold mt-0.5">{{ $mahasiswa->nim }}</p>
                    <p class="text-xs text-slate-500 mt-1">
                        Program Studi Sistem Informasi • Angkatan {{ $mahasiswa->angkatan }} • Jalur {{ $mahasiswa->jalur_masuk ?? '-' }}
                    </p>
                </div>
            </div>

            <!-- Prominent Risk Status Badge -->
            @php
                $statusRisiko = $latestAkademik->label_risiko_aktual ?? 'Belum Dianalisis';
            @endphp
            <div class="p-4 rounded-2xl border text-center min-w-[200px]
                @if($statusRisiko === 'Risiko Rendah') bg-brand-50 border-brand-200 text-brand-800
                @elseif($statusRisiko === 'Risiko Sedang') bg-amber-50 border-amber-200 text-amber-800
                @elseif($statusRisiko === 'Risiko Tinggi') bg-rose-50 border-rose-200 text-rose-800
                @else bg-slate-50 border-slate-200 text-slate-600
                @endif">
                <p class="text-[10px] uppercase tracking-wider font-bold">Status Deteksi Dini</p>
                <h4 class="text-lg font-black mt-0.5">{{ $statusRisiko }}</h4>
                <p class="text-[10px] text-slate-500 mt-0.5">Semester {{ $latestAkademik->semester ?? '-' }}</p>
            </div>
        </div>

        <!-- Academic Performance Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            <!-- IPK -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs text-center space-y-2">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">IPK Kumulatif</p>
                <h4 class="text-3xl font-extrabold text-slate-900">{{ $latestAkademik->ipk ?? '0.00' }}</h4>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold
                    @if(($latestAkademik->ipk ?? 0) >= 3.25) bg-brand-50 text-brand-700 border border-brand-200
                    @elseif(($latestAkademik->ipk ?? 0) >= 2.75) bg-amber-50 text-amber-700 border border-amber-200
                    @else bg-rose-50 text-rose-700 border border-rose-200
                    @endif">
                    Kategori: {{ $latestAkademik->kategori_ipk ?? '-' }}
                </span>
            </div>

            <!-- SKS Selesai -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs text-center space-y-2">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">SKS Semester</p>
                <h4 class="text-3xl font-extrabold text-slate-900">{{ $latestAkademik->sks_semester ?? 0 }} SKS</h4>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    SKS Gagal: {{ $latestAkademik->sks_tidak_lulus ?? 0 }}
                </span>
            </div>

            <!-- Kehadiran -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs text-center space-y-2">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Kehadiran Kuliah</p>
                <h4 class="text-3xl font-extrabold text-slate-900">{{ $latestAkademik->persentase_kehadiran ?? 0 }}%</h4>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold
                    @if(($latestAkademik->persentase_kehadiran ?? 0) >= 85) bg-brand-50 text-brand-700 border border-brand-200
                    @elseif(($latestAkademik->persentase_kehadiran ?? 0) >= 75) bg-amber-50 text-amber-700 border border-amber-200
                    @else bg-rose-50 text-rose-700 border border-rose-200
                    @endif">
                    Kategori: {{ $latestAkademik->kategori_kehadiran ?? '-' }}
                </span>
            </div>

        </div>

        <!-- Academic Recommendations Box -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-3">
            <div class="flex items-center space-x-2 pb-2 border-b border-slate-100">
                <i data-lucide="lightbulb" class="w-4 h-4 text-amber-500"></i>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Rekomendasi Rencana Studi</h4>
            </div>

            <div class="p-4 rounded-2xl bg-brand-50/50 border border-brand-200/80 text-xs text-slate-700 leading-relaxed">
                @if($statusRisiko === 'Risiko Rendah')
                    <p class="font-semibold text-brand-900 mb-1">🎉 Pertahankan Konsistensi Belajar!</p>
                    <p>Performa akademik Anda sangat memuaskan. Anda berada di jalur yang tepat untuk lulus tepat waktu pada semester 8. Disarankan untuk mulai merancang topik proposal skripsi/tugas akhir dan aktif mengikuti kegiatan magang atau konferensi ilmiah.</p>
                @elseif($statusRisiko === 'Risiko Sedang')
                    <p class="font-semibold text-amber-900 mb-1">⚠️ Perhatian & Pendampingan</p>
                    <p>Terdapat beberapa indikator performa yang perlu ditingkatkan, seperti absensi kehadiran perkuliahan atau perbaikan mata kuliah. Disarankan untuk menjadwalkan sesi bimbingan bersama Dosen PA Anda.</p>
                @else
                    <p class="font-semibold text-rose-900 mb-1">🚨 Peringatan Dini Akademik</p>
                    <p>Performa akademik Anda terindikasi memiliki risiko keterlambatan studi. Segera hubungi Dosen Pembimbing Akademik atau Ketua Program Studi untuk menyusun rencana perbaikan nilai dan konsultasi khusus.</p>
                @endif
            </div>
        </div>

    @endif

</div>
@endsection
