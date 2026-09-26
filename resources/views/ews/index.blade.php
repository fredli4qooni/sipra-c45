@extends('layouts.app')

@section('title', 'Early Warning System (EWS) Alert Center')
@section('subtitle', 'Pusat deteksi dini & pencatatan bimbingan intervensi akademik Dosen PA')

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
                Pemantauan mahasiswa yang terindikasi mengalami kendala studi berdasarkan Decision Tree C4.5: penurunan IPK (&lt;2.75), kehadiran rendah (&lt;75%), status cuti, atau penumpukan SKS tidak lulus, lengkap dengan modul tindak lanjut bimbingan Dosen PA.
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

    <!-- 5 Metric Breakdown Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
        <div class="bg-white p-4 rounded-lg border border-rose-200/80 shadow-xs text-center space-y-1">
            <p class="text-[11px] font-bold text-rose-700 uppercase">Risiko Tinggi Aktual</p>
            <h4 class="text-2xl font-black text-slate-900">{{ $criticalCount }}</h4>
            <p class="text-[10px] text-slate-400">Prioritas Tingkat 1</p>
        </div>

        <div class="bg-white p-4 rounded-lg border border-amber-200/80 shadow-xs text-center space-y-1">
            <p class="text-[11px] font-bold text-amber-700 uppercase">IPK &lt; 2.75</p>
            <h4 class="text-2xl font-black text-slate-900">{{ $gpaRiskCount }}</h4>
            <p class="text-[10px] text-slate-400">Kategori Rendah</p>
        </div>

        <div class="bg-white p-4 rounded-lg border border-yellow-200/80 shadow-xs text-center space-y-1">
            <p class="text-[11px] font-bold text-yellow-700 uppercase">Kehadiran &lt; 75%</p>
            <h4 class="text-2xl font-black text-slate-900">{{ $attendanceRiskCount }}</h4>
            <p class="text-[10px] text-slate-400">Batas Minimal Ujian</p>
        </div>

        <div class="bg-white p-4 rounded-lg border border-rose-200 shadow-xs text-center space-y-1 bg-rose-50/30">
            <p class="text-[11px] font-bold text-rose-700 uppercase">Belum Ditindaklanjuti</p>
            <h4 class="text-2xl font-black text-rose-800">{{ $pendingInterventionCount }}</h4>
            <p class="text-[10px] text-rose-600 font-medium">Menunggu Tindakan PA</p>
        </div>

        <div class="bg-white p-4 rounded-lg border border-brand-200 shadow-xs text-center space-y-1 bg-brand-50/30">
            <p class="text-[11px] font-bold text-brand-700 uppercase">Bimbingan Selesai</p>
            <h4 class="text-2xl font-black text-brand-800">{{ $completedInterventionCount }}</h4>
            <p class="text-[10px] text-brand-600 font-medium">Intervensi Teratasi</p>
        </div>
    </div>

    @php
        $ewsIndexRoute = auth()->user()->isAdmin() ? route('admin.ews.index') : route('prodi.ews.index');
    @endphp

    <!-- Dosen PA / Scope Navigation Pills -->
    @if(auth()->user()->isDosenPa())
        <div class="flex items-center space-x-2 bg-slate-100/80 p-1.5 rounded-xl max-w-fit border border-slate-200/80">
            <a href="{{ $ewsIndexRoute }}?scope=bimbingan_saya" 
               class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center {{ $scope === 'bimbingan_saya' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                <i data-lucide="star" class="w-3.5 h-3.5 mr-1.5 {{ $scope === 'bimbingan_saya' ? 'fill-amber-400 text-amber-500' : 'text-slate-400' }}"></i>
                Mahasiswa Bimbingan Saya
                <span class="ml-2 px-1.5 py-0.5 rounded-md text-[10px] font-bold {{ $scope === 'bimbingan_saya' ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-600' }}">
                    {{ $myBimbinganCount }}
                </span>
            </a>

            <a href="{{ $ewsIndexRoute }}?scope=semua" 
               class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center {{ $scope === 'semua' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                <i data-lucide="users" class="w-3.5 h-3.5 mr-1.5 text-slate-400"></i>
                Semua Mahasiswa Prodi
                <span class="ml-2 px-1.5 py-0.5 rounded-md text-[10px] font-bold {{ $scope === 'semua' ? 'bg-slate-200 text-slate-800' : 'bg-slate-200 text-slate-600' }}">
                    {{ $allAlertCount }}
                </span>
            </a>
        </div>
    @endif

    <!-- Alert List Table Card -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-2.5">
                <i data-lucide="shield-alert" class="w-4 h-4 text-rose-600"></i>
                <div>
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                        @if(auth()->user()->isDosenPa() && $scope === 'bimbingan_saya')
                            Daftar Mahasiswa Bimbingan Anda (Perlu Pemantauan EWS)
                        @else
                            Daftar Mahasiswa Terindikasi Masalah Akademik
                        @endif
                    </h4>
                    <p class="text-[11px] text-slate-500">Klik "Bimbingan PA" untuk memperbarui status tindak lanjut dan mencatat hasil konsultasi</p>
                </div>
            </div>

            <!-- Filter Toolbar -->
            <form method="GET" action="{{ $ewsIndexRoute }}" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="scope" value="{{ $scope }}">

                <!-- Dosen PA Filter (For Admin, Prodi, or all students view) -->
                @if(!auth()->user()->isDosenPa() || $scope === 'semua')
                    <select name="dosen_pa_id" onchange="this.form.submit()" class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                        <option value="">Semua Dosen PA</option>
                        <option value="unassigned" {{ request('dosen_pa_id') == 'unassigned' ? 'selected' : '' }}>Belum Ada PA</option>
                        @foreach($dosenPas as $dosen)
                            <option value="{{ $dosen->id }}" {{ request('dosen_pa_id') == $dosen->id ? 'selected' : '' }}>PA: {{ Str::limit($dosen->name, 16) }}</option>
                        @endforeach
                    </select>
                @endif

                <!-- Semester Filter -->
                <select name="semester" onchange="this.form.submit()" class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                    <option value="">Semua Semester</option>
                    @for($s = 1; $s <= 8; $s++)
                        <option value="{{ $s }}" {{ request('semester') == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                    @endfor
                </select>

                <!-- Status Intervensi Filter -->
                <select name="status_intervensi" onchange="this.form.submit()" class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                    <option value="">Semua Status Bimbingan</option>
                    <option value="Belum Ditindaklanjuti" {{ request('status_intervensi') == 'Belum Ditindaklanjuti' ? 'selected' : '' }}>Belum Ditindaklanjuti</option>
                    <option value="Dijadwalkan Bimbingan" {{ request('status_intervensi') == 'Dijadwalkan Bimbingan' ? 'selected' : '' }}>Dijadwalkan Bimbingan</option>
                    <option value="Sedang Bimbingan" {{ request('status_intervensi') == 'Sedang Bimbingan' ? 'selected' : '' }}>Sedang Bimbingan</option>
                    <option value="Selesai / Teratasi" {{ request('status_intervensi') == 'Selesai / Teratasi' ? 'selected' : '' }}>Selesai / Teratasi</option>
                </select>

                @if(request()->hasAny(['semester', 'status_intervensi', 'dosen_pa_id', 'angkatan']))
                    <a href="{{ $ewsIndexRoute }}?scope={{ $scope }}" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:text-slate-900 transition" title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100">
                        <th class="pb-3 font-semibold w-24">NIM</th>
                        <th class="pb-3 font-semibold">Nama Mahasiswa</th>
                        <th class="pb-3 font-semibold">Dosen PA</th>
                        <th class="pb-3 font-semibold">Semester</th>
                        <th class="pb-3 font-semibold">IPK / IPS</th>
                        <th class="pb-3 font-semibold">Kehadiran</th>
                        <th class="pb-3 font-semibold">Faktor Masalah</th>
                        <th class="pb-3 font-semibold">Status Intervensi PA</th>
                        <th class="pb-3 font-semibold text-right">Aksi Tindak Lanjut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($alertList as $akd)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 font-mono font-bold text-rose-700">{{ $akd->mahasiswa->nim }}</td>
                            <td class="py-3">
                                <div class="flex items-center gap-1.5">
                                    <p class="font-bold text-slate-900">{{ $akd->mahasiswa->nama }}</p>
                                    @if(auth()->user()->isDosenPa() && $akd->mahasiswa->dosen_pa_id === auth()->id())
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200 flex-shrink-0" title="Mahasiswa bimbingan Anda">
                                            <i data-lucide="star" class="w-2.5 h-2.5 mr-0.5 fill-amber-400"></i> Bimbingan Anda
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[10px] text-slate-400">Angkatan {{ $akd->mahasiswa->angkatan ?? '-' }}</p>
                            </td>
                            <td class="py-3">
                                @if($akd->mahasiswa->dosenPa)
                                    <span class="text-slate-700 font-medium text-[11px] block truncate max-w-[130px]" title="{{ $akd->mahasiswa->dosenPa->name }}">
                                        {{ $akd->mahasiswa->dosenPa->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-[10px]">Belum diatur</span>
                                @endif
                            </td>
                            <td class="py-3 text-slate-700 font-medium">Semester {{ $akd->semester }}</td>
                            <td class="py-3">
                                <span class="font-bold {{ $akd->ipk < 2.75 ? 'text-rose-600' : 'text-slate-900' }}">{{ $akd->ipk }}</span>
                                <span class="text-[10px] text-slate-400 block font-mono">IPS: {{ $akd->ips }}</span>
                            </td>
                            <td class="py-3 font-semibold {{ $akd->persentase_kehadiran < 75 ? 'text-rose-600' : 'text-slate-700' }}">
                                {{ $akd->persentase_kehadiran }}%
                            </td>
                            <td class="py-3">
                                @if($akd->status_cuti)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Sedang Cuti</span>
                                @elseif($akd->ipk < 2.75 && $akd->persentase_kehadiran < 75)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">IPK & Kehadiran Rendah</span>
                                @elseif($akd->ipk < 2.75)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">IPK &lt; 2.75</span>
                                @elseif($akd->persentase_kehadiran < 75)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Kehadiran &lt; 75%</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">{{ $akd->sks_tidak_lulus }} SKS Mengulang</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @php
                                    $st = $akd->status_intervensi ?? 'Belum Ditindaklanjuti';
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold
                                    @if($st === 'Selesai / Teratasi') bg-brand-50 text-brand-700 border border-brand-200
                                    @elseif($st === 'Sedang Bimbingan') bg-blue-50 text-blue-700 border border-blue-200
                                    @elseif($st === 'Dijadwalkan Bimbingan') bg-amber-50 text-amber-700 border border-amber-200
                                    @else bg-rose-50 text-rose-700 border border-rose-200
                                    @endif">
                                    @if($st === 'Selesai / Teratasi')
                                        <i data-lucide="check-circle" class="w-3 h-3 text-brand-600 flex-shrink-0"></i>
                                    @elseif($st === 'Sedang Bimbingan')
                                        <i data-lucide="clock" class="w-3 h-3 text-blue-600 flex-shrink-0"></i>
                                    @elseif($st === 'Dijadwalkan Bimbingan')
                                        <i data-lucide="calendar" class="w-3 h-3 text-amber-600 flex-shrink-0"></i>
                                    @else
                                        <i data-lucide="alert-circle" class="w-3 h-3 text-rose-600 flex-shrink-0"></i>
                                    @endif
                                    <span>{{ $st }}</span>
                                </span>
                                @if($akd->tindakan_intervensi)
                                    <p class="text-[10px] text-slate-600 mt-1 truncate max-w-[180px]" title="{{ $akd->tindakan_intervensi }}">
                                        {{ $akd->tindakan_intervensi }}
                                    </p>
                                @endif
                                @if($akd->dosenPa)
                                    <p class="text-[9px] text-slate-400 mt-0.5">Oleh: {{ $akd->dosenPa->name }}</p>
                                @endif
                            </td>
                            <td class="py-3 text-right">
                                <div class="inline-flex items-center space-x-1.5">
                                    <button type="button" onclick="openEwsModal('ews-modal-{{ $akd->id }}')" class="inline-flex items-center px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] border border-indigo-200 transition">
                                        <i data-lucide="clipboard-edit" class="w-3 h-3 mr-1"></i>
                                        Bimbingan PA
                                    </button>

                                    @php
                                        $prediksiSingleUrl = (auth()->user()->isAdmin() ? route('admin.prediksi.single') : route('prodi.prediksi.single')) . '?mahasiswa_id=' . $akd->mahasiswa_id;
                                    @endphp
                                    <a href="{{ $prediksiSingleUrl }}" class="inline-flex items-center px-2 py-1 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-[11px] border border-slate-200 transition" title="Simulasi Prediksi C4.5">
                                        <i data-lucide="sparkles" class="w-3 h-3 text-purple-600"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">Tidak ada mahasiswa yang memenuhi kriteria peringatan dini pada filter ini.</td>
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

<!-- Counseling Modals for Each Alerted Student -->
@foreach($alertList as $akd)
    @php
        $updateRoute = auth()->user()->isAdmin() ? route('admin.ews.intervensi.update', $akd) : route('prodi.ews.intervensi.update', $akd);
    @endphp
    <div id="ews-modal-{{ $akd->id }}" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-xl max-w-lg w-full overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            
            <!-- Modal Header -->
            <div class="px-5 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-700 flex items-center justify-center">
                        <i data-lucide="clipboard-edit" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 uppercase">Tindak Lanjut Bimbingan Akademik (Dosen PA)</h4>
                        <p class="text-[10px] text-slate-500">Pencatatan konseling intervensi mahasiswa EWS</p>
                    </div>
                </div>
                <button type="button" onclick="closeEwsModal('ews-modal-{{ $akd->id }}')" class="text-slate-400 hover:text-slate-600 p-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Student Summary Context -->
            <div class="p-4 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between text-xs">
                <div>
                    <h5 class="font-bold text-slate-900">{{ $akd->mahasiswa->nama }}</h5>
                    <p class="text-[11px] font-mono text-brand-700">NIM: {{ $akd->mahasiswa->nim }} • Sem {{ $akd->semester }}</p>
                </div>
                <div class="text-right">
                    <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        IPK: {{ $akd->ipk }} • Absen: {{ $akd->persentase_kehadiran }}%
                    </span>
                    <p class="text-[10px] text-slate-400 mt-0.5">SKS Gagal: {{ $akd->sks_tidak_lulus }} SKS</p>
                </div>
            </div>

            <!-- Form Body -->
            <form method="POST" action="{{ $updateRoute }}" class="p-5 space-y-4 text-xs">
                @csrf
                @method('PUT')

                <!-- Status Intervensi -->
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 block">Status Intervensi / Bimbingan:</label>
                    <select name="status_intervensi" required class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                        <option value="Belum Ditindaklanjuti" {{ ($akd->status_intervensi ?? '') === 'Belum Ditindaklanjuti' ? 'selected' : '' }}>Belum Ditindaklanjuti</option>
                        <option value="Dijadwalkan Bimbingan" {{ ($akd->status_intervensi ?? '') === 'Dijadwalkan Bimbingan' ? 'selected' : '' }}>Dijadwalkan Bimbingan</option>
                        <option value="Sedang Bimbingan" {{ ($akd->status_intervensi ?? '') === 'Sedang Bimbingan' ? 'selected' : '' }}>Sedang Bimbingan</option>
                        <option value="Selesai / Teratasi" {{ ($akd->status_intervensi ?? '') === 'Selesai / Teratasi' ? 'selected' : '' }}>Selesai / Teratasi</option>
                    </select>
                </div>

                <!-- Bentuk Tindakan -->
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 block">Bentuk Tindakan / Rekomendasi PA:</label>
                    <select name="tindakan_intervensi" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                        <option value="">-- Pilih Bentuk Tindakan --</option>
                        <option value="Konseling Akademik Rutin Terjadwal" {{ ($akd->tindakan_intervensi ?? '') === 'Konseling Akademik Rutin Terjadwal' ? 'selected' : '' }}>Konseling Akademik Rutin Terjadwal</option>
                        <option value="Restrukturisasi Beban SKS Semester Depan" {{ ($akd->tindakan_intervensi ?? '') === 'Restrukturisasi Beban SKS Semester Depan' ? 'selected' : '' }}>Restrukturisasi Beban SKS Semester Depan</option>
                        <option value="Program Remedial / Pengulangan Mata Kuliah" {{ ($akd->tindakan_intervensi ?? '') === 'Program Remedial / Pengulangan Mata Kuliah' ? 'selected' : '' }}>Program Remedial / Pengulangan Mata Kuliah</option>
                        <option value="Peringatan Kehadiran & Surat Teguran Absensi" {{ ($akd->tindakan_intervensi ?? '') === 'Peringatan Kehadiran & Surat Teguran Absensi' ? 'selected' : '' }}>Peringatan Kehadiran & Surat Teguran Absensi</option>
                        <option value="Pemanggilan Orang Tua / Wali Mahasiswa" {{ ($akd->tindakan_intervensi ?? '') === 'Pemanggilan Orang Tua / Wali Mahasiswa' ? 'selected' : '' }}>Pemanggilan Orang Tua / Wali Mahasiswa</option>
                        <option value="Rekomendasi Cuti Akademik Sementara" {{ ($akd->tindakan_intervensi ?? '') === 'Rekomendasi Cuti Akademik Sementara' ? 'selected' : '' }}>Rekomendasi Cuti Akademik Sementara</option>
                        <option value="Lainnya" {{ ($akd->tindakan_intervensi ?? '') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <!-- Tanggal Bimbingan -->
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 block">Tanggal Bimbingan / Intervensi:</label>
                    <input type="date" name="tanggal_intervensi" value="{{ $akd->tanggal_intervensi ? $akd->tanggal_intervensi->format('Y-m-d') : date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                </div>

                <!-- Catatan Bimbingan / Solusi -->
                <div class="space-y-1">
                    <label class="font-bold text-slate-700 block">Catatan Bimbingan & Kesepakatan Solusi:</label>
                    <textarea name="catatan_intervensi" rows="3" placeholder="Tuliskan hasil diskusi, penyebab kendala studi mahasiswa, serta komitmen perbaikan nilai..." class="w-full px-3 py-2 rounded-lg bg-white border border-slate-200 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">{{ $akd->catatan_intervensi }}</textarea>
                </div>

                <!-- Modal Actions -->
                <div class="pt-2 flex items-center justify-end space-x-2">
                    <button type="button" onclick="closeEwsModal('ews-modal-{{ $akd->id }}')" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-xs font-bold text-white shadow-xs transition flex items-center">
                        <i data-lucide="save" class="w-3.5 h-3.5 mr-1.5"></i>
                        Simpan Tindak Lanjut
                    </button>
                </div>
            </form>

        </div>
    </div>
@endforeach

<script>
    function openEwsModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            if (window.lucide) { lucide.createIcons(); }
        }
    }

    function closeEwsModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
        }
    }
</script>
@endsection
