@extends('layouts.app')

@section('title', 'Data Akademik & Nilai Mahasiswa')
@section('subtitle', 'Dataset riwayat IPS, IPK, SKS, dan persentase kehadiran untuk data mining C4.5')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Filter Card -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Dataset Riwayat Akademik</h3>
                <p class="text-xs text-slate-500">Total data tercatat: <strong class="text-brand-700 font-mono">{{ $akademiks->total() }} Baris</strong></p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <!-- Download Template -->
                <a href="{{ route('admin.akademik.template') }}" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200/80 border border-slate-200 transition">
                    <i data-lucide="file-down" class="w-4 h-4 mr-1.5 text-brand-600"></i>
                    Unduh Template
                </a>

                <!-- Export Excel -->
                <a href="{{ route('admin.akademik.export') }}" class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-semibold text-brand-700 bg-brand-50 hover:bg-brand-100 border border-brand-200 transition">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-1.5 text-brand-600"></i>
                    Ekspor Excel
                </a>

                <!-- Import Excel Modal Trigger -->
                <button type="button" onclick="toggleImportModal()" class="inline-flex items-center px-3.5 py-2 rounded-lg text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 shadow-xs transition">
                    <i data-lucide="upload-cloud" class="w-4 h-4 mr-1.5"></i>
                    Impor Excel
                </button>

                <!-- Add New Record -->
                <a href="{{ route('admin.akademik.create') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-xs font-bold text-white bg-brand-600 hover:bg-brand-500 shadow-xs hover:shadow-sm transition">
                    <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i>
                    Tambah Nilai
                </a>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.akademik.index') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3 pt-2">
            <!-- Search -->
            <div class="relative sm:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari berdasarkan NIM atau Nama Mahasiswa..." 
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                >
            </div>

            <!-- Filter Semester -->
            <div>
                <select name="semester" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    <option value="">Semua Semester</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem }}" {{ request('semester') == $sem ? 'selected' : '' }}>Semester {{ $sem }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status Risiko -->
            <div class="flex items-center space-x-2">
                <select name="label_risiko_aktual" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    <option value="">Semua Risiko</option>
                    <option value="Risiko Rendah" {{ request('label_risiko_aktual') === 'Risiko Rendah' ? 'selected' : '' }}>Risiko Rendah</option>
                    <option value="Risiko Sedang" {{ request('label_risiko_aktual') === 'Risiko Sedang' ? 'selected' : '' }}>Risiko Sedang</option>
                    <option value="Risiko Tinggi" {{ request('label_risiko_aktual') === 'Risiko Tinggi' ? 'selected' : '' }}>Risiko Tinggi</option>
                </select>
                @if(request()->hasAny(['search', 'semester', 'label_risiko_aktual']))
                    <a href="{{ route('admin.akademik.index') }}" title="Reset Filter" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:text-slate-900 transition">
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
                        <th class="pb-3 font-semibold">Sem</th>
                        <th class="pb-3 font-semibold">IPS</th>
                        <th class="pb-3 font-semibold">IPK</th>
                        <th class="pb-3 font-semibold">SKS</th>
                        <th class="pb-3 font-semibold">Kehadiran</th>
                        <th class="pb-3 font-semibold">Cuti</th>
                        <th class="pb-3 font-semibold">Label Risiko</th>
                        <th class="pb-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($akademiks as $akd)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 font-mono font-bold text-brand-700">{{ $akd->mahasiswa->nim ?? '-' }}</td>
                            <td class="py-3.5 font-semibold text-slate-900">{{ $akd->mahasiswa->nama ?? 'Mahasiswa Telah Dihapus' }}</td>
                            <td class="py-3.5 text-slate-600 font-medium">Sem {{ $akd->semester }}</td>
                            <td class="py-3.5 text-slate-700">{{ $akd->ips }}</td>
                            <td class="py-3.5 font-bold text-slate-900">{{ $akd->ipk }}</td>
                            <td class="py-3.5 text-slate-600">
                                {{ $akd->sks_semester }} SKS
                                @if($akd->sks_tidak_lulus > 0)
                                    <span class="text-[10px] text-rose-600 font-bold block">({{ $akd->sks_tidak_lulus }} gagal)</span>
                                @endif
                            </td>
                            <td class="py-3.5 font-semibold {{ $akd->persentase_kehadiran < 75 ? 'text-rose-600' : 'text-slate-700' }}">
                                {{ $akd->persentase_kehadiran }}%
                            </td>
                            <td class="py-3.5 text-slate-600">{{ $akd->status_cuti ? 'Ya' : 'Tidak' }}</td>
                            <td class="py-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold
                                    @if($akd->label_risiko_aktual === 'Risiko Rendah') bg-brand-50 text-brand-700 border border-brand-200/60
                                    @elseif($akd->label_risiko_aktual === 'Risiko Sedang') bg-amber-50 text-amber-700 border border-amber-200/60
                                    @else bg-rose-50 text-rose-700 border border-rose-200/60
                                    @endif">
                                    @if($akd->label_risiko_aktual === 'Risiko Rendah')
                                        <i data-lucide="shield-check" class="w-3 h-3 text-brand-600 flex-shrink-0"></i>
                                    @elseif($akd->label_risiko_aktual === 'Risiko Sedang')
                                        <i data-lucide="alert-triangle" class="w-3 h-3 text-amber-600 flex-shrink-0"></i>
                                    @else
                                        <i data-lucide="alert-circle" class="w-3 h-3 text-rose-600 flex-shrink-0"></i>
                                    @endif
                                    <span>{{ $akd->label_risiko_aktual }}</span>
                                </span>
                            </td>
                            <td class="py-3.5 text-right space-x-1.5">
                                <a href="{{ route('admin.akademik.edit', $akd) }}" title="Ubah Nilai" class="p-1.5 rounded-lg bg-slate-100 hover:bg-amber-50 text-slate-600 hover:text-amber-700 inline-flex transition">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.akademik.destroy', $akd) }}" class="inline" onsubmit="return confirm('Hapus baris data akademik ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-700 inline-flex transition">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-slate-400">Belum ada dataset akademik yang sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-2 text-xs text-slate-500">
            <span>Menampilkan {{ $akademiks->firstItem() ?? 0 }} - {{ $akademiks->lastItem() ?? 0 }} dari {{ $akademiks->total() }} baris data</span>
            <div>{{ $akademiks->links() }}</div>
        </div>
    </div>

</div>

<!-- Import Excel Modal -->
<div id="importModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs hidden">
    <div class="bg-white rounded-xl border border-slate-200 shadow-xl max-w-lg w-full p-6 sm:p-8 space-y-5 animate-fade-in">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-900">Impor Dataset Excel (.xlsx / .csv)</h4>
            </div>
            <button type="button" onclick="toggleImportModal()" class="text-slate-400 hover:text-slate-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.akademik.import') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1.5">
                <p class="font-bold text-slate-900 flex items-center">
                    <i data-lucide="info" class="w-3.5 h-3.5 mr-1 text-blue-600"></i>
                    Petunjuk Pengunggahan:
                </p>
                <p>1. Gunakan file spreadsheet dengan format kolom sesuai template resmi.</p>
                <p>2. Data mahasiswa baru akan dibuat otomatis jika NIM belum terdaftar di database.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Berkas Excel <span class="text-rose-500">*</span></label>
                <input 
                    type="file" 
                    name="file" 
                    required 
                    accept=".xlsx,.xls,.csv"
                    class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer bg-slate-50 border border-slate-200 rounded-lg p-2"
                >
            </div>

            <div class="pt-3 flex items-center justify-end space-x-3 border-t border-slate-100">
                <button type="button" onclick="toggleImportModal()" class="px-4 py-2 rounded-lg bg-slate-100 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-lg text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 shadow-xs transition flex items-center">
                    <i data-lucide="upload" class="w-4 h-4 mr-1.5"></i>
                    Unggah & Proses Impor
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleImportModal() {
        const modal = document.getElementById('importModal');
        modal.classList.toggle('hidden');
    }
</script>
@endpush
@endsection
