@extends('layouts.app')

@section('title', 'Hasil Batch Prediksi: ' . $batch->batch_code)
@section('subtitle', 'Rekapitulasi distribusi klasifikasi risiko akademik mahasiswa')

@section('content')
<div class="space-y-6">

    <!-- Top Action Toolbar -->
    <div class="flex items-center justify-between">
        <a href="{{ auth()->user()->isAdmin() ? route('admin.prediksi.index') : route('prodi.prediksi.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1.5"></i>
            Kembali ke Riwayat Prediksi
        </a>

        @php
            $exportBatchRoute = auth()->user()->isAdmin() ? route('admin.prediksi.batch.export', $batch) : route('prodi.prediksi.batch.export', $batch);
        @endphp
        <a href="{{ $exportBatchRoute }}" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-xs font-bold text-white shadow-xs transition flex items-center">
            <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2"></i>
            Ekspor Hasil Prediksi ke Excel (.xlsx)
        </a>
    </div>

    <!-- Overview Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Batch Metadata & Distribution Cards -->
        <div class="lg:col-span-2 space-y-4">
            
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200 font-mono text-xs font-bold">
                        {{ $batch->batch_code }}
                    </span>
                    <span class="text-xs text-slate-500">{{ $batch->created_at->format('d F Y, H:i') }} WIB</span>
                </div>
                <h3 class="text-xl font-extrabold text-slate-900">Hasil Klasifikasi: {{ $batch->file_name ?? 'Batch Dataset' }}</h3>
                <p class="text-xs text-slate-500">
                    Model Acuan: <strong class="text-purple-700">{{ $batch->model->nama_model ?? 'C4.5 Default' }}</strong> • Dieksekusi oleh <strong>{{ $batch->creator->name ?? 'Admin' }}</strong>
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 rounded-2xl bg-brand-50/60 border border-brand-200/80 text-center space-y-1">
                    <span class="text-xs font-bold text-brand-800">🟢 Risiko Rendah</span>
                    <h4 class="text-2xl font-black text-slate-900">{{ $batch->total_rendah }}</h4>
                    <p class="text-[10px] text-brand-700 font-medium">
                        {{ $batch->total_records > 0 ? round(($batch->total_rendah / $batch->total_records) * 100) : 0 }}% Mahasiswa
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/80 text-center space-y-1">
                    <span class="text-xs font-bold text-amber-800">🟡 Risiko Sedang</span>
                    <h4 class="text-2xl font-black text-slate-900">{{ $batch->total_sedang }}</h4>
                    <p class="text-[10px] text-amber-700 font-medium">
                        {{ $batch->total_records > 0 ? round(($batch->total_sedang / $batch->total_records) * 100) : 0 }}% Mahasiswa
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-rose-50/60 border border-rose-200/80 text-center space-y-1">
                    <span class="text-xs font-bold text-rose-800">🔴 Risiko Tinggi</span>
                    <h4 class="text-2xl font-black text-slate-900">{{ $batch->total_tinggi }}</h4>
                    <p class="text-[10px] text-rose-700 font-medium">
                        {{ $batch->total_records > 0 ? round(($batch->total_tinggi / $batch->total_records) * 100) : 0 }}% Mahasiswa
                    </p>
                </div>
            </div>
        </div>

        <!-- Doughnut Chart Canvas Card -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col items-center justify-center space-y-4">
            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Distribusi Proporsi Risiko</h4>
            <div class="w-44 h-44 relative">
                <canvas id="riskDoughnutChart"></canvas>
            </div>
            <p class="text-[11px] text-slate-500 text-center">Total <strong>{{ $batch->total_records }}</strong> Mahasiswa Terklasifikasi</p>
        </div>

    </div>

    <!-- Student List Table -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-2.5">
                <i data-lucide="users" class="w-4 h-4 text-brand-600"></i>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Rincian Hasil per Mahasiswa</h4>
            </div>
            <span class="text-xs text-slate-500 font-medium">Menampilkan {{ $prediksis->count() }} dari {{ $batch->total_records }} data</span>
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
                        <th class="pb-3 font-semibold">Hasil Prediksi</th>
                        <th class="pb-3 font-semibold">Status DO</th>
                        <th class="pb-3 font-semibold">Confidence</th>
                        <th class="pb-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($prediksis as $item)
                        @php $input = $item->input_params_json ?? []; @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 font-mono font-bold text-brand-700">{{ $item->nim ?? '-' }}</td>
                            <td class="py-3.5 font-semibold text-slate-900">{{ $item->nama_mahasiswa }}</td>
                            <td class="py-3.5 text-slate-600 font-medium">Sem {{ $item->semester }}</td>
                            <td class="py-3.5 font-bold text-slate-900">{{ $input['ipk'] ?? '-' }}</td>
                            <td class="py-3.5 text-slate-600">{{ $input['persentase_kehadiran'] ?? '-' }}%</td>
                            <td class="py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold
                                    @if($item->hasil_klasifikasi === 'Risiko Rendah') bg-brand-50 text-brand-700 border border-brand-200/60
                                    @elseif($item->hasil_klasifikasi === 'Risiko Sedang') bg-amber-50 text-amber-700 border border-amber-200/60
                                    @else bg-rose-50 text-rose-700 border border-rose-200/60
                                    @endif">
                                    {{ $item->hasil_klasifikasi }}
                                </span>
                            </td>
                            <td class="py-3.5 text-[10px] font-bold {{ $item->status_do === 'Berisiko DO' ? 'text-rose-600' : 'text-slate-500' }}">
                                {{ $item->status_do }}
                            </td>
                            <td class="py-3.5 text-slate-900 font-mono font-semibold">{{ $item->confidence_score }}%</td>
                            <td class="py-3.5 text-right">
                                @php $showRoute = auth()->user()->isAdmin() ? route('admin.prediksi.show', $item) : route('prodi.prediksi.show', $item); @endphp
                                <a href="{{ $showRoute }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">Tidak ada rekam data dalam batch ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100">
            {{ $prediksis->links() }}
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('riskDoughnutChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Risiko Rendah', 'Risiko Sedang', 'Risiko Tinggi'],
                datasets: [{
                    data: [{{ $batch->total_rendah }}, {{ $batch->total_sedang }}, {{ $batch->total_tinggi }}],
                    backgroundColor: ['#059669', '#d97706', '#e11d48'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endpush
@endsection
