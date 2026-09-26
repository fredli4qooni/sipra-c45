@extends('layouts.app')

@section('title', 'Master Data Dosen Pembimbing Akademik (Dosen PA)')
@section('subtitle', 'Kelola akun dan pantau distribusi mahasiswa bimbingan Dosen PA Program Studi Sistem Informasi')

@section('content')
<div class="space-y-6">

    <!-- Top Summary Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="user-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Dosen PA</p>
                    <h4 class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $totalDosen }}</h4>
                </div>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-brand-50 text-brand-700 border border-brand-200/60">
                Terdaftar
            </span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Dosen PA Aktif</p>
                    <h4 class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $totalAktif }}</h4>
                </div>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                Aktif
            </span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Mahasiswa Dibimbing</p>
                    <h4 class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $totalBimbingan }}</h4>
                </div>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60">
                Terdistribusi
            </span>
        </div>
    </div>

    <!-- Action & Filter Bar -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Daftar Dosen Pembimbing Akademik</h3>
                <p class="text-xs text-slate-500">Kelola akun login Dosen PA dan pantau jumlah mahasiswa bimbingan masing-masing</p>
            </div>
            
            <a href="{{ route('admin.dosen.create') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-xs font-bold text-white bg-brand-600 hover:bg-brand-500 shadow-xs hover:shadow-sm transition">
                <i data-lucide="user-plus" class="w-4 h-4 mr-1.5"></i>
                Tambah Dosen PA Baru
            </a>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.dosen.index') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3 pt-2">
            <!-- Search -->
            <div class="relative sm:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari nama dosen, NIP/NIDN, atau email..." 
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                >
            </div>

            <!-- Filter Status -->
            <div class="flex items-center space-x-2">
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.dosen.index') }}" title="Reset Filter" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:text-slate-900 transition flex-shrink-0">
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
                        <th class="pb-3 font-semibold">Nama Dosen & Gelar</th>
                        <th class="pb-3 font-semibold">NIP / NIDN</th>
                        <th class="pb-3 font-semibold">Kontak / Email</th>
                        <th class="pb-3 font-semibold text-center">Bimbingan Aktif</th>
                        <th class="pb-3 font-semibold">Status Akun</th>
                        <th class="pb-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dosenPas as $dosen)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-brand-50 border border-brand-200 text-brand-700 font-bold text-xs flex items-center justify-center flex-shrink-0">
                                        {{ $dosen->initials }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $dosen->name }}</p>
                                        <p class="text-[10px] text-slate-400">Peran: {{ $dosen->role_label }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 font-mono text-slate-700 font-medium">
                                {{ $dosen->nim_nip ?? '-' }}
                            </td>
                            <td class="py-3.5">
                                <p class="text-slate-900 font-medium">{{ $dosen->email }}</p>
                                <p class="text-[10px] text-slate-400">{{ $dosen->phone ?? 'Belum ada telepon' }}</p>
                            </td>
                            <td class="py-3.5 text-center">
                                <a href="{{ route('admin.dosen.show', $dosen) }}" class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 hover:bg-brand-50 text-slate-700 hover:text-brand-700 border border-slate-200/80 transition" title="Lihat mahasiswa bimbingan">
                                    <i data-lucide="users" class="w-3.5 h-3.5 mr-1 text-brand-600"></i>
                                    <span>{{ $dosen->mahasiswa_bimbingan_count }} Mahasiswa</span>
                                    @if($dosen->mahasiswa_risiko_tinggi_count > 0)
                                        <span class="ml-1.5 px-1 py-0.2 rounded text-[9px] font-bold bg-rose-100 text-rose-700">
                                            {{ $dosen->mahasiswa_risiko_tinggi_count }} Risiko Tinggi
                                        </span>
                                    @endif
                                </a>
                            </td>
                            <td class="py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold
                                    {{ $dosen->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-rose-50 text-rose-700 border border-rose-200/60' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dosen->status === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }} mr-1.5"></span>
                                    {{ $dosen->status === 'active' ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                            <td class="py-3.5 text-right space-x-1.5">
                                <a href="{{ route('admin.dosen.show', $dosen) }}" title="Lihat Mahasiswa Bimbingan" class="p-1.5 rounded-lg bg-slate-100 hover:bg-brand-50 text-slate-600 hover:text-brand-700 inline-flex transition">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                </a>
                                <a href="{{ route('admin.dosen.edit', $dosen) }}" title="Ubah Akun Dosen" class="p-1.5 rounded-lg bg-slate-100 hover:bg-amber-50 text-slate-600 hover:text-amber-700 inline-flex transition">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.dosen.destroy', $dosen) }}" class="inline" onsubmit="return confirm('Hapus akun Dosen PA ini? Mahasiswa yang dibimbing akan dilepaskan penugasannya.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus Dosen" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-700 inline-flex transition">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Tidak ada data Dosen PA yang sesuai dengan filter pencarian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100">
            {{ $dosenPas->links() }}
        </div>
    </div>

</div>
@endsection
