@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi Risiko Akademik')
@section('subtitle', 'Rekapitulasi analisis risiko mahasiswa untuk evaluasi prodi & pelaporan akademik')

@section('content')
<div class="space-y-6">

    <!-- Filter & Action Card -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Filter & Rekapitulasi Data</h3>
                <p class="text-xs text-slate-500">Total data tersaring: <strong class="text-brand-700 font-mono">{{ $records->total() }} Mahasiswa</strong></p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @php
                    $queryString = request()->getQueryString();
                    $printUrl = (auth()->user()->isAdmin() ? route('admin.laporan.print') : route('prodi.laporan.print')) . ($queryString ? '?' . $queryString : '');
                    $exportUrl = (auth()->user()->isAdmin() ? route('admin.laporan.export') : route('prodi.laporan.export')) . ($queryString ? '?' . $queryString : '');
                @endphp

                <!-- Export Excel -->
                <a href="{{ $exportUrl }}" class="px-3.5 py-2 rounded-xl bg-brand-50 hover:bg-brand-100 text-xs font-bold text-brand-700 border border-brand-200 transition flex items-center">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-1.5 text-brand-600"></i>
                    Ekspor Excel
                </a>

                <!-- Print Report -->
                <a href="{{ $printUrl }}" target="_blank" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-xs font-bold text-white shadow-xs hover:shadow-sm transition flex items-center">
                    <i data-lucide="printer" class="w-4 h-4 mr-1.5"></i>
                    Cetak Laporan Resmi (PDF)
                </a>
            </div>
        </div>

        @php
            $laporanIndexRoute = auth()->user()->isAdmin() ? route('admin.laporan.index') : route('prodi.laporan.index');
        @endphp

        <!-- Filter Form -->
        <form method="GET" action="{{ $laporanIndexRoute }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-2">
            <!-- Filter Angkatan -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Angkatan</label>
                <select name="angkatan" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    <option value="">Semua Angkatan</option>
                    @foreach($angkatans as $akt)
                        <option value="{{ $akt }}" {{ request('angkatan') == $akt ? 'selected' : '' }}>Angkatan {{ $akt }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Semester -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Semester</label>
                <select name="semester" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    <option value="">Semua Semester</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem }}" {{ request('semester') == $sem ? 'selected' : '' }}>Semester {{ $sem }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status Risiko -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Tingkat Risiko</label>
                <select name="label_risiko_aktual" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    <option value="">Semua Tingkat Risiko</option>
                    <option value="Risiko Rendah" {{ request('label_risiko_aktual') === 'Risiko Rendah' ? 'selected' : '' }}>🟢 Risiko Rendah</option>
                    <option value="Risiko Sedang" {{ request('label_risiko_aktual') === 'Risiko Sedang' ? 'selected' : '' }}>🟡 Risiko Sedang</option>
                    <option value="Risiko Tinggi" {{ request('label_risiko_aktual') === 'Risiko Tinggi' ? 'selected' : '' }}>🔴 Risiko Tinggi</option>
                </select>
            </div>

            <!-- Filter Jalur Masuk -->
            <div class="flex items-end space-x-2">
                <div class="flex-1">
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Jalur Masuk</label>
                    <select name="jalur_masuk" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        <option value="">Semua Jalur</option>
                        <option value="SPAN-PTKIN" {{ request('jalur_masuk') === 'SPAN-PTKIN' ? 'selected' : '' }}>SPAN-PTKIN</option>
                        <option value="UM-PTKIN" {{ request('jalur_masuk') === 'UM-PTKIN' ? 'selected' : '' }}>UM-PTKIN</option>
                        <option value="SNBP" {{ request('jalur_masuk') === 'SNBP' ? 'selected' : '' }}>SNBP</option>
                        <option value="SNBT" {{ request('jalur_masuk') === 'SNBT' ? 'selected' : '' }}>SNBT</option>
                        <option value="Mandiri" {{ request('jalur_masuk') === 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                    </select>
                </div>
                @if(request()->hasAny(['angkatan', 'semester', 'label_risiko_aktual', 'jalur_masuk']))
                    <a href="{{ $laporanIndexRoute }}" title="Reset Filter" class="p-2 rounded-xl bg-slate-100 text-slate-500 hover:text-slate-900 transition mb-0.5">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs text-center space-y-1">
            <p class="text-[11px] font-medium text-slate-500 uppercase">Total Sampel Laporan</p>
            <h4 class="text-2xl font-black text-slate-900">{{ $totalRecords }}</h4>
        </div>
        <div class="p-4 rounded-2xl bg-brand-50/60 border border-brand-200/80 text-center space-y-1">
            <p class="text-[11px] font-bold text-brand-800 uppercase">🟢 Risiko Rendah</p>
            <h4 class="text-2xl font-black text-slate-900">{{ $totalRendah }}</h4>
        </div>
        <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/80 text-center space-y-1">
            <p class="text-[11px] font-bold text-amber-800 uppercase">🟡 Risiko Sedang</p>
            <h4 class="text-2xl font-black text-slate-900">{{ $totalSedang }}</h4>
        </div>
        <div class="p-4 rounded-2xl bg-rose-50/60 border border-rose-200/80 text-center space-y-1">
            <p class="text-[11px] font-bold text-rose-800 uppercase">🔴 Risiko Tinggi</p>
            <h4 class="text-2xl font-black text-slate-900">{{ $totalTinggi }}</h4>
        </div>
    </div>

    <!-- Table Preview -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100">
                        <th class="pb-3 font-semibold">No</th>
                        <th class="pb-3 font-semibold">NIM</th>
                        <th class="pb-3 font-semibold">Nama Mahasiswa</th>
                        <th class="pb-3 font-semibold">Angkatan</th>
                        <th class="pb-3 font-semibold">Semester</th>
                        <th class="pb-3 font-semibold">IPS</th>
                        <th class="pb-3 font-semibold">IPK</th>
                        <th class="pb-3 font-semibold">SKS</th>
                        <th class="pb-3 font-semibold">Kehadiran</th>
                        <th class="pb-3 font-semibold">Cuti</th>
                        <th class="pb-3 font-semibold">Klasifikasi Risiko</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $index => $row)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 text-slate-400">{{ $records->firstItem() + $index }}</td>
                            <td class="py-3 font-mono font-bold text-brand-700">{{ $row->mahasiswa->nim }}</td>
                            <td class="py-3 font-semibold text-slate-900">{{ $row->mahasiswa->nama }}</td>
                            <td class="py-3 text-slate-500">{{ $row->mahasiswa->angkatan }}</td>
                            <td class="py-3 text-slate-600 font-medium">Sem {{ $row->semester }}</td>
                            <td class="py-3 text-slate-700">{{ $row->ips }}</td>
                            <td class="py-3 font-bold text-slate-900">{{ $row->ipk }}</td>
                            <td class="py-3 text-slate-600">{{ $row->sks_semester }} SKS</td>
                            <td class="py-3 font-semibold {{ $row->persentase_kehadiran < 75 ? 'text-rose-600' : 'text-brand-700' }}">
                                {{ $row->persentase_kehadiran }}%
                            </td>
                            <td class="py-3 text-slate-600">{{ $row->status_cuti ? 'Ya' : 'Tidak' }}</td>
                            <td class="py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold
                                    @if($row->label_risiko_aktual === 'Risiko Rendah') bg-brand-50 text-brand-700 border border-brand-200/60
                                    @elseif($row->label_risiko_aktual === 'Risiko Sedang') bg-amber-50 text-amber-700 border border-amber-200/60
                                    @else bg-rose-50 text-rose-700 border border-rose-200/60
                                    @endif">
                                    {{ $row->label_risiko_aktual }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-8 text-center text-slate-400">Tidak ada data yang sesuai dengan kriteria filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100">
            {{ $records->links() }}
        </div>
    </div>

</div>
@endsection
