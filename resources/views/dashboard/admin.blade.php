@extends('layouts.app')

@section('title', 'Dashboard Administrator')
@section('subtitle', 'Panel Kontrol Utama Sistem Prediksi Risiko Akademik Decision Tree C4.5')

@section('header_actions')
<div class="flex items-center space-x-2">
    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200/80">
        <span class="w-1.5 h-1.5 rounded-full bg-brand-500 mr-2"></span>
        Model: {{ $activeModel->nama_model ?? 'C4.5 Default' }} ({{ $activeModel->accuracy ?? '0' }}%)
    </span>
</div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Stat Cards Grid (Matching Reference Design: Soft square icon, bold metric, inline trend chip) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Card 1: Total Mahasiswa -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition flex items-center justify-between">
            <div class="flex items-center space-x-3.5 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-slate-100/90 text-slate-700 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="users" class="w-5 h-5 text-slate-600"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 truncate">Total Mahasiswa</p>
                    <h4 class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ number_format($totalMahasiswa) }}</h4>
                </div>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-brand-50 text-brand-700 border border-brand-200/60">
                {{ $totalAktif }} Aktif
            </span>
        </div>

        <!-- Card 2: Rekam Akademik -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition flex items-center justify-between">
            <div class="flex items-center space-x-3.5 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-slate-100/90 text-slate-700 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="database" class="w-5 h-5 text-slate-600"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 truncate">Rekam Akademik</p>
                    <h4 class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ number_format($totalDataAkademik) }}</h4>
                </div>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60">
                Data Nilai
            </span>
        </div>

        <!-- Card 3: Model C4.5 -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition flex items-center justify-between">
            <div class="flex items-center space-x-3.5 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-slate-100/90 text-slate-700 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="cpu" class="w-5 h-5 text-purple-600"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 truncate">Model C4.5</p>
                    <h4 class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $totalModelC45 }} Model</h4>
                </div>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200/60">
                {{ $activeModel ? $activeModel->accuracy . '%' : 'Ready' }}
            </span>
        </div>

        <!-- Card 4: Total Prediksi -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition flex items-center justify-between">
            <div class="flex items-center space-x-3.5 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-slate-100/90 text-slate-700 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="activity" class="w-5 h-5 text-amber-600"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 truncate">Total Prediksi</p>
                    <h4 class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ number_format($totalPrediksi) }}</h4>
                </div>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                Riwayat
            </span>
        </div>

    </div>

    <!-- Long Highlight Card (Matching bottom card in Reference) -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-600 flex items-center justify-center flex-shrink-0">
                <i data-lucide="bell-ring" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h4 class="text-2xl font-black text-slate-900">{{ $totalRisikoTinggi }}</h4>
                    <span class="text-xs font-semibold text-slate-500">Mahasiswa dalam Kategori Peringatan Dini (EWS)</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Memerlukan intervensi konseling akademik dan pendampingan Dosen PA sebelum semester berjalan berakhir.
                </p>
            </div>
        </div>
        <a href="{{ route('admin.ews.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100/80 border border-rose-200 text-xs font-bold text-rose-700 transition flex-shrink-0">
            <span>Buka EWS Alert Center</span>
            <i data-lucide="arrow-right" class="w-4 h-4 ml-1.5"></i>
        </a>
    </div>

    <!-- Risk Distribution & Charts Grid -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-2.5">
                <i data-lucide="pie-chart" class="w-4 h-4 text-brand-600"></i>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Distribusi Proporsi Risiko Mahasiswa</h4>
            </div>
            <span class="text-xs text-slate-400 font-medium">Total Sampel: {{ $totalDataAkademik }}</span>
        </div>

        <!-- 3-Column Risk Summary Tiles -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            <!-- Risiko Rendah -->
            <div class="p-4 rounded-2xl bg-brand-50/60 border border-brand-200/80 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-brand-800">🟢 Risiko Rendah</span>
                    <h5 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalRisikoRendah }}</h5>
                    <p class="text-[11px] text-brand-700">Performa Baik / Aman</p>
                </div>
                <span class="text-sm font-extrabold text-brand-700 bg-brand-100/80 px-2 py-0.5 rounded-lg">
                    {{ $totalDataAkademik > 0 ? round(($totalRisikoRendah / $totalDataAkademik) * 100) : 0 }}%
                </span>
            </div>

            <!-- Risiko Sedang -->
            <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/80 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-amber-800">🟡 Risiko Sedang</span>
                    <h5 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalRisikoSedang }}</h5>
                    <p class="text-[11px] text-amber-700">Perlu Pemantauan PA</p>
                </div>
                <span class="text-sm font-extrabold text-amber-700 bg-amber-100/80 px-2 py-0.5 rounded-lg">
                    {{ $totalDataAkademik > 0 ? round(($totalRisikoSedang / $totalDataAkademik) * 100) : 0 }}%
                </span>
            </div>

            <!-- Risiko Tinggi -->
            <div class="p-4 rounded-2xl bg-rose-50/60 border border-rose-200/80 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-rose-800">🔴 Risiko Tinggi</span>
                    <h5 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalRisikoTinggi }}</h5>
                    <p class="text-[11px] text-rose-700">Potensi Hambatan / DO</p>
                </div>
                <span class="text-sm font-extrabold text-rose-700 bg-rose-100/80 px-2 py-0.5 rounded-lg">
                    {{ $totalDataAkademik > 0 ? round(($totalRisikoTinggi / $totalDataAkademik) * 100) : 0 }}%
                </span>
            </div>

        </div>

        <!-- Visual Charts Grid (Doughnut & Bar) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
            <div class="space-y-3">
                <p class="text-xs font-bold text-slate-700 text-center">Proporsi Klasifikasi Risiko Mahasiswa</p>
                <div class="h-48 relative">
                    <canvas id="adminRiskPieChart"></canvas>
                </div>
            </div>
            <div class="space-y-3">
                <p class="text-xs font-bold text-slate-700 text-center">Distribusi Kategori Kehadiran Kuliah</p>
                <div class="h-48 relative">
                    <canvas id="adminAttendanceChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Students Table Card -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-2.5">
                <i data-lucide="user-check" class="w-4 h-4 text-brand-600"></i>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Mahasiswa Terdaftar Terbaru</h4>
            </div>
            <a href="{{ route('admin.mahasiswa.index') }}" class="text-xs font-bold text-brand-700 hover:text-brand-800 hover:underline">
                Lihat Semua ➜
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100">
                        <th class="pb-2.5 font-semibold">NIM</th>
                        <th class="pb-2.5 font-semibold">Nama Lengkap</th>
                        <th class="pb-2.5 font-semibold">Angkatan</th>
                        <th class="pb-2.5 font-semibold">IPK Terakhir</th>
                        <th class="pb-2.5 font-semibold">Kehadiran</th>
                        <th class="pb-2.5 font-semibold">Status Risiko</th>
                        <th class="pb-2.5 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentMahasiswas as $mhs)
                        @php $akd = $mhs->latestAkademik; @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 font-mono font-bold text-brand-700">{{ $mhs->nim }}</td>
                            <td class="py-3 font-semibold text-slate-900">{{ $mhs->nama }}</td>
                            <td class="py-3 text-slate-500">{{ $mhs->angkatan }}</td>
                            <td class="py-3 font-bold text-slate-900">{{ $akd->ipk ?? '-' }}</td>
                            <td class="py-3 text-slate-600">{{ $akd->persentase_kehadiran ?? '-' }}%</td>
                            <td class="py-3">
                                @if($akd)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold
                                        @if($akd->label_risiko_aktual === 'Risiko Rendah') bg-brand-50 text-brand-700 border border-brand-200/60
                                        @elseif($akd->label_risiko_aktual === 'Risiko Sedang') bg-amber-50 text-amber-700 border border-amber-200/60
                                        @else bg-rose-50 text-rose-700 border border-rose-200/60
                                        @endif">
                                        {{ $akd->label_risiko_aktual }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[10px]">-</span>
                                @endif
                            </td>
                            <td class="py-3 text-right">
                                <a href="{{ route('admin.mahasiswa.show', $mhs) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200/80 text-slate-700 text-[11px] font-semibold transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400">Belum ada data mahasiswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Doughnut Chart: Risk Distribution
        const riskCtx = document.getElementById('adminRiskPieChart').getContext('2d');
        new Chart(riskCtx, {
            type: 'doughnut',
            data: {
                labels: ['Risiko Rendah', 'Risiko Sedang', 'Risiko Tinggi'],
                datasets: [{
                    data: [{{ $totalRisikoRendah }}, {{ $totalRisikoSedang }}, {{ $totalRisikoTinggi }}],
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
                        position: 'bottom',
                        labels: {
                            color: '#475569',
                            font: { size: 11, family: 'Plus Jakarta Sans' },
                            padding: 12
                        }
                    }
                },
                cutout: '70%'
            }
        });

        // 2. Bar Chart: Attendance Breakdown
        const attCtx = document.getElementById('adminAttendanceChart').getContext('2d');
        new Chart(attCtx, {
            type: 'bar',
            data: {
                labels: ['Baik (>85%)', 'Cukup (75-85%)', 'Kurang (<75%)'],
                datasets: [{
                    label: 'Jumlah Mahasiswa',
                    data: [
                        {{ \App\Models\DataAkademik::where('persentase_kehadiran', '>=', 85)->count() }},
                        {{ \App\Models\DataAkademik::whereBetween('persentase_kehadiran', [75, 84.99])->count() }},
                        {{ \App\Models\DataAkademik::where('persentase_kehadiran', '<', 75)->count() }}
                    ],
                    backgroundColor: ['#059669', '#d97706', '#e11d48'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        ticks: { color: '#64748b', font: { size: 10, family: 'Plus Jakarta Sans' } },
                        grid: { display: false }
                    },
                    y: {
                        ticks: { color: '#64748b', font: { size: 10 }, stepSize: 1 },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
