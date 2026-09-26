@extends('layouts.app')

@section('title', 'Detail Dosen PA: ' . $dosen->name)
@section('subtitle', 'Profil identitas & rekapitulasi mahasiswa bimbingan akademik')

@section('content')
<div class="space-y-6">

    <!-- Top Action Toolbar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.dosen.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1.5"></i>
            Kembali ke Daftar Dosen PA
        </a>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.dosen.edit', $dosen) }}" class="px-3.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition flex items-center">
                <i data-lucide="pencil" class="w-3.5 h-3.5 mr-1.5"></i>
                Ubah Profil
            </a>
            <a href="{{ route('admin.ews.index') }}?dosen_pa_id={{ $dosen->id }}" class="px-3.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-500 text-xs font-bold text-white shadow-xs transition flex items-center">
                <i data-lucide="shield-alert" class="w-3.5 h-3.5 mr-1.5"></i>
                Pantau EWS Dosen Ini
            </a>
        </div>
    </div>

    <!-- Dosen Profile Header Card -->
    <div class="bg-white p-6 sm:p-8 rounded-xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center md:items-start justify-between gap-6">
        <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-5 text-center sm:text-left">
            <div class="w-16 h-16 rounded-xl bg-brand-50 border border-brand-200 text-brand-700 font-extrabold text-2xl flex items-center justify-center shadow-xs flex-shrink-0">
                {{ $dosen->initials }}
            </div>
            <div>
                <h3 class="text-xl font-extrabold text-slate-900">{{ $dosen->name }}</h3>
                <p class="text-sm font-mono text-brand-700 font-bold mt-0.5">NIP: {{ $dosen->nim_nip ?? 'Belum terdata' }}</p>
                <p class="text-xs text-slate-500 mt-1">
                    Email: {{ $dosen->email }} • No. HP: {{ $dosen->phone ?? '-' }}
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200 text-center min-w-[120px]">
                <p class="text-[10px] text-slate-400 uppercase font-bold">Total Mahasiswa</p>
                <h4 class="text-xl font-black text-slate-900 mt-0.5">{{ $totalAdvisees }}</h4>
                <span class="text-[10px] text-slate-500 font-medium">Bimbingan</span>
            </div>

            <div class="p-3.5 rounded-lg bg-rose-50 border border-rose-200 text-center min-w-[120px]">
                <p class="text-[10px] text-rose-700 uppercase font-bold">Risiko Tinggi</p>
                <h4 class="text-xl font-black text-rose-800 mt-0.5">{{ $highRiskAdvisees }}</h4>
                <span class="text-[10px] text-rose-600 font-medium">Perlu Intervensi</span>
            </div>
        </div>
    </div>

    <!-- Advisees Table Card -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-2.5">
                <i data-lucide="users" class="w-4 h-4 text-brand-600"></i>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Daftar Mahasiswa Bimbingan Akademik</h4>
            </div>
            <a href="{{ route('admin.mahasiswa.index') }}?dosen_pa_id={{ $dosen->id }}" class="text-xs font-bold text-brand-700 hover:text-brand-800 hover:underline">
                Kelola di Master Mahasiswa →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100">
                        <th class="pb-2.5 font-semibold">NIM</th>
                        <th class="pb-2.5 font-semibold">Nama Mahasiswa</th>
                        <th class="pb-2.5 font-semibold">Angkatan</th>
                        <th class="pb-2.5 font-semibold">L/P</th>
                        <th class="pb-2.5 font-semibold">IPK Terakhir</th>
                        <th class="pb-2.5 font-semibold">Kehadiran</th>
                        <th class="pb-2.5 font-semibold">Status Studi</th>
                        <th class="pb-2.5 font-semibold">Klasifikasi Risiko</th>
                        <th class="pb-2.5 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($mahasiswaBimbingan as $mhs)
                        @php $akd = $mhs->latestAkademik; @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 font-mono font-bold text-brand-700">{{ $mhs->nim }}</td>
                            <td class="py-3 font-semibold text-slate-900">{{ $mhs->nama }}</td>
                            <td class="py-3 text-slate-600">{{ $mhs->angkatan }}</td>
                            <td class="py-3 text-slate-500">{{ $mhs->jenis_kelamin }}</td>
                            <td class="py-3 font-bold text-slate-900">{{ $akd->ipk ?? '-' }}</td>
                            <td class="py-3 text-slate-600">{{ $akd->persentase_kehadiran ?? '-' }}%</td>
                            <td class="py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold
                                    @if($mhs->status_mahasiswa === 'Aktif') bg-brand-50 text-brand-700 border border-brand-200/60
                                    @elseif($mhs->status_mahasiswa === 'Cuti') bg-amber-50 text-amber-700 border border-amber-200/60
                                    @else bg-rose-50 text-rose-700 border border-rose-200/60
                                    @endif">
                                    {{ $mhs->status_mahasiswa }}
                                </span>
                            </td>
                            <td class="py-3">
                                @if($akd)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold
                                        @if($akd->label_risiko_aktual === 'Risiko Rendah') bg-brand-50 text-brand-700 border border-brand-200
                                        @elseif($akd->label_risiko_aktual === 'Risiko Sedang') bg-amber-50 text-amber-700 border border-amber-200
                                        @else bg-rose-50 text-rose-700 border border-rose-200
                                        @endif">
                                        {{ $akd->label_risiko_aktual }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[10px]">-</span>
                                @endif
                            </td>
                            <td class="py-3 text-right space-x-1">
                                <a href="{{ route('admin.mahasiswa.show', $mhs) }}" title="Lihat Profil" class="p-1.5 rounded-lg bg-slate-100 hover:bg-brand-50 text-slate-600 hover:text-brand-700 inline-flex transition">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                </a>
                                <a href="{{ route('admin.mahasiswa.edit', $mhs) }}" title="Ubah Penugasan PA" class="p-1.5 rounded-lg bg-slate-100 hover:bg-amber-50 text-slate-600 hover:text-amber-700 inline-flex transition">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">Belum ada mahasiswa yang ditugaskan ke Dosen PA ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100">
            {{ $mahasiswaBimbingan->links() }}
        </div>
    </div>

</div>
@endsection
