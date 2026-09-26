@extends('layouts.app')

@section('title', 'Early Warning System (EWS) Alert Center')
@section('subtitle', 'Pusat deteksi dini & pemantauan mahasiswa berpotensi mengalami hambatan akademik / DO')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-white p-6 sm:p-8 rounded-xl border border-rose-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5 animate-ping"></span>
                    EWS Prioritas Bimbingan
                </span>
            </div>
            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Peringatan Dini Akademik Mahasiswa</h3>
            <p class="text-xs text-slate-500 max-w-2xl leading-relaxed">
                Daftar mahasiswa yang memerlukan perhatian segera berdasarkan indikator kritis C4.5: penurunan IPK di bawah standar (&lt;2.75), kehadiran rendah (&lt;75%), status cuti, atau penumpukan SKS tidak lulus.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <div class="p-4 rounded-lg bg-rose-50 border border-rose-200 text-center min-w-[130px]">
                <p class="text-[10px] text-rose-700 uppercase font-bold tracking-wider">Perlu Intervensi</p>
                <h4 class="text-3xl font-black text-rose-800 mt-0.5">{{ $alertList->total() }}</h4>
                <span class="text-[10px] text-rose-600 font-medium">Mahasiswa</span>
            </div>
        </div>
    </div>

    <!-- Metric Breakdown Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg border border-rose-200/80 shadow-xs text-center space-y-1">
            <p class="text-[11px] font-bold text-rose-700 uppercase">Risiko Tinggi Aktual</p>
            <h4 class="text-2xl font-black text-slate-900">{{ $criticalCount }}</h4>
            <p class="text-[10px] text-slate-400">Kategori Prioritas 1</p>
        </div>

        <div class="bg-white p-4 rounded-lg border border-amber-200/80 shadow-xs text-center space-y-1">
            <p class="text-[11px] font-bold text-amber-700 uppercase">Kehadiran &lt; 75%</p>
            <h4 class="text-2xl font-black text-slate-900">{{ $attendanceRiskCount }}</h4>
            <p class="text-[10px] text-slate-400">Batas Minimal Ujian</p>
        </div>

        <div class="bg-white p-4 rounded-lg border border-yellow-200/80 shadow-xs text-center space-y-1">
            <p class="text-[11px] font-bold text-yellow-700 uppercase">IPK &lt; 2.75</p>
            <h4 class="text-2xl font-black text-slate-900">{{ $gpaRiskCount }}</h4>
            <p class="text-[10px] text-slate-400">Kategori IPK Rendah</p>
        </div>

        <div class="bg-white p-4 rounded-lg border border-blue-200/80 shadow-xs text-center space-y-1">
            <p class="text-[11px] font-bold text-blue-700 uppercase">Status Cuti Studi</p>
            <h4 class="text-2xl font-black text-slate-900">{{ $cutiCount }}</h4>
            <p class="text-[10px] text-slate-400">Dalam Masa Penundaan</p>
        </div>
    </div>

    <!-- Alert List Table Card -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-2.5">
                <i data-lucide="shield-alert" class="w-4 h-4 text-rose-600"></i>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Daftar Mahasiswa Terindikasi Masalah Akademik</h4>
            </div>

            <!-- Filter Semester -->
            @php
                $ewsIndexRoute = auth()->user()->isAdmin() ? route('admin.ews.index') : route('prodi.ews.index');
            @endphp
            <form method="GET" action="{{ $ewsIndexRoute }}" class="flex items-center space-x-2">
                <select name="semester" onchange="this.form.submit()" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                    <option value="">Semua Semester</option>
                    @for($s = 1; $s <= 8; $s++)
                        <option value="{{ $s }}" {{ request('semester') == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                    @endfor
                </select>
                @if(request()->has('semester'))
                    <a href="{{ $ewsIndexRoute }}" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:text-slate-900 transition">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100">
                        <th class="pb-3 font-semibold">NIM</th>
                        <th class="pb-3 font-semibold">Nama Mahasiswa</th>
                        <th class="pb-3 font-semibold">Semester</th>
                        <th class="pb-3 font-semibold">IPK</th>
                        <th class="pb-3 font-semibold">Kehadiran</th>
                        <th class="pb-3 font-semibold">SKS Gagal</th>
                        <th class="pb-3 font-semibold">Faktor Pemicu Risiko</th>
                        <th class="pb-3 font-semibold text-right">Tindakan Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($alertList as $akd)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 font-mono font-bold text-rose-700">{{ $akd->mahasiswa->nim }}</td>
                            <td class="py-3.5 font-semibold text-slate-900">{{ $akd->mahasiswa->nama }}</td>
                            <td class="py-3.5 text-slate-600 font-medium">Sem {{ $akd->semester }}</td>
                            <td class="py-3.5 font-bold {{ $akd->ipk < 2.75 ? 'text-rose-600' : 'text-slate-900' }}">{{ $akd->ipk }}</td>
                            <td class="py-3.5 font-semibold {{ $akd->persentase_kehadiran < 75 ? 'text-rose-600' : 'text-slate-700' }}">
                                {{ $akd->persentase_kehadiran }}%
                            </td>
                            <td class="py-3.5 text-slate-700 {{ $akd->sks_tidak_lulus > 0 ? 'text-rose-600 font-bold' : '' }}">
                                {{ $akd->sks_tidak_lulus }} SKS
                            </td>
                            <td class="py-3.5">
                                @if($akd->status_cuti)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Sedang Cuti</span>
                                @elseif($akd->ipk < 2.75 && $akd->persentase_kehadiran < 75)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">IPK & Kehadiran Rendah</span>
                                @elseif($akd->ipk < 2.75)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">IPK &lt; 2.75</span>
                                @elseif($akd->persentase_kehadiran < 75)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Kehadiran &lt; 75%</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">SKS Mengulang</span>
                                @endif
                            </td>
                            <td class="py-3.5 text-right">
                                @php
                                    $prediksiSingleUrl = (auth()->user()->isAdmin() ? route('admin.prediksi.single') : route('prodi.prediksi.single')) . '?mahasiswa_id=' . $akd->mahasiswa_id;
                                @endphp
                                <a href="{{ $prediksiSingleUrl }}" class="inline-flex items-center px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] border border-rose-200 transition">
                                    <i data-lucide="sparkles" class="w-3 h-3 mr-1"></i>
                                    Analisis C4.5
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Tidak ada mahasiswa yang memenuhi kriteria peringatan dini pada filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100">
            {{ $alertList->links() }}
        </div>
    </div>

</div>
@endsection
