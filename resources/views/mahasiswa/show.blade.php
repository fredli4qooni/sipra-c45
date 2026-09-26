@extends('layouts.app')

@section('title', 'Detail Mahasiswa: ' . $mahasiswa->nama)
@section('subtitle', 'Profil identitas & rekam jejak akademik per semester')

@section('content')
<div class="space-y-6">

    <!-- Top Action Toolbar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.mahasiswa.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1.5"></i>
            Kembali ke Daftar Mahasiswa
        </a>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.mahasiswa.edit', $mahasiswa) }}" class="px-3.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition flex items-center">
                <i data-lucide="pencil" class="w-3.5 h-3.5 mr-1.5"></i>
                Ubah Profil
            </a>
            <a href="{{ route('admin.prediksi.single') }}?mahasiswa_id={{ $mahasiswa->id }}" class="px-3.5 py-1.5 rounded-lg bg-brand-600 hover:bg-brand-500 text-xs font-bold text-white shadow-xs transition flex items-center">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 mr-1.5"></i>
                Simulasi Prediksi C4.5
            </a>
        </div>
    </div>

    <!-- Student Profile Header Card -->
    <div class="bg-white p-6 sm:p-8 rounded-xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center md:items-start justify-between gap-6">
        <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-5 text-center sm:text-left">
            <div class="w-16 h-16 rounded-lg bg-brand-50 border border-brand-200 text-brand-700 font-extrabold text-2xl flex items-center justify-center shadow-xs flex-shrink-0">
                {{ strtoupper(substr($mahasiswa->nama, 0, 2)) }}
            </div>
            <div>
                <h3 class="text-xl font-extrabold text-slate-900">{{ $mahasiswa->nama }}</h3>
                <p class="text-sm font-mono text-brand-700 font-bold mt-0.5">{{ $mahasiswa->nim }}</p>
                <p class="text-xs text-slate-500 mt-1">
                    Angkatan {{ $mahasiswa->angkatan }} • Jenis Kelamin: {{ $mahasiswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }} • Jalur {{ $mahasiswa->jalur_masuk ?? '-' }}
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200 text-center min-w-[120px]">
                <p class="text-[10px] text-slate-400 uppercase font-bold">Status Studi</p>
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold mt-1
                    @if($mahasiswa->status_mahasiswa === 'Aktif') bg-brand-50 text-brand-700 border border-brand-200
                    @elseif($mahasiswa->status_mahasiswa === 'Cuti') bg-amber-50 text-amber-700 border border-amber-200
                    @else bg-rose-50 text-rose-700 border border-rose-200
                    @endif">
                    {{ $mahasiswa->status_mahasiswa }}
                </span>
            </div>
        </div>
    </div>

    <!-- Academic History Table Card -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-2.5">
                <i data-lucide="graduation-cap" class="w-4 h-4 text-brand-600"></i>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Rekam Riwayat Akademik Per Semester</h4>
            </div>
            <a href="{{ route('admin.akademik.create') }}?mahasiswa_id={{ $mahasiswa->id }}" class="text-xs font-bold text-brand-700 hover:text-brand-800 hover:underline">
                + Entri Nilai Semester Baru
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100">
                        <th class="pb-2.5 font-semibold">Semester</th>
                        <th class="pb-2.5 font-semibold">Tahun Akademik</th>
                        <th class="pb-2.5 font-semibold">IPS</th>
                        <th class="pb-2.5 font-semibold">IPK Kumulatif</th>
                        <th class="pb-2.5 font-semibold">SKS Diambil</th>
                        <th class="pb-2.5 font-semibold">SKS Gagal</th>
                        <th class="pb-2.5 font-semibold">Kehadiran</th>
                        <th class="pb-2.5 font-semibold">Status Cuti</th>
                        <th class="pb-2.5 font-semibold">Klasifikasi Risiko</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($mahasiswa->dataAkademiks()->orderBy('semester', 'asc')->get() as $akd)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 font-bold text-slate-900">Semester {{ $akd->semester }}</td>
                            <td class="py-3 text-slate-600">{{ $akd->tahun_akademik }}</td>
                            <td class="py-3 text-slate-700">{{ $akd->ips }}</td>
                            <td class="py-3 font-bold text-slate-900">{{ $akd->ipk }}</td>
                            <td class="py-3 text-slate-600">{{ $akd->sks_semester }} SKS</td>
                            <td class="py-3 {{ $akd->sks_tidak_lulus > 0 ? 'text-rose-600 font-bold' : 'text-slate-400' }}">
                                {{ $akd->sks_tidak_lulus }} SKS
                            </td>
                            <td class="py-3 font-semibold {{ $akd->persentase_kehadiran < 75 ? 'text-rose-600' : 'text-slate-700' }}">
                                {{ $akd->persentase_kehadiran }}%
                            </td>
                            <td class="py-3 text-slate-600">{{ $akd->status_cuti ? 'Ya' : 'Tidak' }}</td>
                            <td class="py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold
                                    @if($akd->label_risiko_aktual === 'Risiko Rendah') bg-brand-50 text-brand-700 border border-brand-200
                                    @elseif($akd->label_risiko_aktual === 'Risiko Sedang') bg-amber-50 text-amber-700 border border-amber-200
                                    @else bg-rose-50 text-rose-700 border border-rose-200
                                    @endif">
                                    {{ $akd->label_risiko_aktual }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">Belum ada riwayat data akademik untuk mahasiswa ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
