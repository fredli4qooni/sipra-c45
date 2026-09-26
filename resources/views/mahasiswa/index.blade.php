@extends('layouts.app')

@section('title', 'Master Data Mahasiswa')
@section('subtitle', 'Kelola informasi identitas mahasiswa Program Studi Sistem Informasi')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Filter Card -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Daftar Mahasiswa Terdaftar</h3>
                <p class="text-xs text-slate-500">Total data: <strong class="text-brand-700 font-mono">{{ $mahasiswas->total() }} Mahasiswa</strong></p>
            </div>
            
            <a href="{{ route('admin.mahasiswa.create') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-xs font-bold text-white bg-brand-600 hover:bg-brand-500 shadow-xs hover:shadow-sm transition">
                <i data-lucide="user-plus" class="w-4 h-4 mr-1.5"></i>
                Tambah Mahasiswa Baru
            </a>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.mahasiswa.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-2">
            <!-- Search -->
            <div class="relative sm:col-span-2 lg:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari berdasarkan NIM atau Nama Mahasiswa..." 
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                >
            </div>

            <!-- Filter Angkatan -->
            <div>
                <select name="angkatan" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    <option value="">Semua Angkatan</option>
                    @foreach($angkatans as $akt)
                        <option value="{{ $akt }}" {{ request('angkatan') == $akt ? 'selected' : '' }}>Angkatan {{ $akt }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Dosen PA -->
            <div>
                <select name="dosen_pa_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    <option value="">Semua Dosen PA</option>
                    <option value="unassigned" {{ request('dosen_pa_id') == 'unassigned' ? 'selected' : '' }}>Belum Ada PA</option>
                    @foreach($dosenPas as $dosen)
                        <option value="{{ $dosen->id }}" {{ request('dosen_pa_id') == $dosen->id ? 'selected' : '' }}>PA: {{ Str::limit($dosen->name, 18) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div class="flex items-center space-x-2">
                <select name="status_mahasiswa" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    <option value="">Semua Status</option>
                    <option value="Aktif" {{ request('status_mahasiswa') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Cuti" {{ request('status_mahasiswa') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="Lulus" {{ request('status_mahasiswa') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="Drop Out" {{ request('status_mahasiswa') == 'Drop Out' ? 'selected' : '' }}>Drop Out</option>
                </select>
                @if(request()->hasAny(['search', 'angkatan', 'status_mahasiswa', 'dosen_pa_id']))
                    <a href="{{ route('admin.mahasiswa.index') }}" title="Reset Filter" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:text-slate-900 transition flex-shrink-0">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100">
                        <th class="pb-3 font-semibold">NIM</th>
                        <th class="pb-3 font-semibold">Nama Mahasiswa</th>
                        <th class="pb-3 font-semibold">Dosen PA</th>
                        <th class="pb-3 font-semibold">L/P</th>
                        <th class="pb-3 font-semibold">Angkatan</th>
                        <th class="pb-3 font-semibold">Jalur Masuk</th>
                        <th class="pb-3 font-semibold">IPK Terakhir</th>
                        <th class="pb-3 font-semibold">Kehadiran</th>
                        <th class="pb-3 font-semibold">Status</th>
                        <th class="pb-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($mahasiswas as $mhs)
                        @php $akd = $mhs->latestAkademik; @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 font-mono font-bold text-brand-700">{{ $mhs->nim }}</td>
                            <td class="py-3.5 font-semibold text-slate-900">{{ $mhs->nama }}</td>
                            <td class="py-3.5">
                                @if($mhs->dosenPa)
                                    <span class="inline-flex items-center text-slate-700 font-medium" title="{{ $mhs->dosenPa->name }}">
                                        <i data-lucide="user-check" class="w-3.5 h-3.5 text-brand-600 mr-1 flex-shrink-0"></i>
                                        <span class="truncate max-w-[140px]">{{ $mhs->dosenPa->name }}</span>
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Belum diatur</span>
                                @endif
                            </td>
                            <td class="py-3.5 text-slate-500">{{ $mhs->jenis_kelamin }}</td>
                            <td class="py-3.5 text-slate-600 font-medium">{{ $mhs->angkatan }}</td>
                            <td class="py-3.5">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $mhs->jalur_masuk ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 font-bold text-slate-900">{{ $akd->ipk ?? '-' }}</td>
                            <td class="py-3.5 text-slate-600">{{ $akd->persentase_kehadiran ?? '-' }}%</td>
                            <td class="py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold
                                    @if($mhs->status_mahasiswa === 'Aktif') bg-brand-50 text-brand-700 border border-brand-200/60
                                    @elseif($mhs->status_mahasiswa === 'Cuti') bg-amber-50 text-amber-700 border border-amber-200/60
                                    @elseif($mhs->status_mahasiswa === 'Lulus') bg-blue-50 text-blue-700 border border-blue-200/60
                                    @else bg-rose-50 text-rose-700 border border-rose-200/60
                                    @endif">
                                    {{ $mhs->status_mahasiswa }}
                                </span>
                            </td>
                            <td class="py-3.5 text-right space-x-1.5">
                                <a href="{{ route('admin.mahasiswa.show', $mhs) }}" title="Detail Profil" class="p-1.5 rounded-lg bg-slate-100 hover:bg-brand-50 text-slate-600 hover:text-brand-700 inline-flex transition">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                </a>
                                <a href="{{ route('admin.mahasiswa.edit', $mhs) }}" title="Ubah Data" class="p-1.5 rounded-lg bg-slate-100 hover:bg-amber-50 text-slate-600 hover:text-amber-700 inline-flex transition">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.mahasiswa.destroy', $mhs) }}" class="inline" onsubmit="return confirm('Hapus data mahasiswa ini? Data akademik terkait juga akan terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus Data" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-700 inline-flex transition">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-slate-400">Tidak ada data mahasiswa yang sesuai dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100">
            {{ $mahasiswas->links() }}
        </div>
    </div>

</div>
@endsection
