@extends('layouts.app')

@section('title', 'Riwayat & Modul Prediksi Risiko C4.5')
@section('subtitle', 'Simulasi klasifikasi risiko akademik & pemantauan deteksi dini mahasiswa')

@section('content')
<div class="space-y-6">

    <!-- Top Ribbon & Action Buttons -->
    <div class="bg-white p-6 sm:p-8 rounded-xl border border-slate-200/80 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 mr-1.5 text-brand-600"></i>
                    Mesin Prediksi Aktif
                </span>
                <span class="text-xs text-slate-500 font-mono font-semibold">{{ $activeModel->nama_model ?? 'Model Belum Aktif' }}</span>
            </div>
            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Sistem Prediksi Risiko Akademik</h3>
            <p class="text-xs text-slate-500 max-w-2xl leading-relaxed">
                Jalankan prediksi risiko studi mahasiswa secara individu untuk bimbingan konseling PA atau lakukan klasifikasi massal seluruh angkatan menggunakan berkas Excel.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ auth()->user()->isAdmin() ? route('admin.prediksi.batch') : route('prodi.prediksi.batch') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200/80 border border-slate-200 transition">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-1.5 text-blue-600"></i>
                Prediksi Massal (Excel)
            </a>

            <a href="{{ auth()->user()->isAdmin() ? route('admin.prediksi.single') : route('prodi.prediksi.single') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg text-xs font-bold text-white bg-brand-600 hover:bg-brand-500 shadow-xs transition">
                <i data-lucide="play" class="w-4 h-4 mr-1.5"></i>
                Simulasi Prediksi Tunggal
            </a>
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg border border-slate-200/80 shadow-xs text-center space-y-1">
            <p class="text-[11px] font-medium text-slate-500 uppercase">Total Prediksi</p>
            <h4 class="text-2xl font-black text-slate-900">{{ number_format($totalPrediksi) }}</h4>
            <p class="text-[10px] text-slate-400">Total riwayat tersimpan</p>
        </div>

        <div class="p-4 rounded-lg bg-brand-50/60 border border-brand-200/80 text-center space-y-1">
            <div class="flex items-center justify-center space-x-1 text-[11px] font-bold text-brand-800 uppercase">
                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-brand-600"></i>
                <span>Risiko Rendah</span>
            </div>
            <h4 class="text-2xl font-black text-slate-900">{{ number_format($totalRendah) }}</h4>
            <p class="text-[10px] text-brand-700 font-medium">Studi Berjalan Lancar</p>
        </div>

        <div class="p-4 rounded-lg bg-amber-50/60 border border-amber-200/80 text-center space-y-1">
            <div class="flex items-center justify-center space-x-1 text-[11px] font-bold text-amber-800 uppercase">
                <i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-amber-600"></i>
                <span>Risiko Sedang</span>
            </div>
            <h4 class="text-2xl font-black text-slate-900">{{ number_format($totalSedang) }}</h4>
            <p class="text-[10px] text-amber-700 font-medium">Perlu Pemantauan PA</p>
        </div>

        <div class="p-4 rounded-lg bg-rose-50/60 border border-rose-200/80 text-center space-y-1">
            <div class="flex items-center justify-center space-x-1 text-[11px] font-bold text-rose-800 uppercase">
                <i data-lucide="alert-circle" class="w-3.5 h-3.5 text-rose-600"></i>
                <span>Risiko Tinggi</span>
            </div>
            <h4 class="text-2xl font-black text-slate-900">{{ number_format($totalTinggi) }}</h4>
            <p class="text-[10px] text-rose-700 font-medium">Peringatan Dini / Potensi DO</p>
        </div>
    </div>

    <!-- Recent Prediction Batches -->
    @if($batches->count() > 0)
        <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2.5">
                    <i data-lucide="folders" class="w-4 h-4 text-blue-600"></i>
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Riwayat Batch Prediksi Massal (Excel)</h4>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($batches as $batch)
                    <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 hover:border-slate-300 transition space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-xs font-bold text-blue-700">{{ $batch->batch_code }}</span>
                            <span class="text-[10px] text-slate-400">{{ $batch->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="text-xs text-slate-900 font-semibold truncate">{{ $batch->file_name ?? 'Batch Upload' }}</p>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-200/60 text-[11px]">
                            <span class="text-slate-500">Total: <strong class="text-slate-900">{{ $batch->total_records }}</strong> data</span>
                            <a href="{{ auth()->user()->isAdmin() ? route('admin.prediksi.batch.show', $batch) : route('prodi.prediksi.batch.show', $batch) }}" class="text-brand-700 hover:underline font-bold inline-flex items-center">
                                <span>Hasil Detail</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Prediction History Table -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-2.5">
                <i data-lucide="history" class="w-4 h-4 text-brand-600"></i>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Daftar Hasil Klasifikasi Risiko</h4>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ auth()->user()->isAdmin() ? route('admin.prediksi.index') : route('prodi.prediksi.index') }}" class="flex items-center space-x-2">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari NIM atau Nama..." 
                    class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                >
                <select name="hasil_klasifikasi" onchange="this.form.submit()" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    <option value="">Semua Risiko</option>
                    <option value="Risiko Rendah" {{ request('hasil_klasifikasi') === 'Risiko Rendah' ? 'selected' : '' }}>Risiko Rendah</option>
                    <option value="Risiko Sedang" {{ request('hasil_klasifikasi') === 'Risiko Sedang' ? 'selected' : '' }}>Risiko Sedang</option>
                    <option value="Risiko Tinggi" {{ request('hasil_klasifikasi') === 'Risiko Tinggi' ? 'selected' : '' }}>Risiko Tinggi</option>
                </select>
                @if(request()->hasAny(['search', 'hasil_klasifikasi']))
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.prediksi.index') : route('prodi.prediksi.index') }}" class="p-1.5 rounded-lg bg-slate-100 text-slate-500 hover:text-slate-900 transition">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100">
                        <th class="pb-3 font-semibold">Waktu Prediksi</th>
                        <th class="pb-3 font-semibold">NIM</th>
                        <th class="pb-3 font-semibold">Nama Mahasiswa</th>
                        <th class="pb-3 font-semibold">Semester</th>
                        <th class="pb-3 font-semibold">IPK</th>
                        <th class="pb-3 font-semibold">Kehadiran</th>
                        <th class="pb-3 font-semibold">Hasil Klasifikasi</th>
                        <th class="pb-3 font-semibold">Status DO</th>
                        <th class="pb-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($prediksis as $p)
                        @php $input = $p->input_params_json ?? []; @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 text-slate-500">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3.5 font-mono font-bold text-brand-700">{{ $p->nim ?? '-' }}</td>
                            <td class="py-3.5 font-semibold text-slate-900">{{ $p->nama_mahasiswa }}</td>
                            <td class="py-3.5 text-slate-600 font-medium">Sem {{ $p->semester ?? '-' }}</td>
                            <td class="py-3.5 font-bold text-slate-900">{{ $input['ipk'] ?? '-' }}</td>
                            <td class="py-3.5 text-slate-600">{{ $input['persentase_kehadiran'] ?? '-' }}%</td>
                            <td class="py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold
                                    @if($p->hasil_klasifikasi === 'Risiko Rendah') bg-brand-50 text-brand-700 border border-brand-200/60
                                    @elseif($p->hasil_klasifikasi === 'Risiko Sedang') bg-amber-50 text-amber-700 border border-amber-200/60
                                    @else bg-rose-50 text-rose-700 border border-rose-200/60
                                    @endif">
                                    {{ $p->hasil_klasifikasi }}
                                </span>
                            </td>
                            <td class="py-3.5">
                                <span class="text-[10px] font-bold {{ $p->status_do === 'Berisiko DO' ? 'text-rose-600' : 'text-slate-500' }}">
                                    {{ $p->status_do }}
                                </span>
                            </td>
                            <td class="py-3.5 text-right">
                                @php $showPredRoute = auth()->user()->isAdmin() ? route('admin.prediksi.show', $p) : route('prodi.prediksi.show', $p); @endphp
                                <a href="{{ $showPredRoute }}" class="px-2.5 py-1 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold text-[11px] border border-brand-200/60 transition inline-flex items-center">
                                    <span>Surat Rekomendasi</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">Belum ada riwayat hasil prediksi. Silakan jalankan simulasi prediksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-between items-center text-xs text-slate-500">
            <span>Menampilkan {{ $prediksis->firstItem() ?? 0 }} - {{ $prediksis->lastItem() ?? 0 }} dari {{ $prediksis->total() }} data</span>
            <div>{{ $prediksis->links() }}</div>
        </div>
    </div>

</div>
@endsection
