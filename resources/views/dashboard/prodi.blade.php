@extends('layouts.app')

@section('title', 'Dashboard Pemantauan Akademik')
@section('subtitle', 'Monitoring Risiko Akademik Mahasiswa Program Studi Sistem Informasi')

@section('header_actions')
<div class="flex items-center space-x-2">
    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200/80">
        <span class="w-1.5 h-1.5 rounded-full bg-brand-500 mr-2"></span>
        Total Terpantau: {{ $totalMahasiswa }} Mahasiswa
    </span>
</div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- 3 Stat Cards Grid (Matching Reference Design) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        
        <!-- Risiko Rendah -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition flex items-center justify-between">
            <div class="flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Risiko Rendah (Aman)</p>
                    <h4 class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $totalRisikoRendah }}</h4>
                </div>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-brand-50 text-brand-700 border border-brand-200/60">
                Lancar
            </span>
        </div>

        <!-- Risiko Sedang -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition flex items-center justify-between">
            <div class="flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Risiko Sedang (Waspada)</p>
                    <h4 class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $totalRisikoSedang }}</h4>
                </div>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                Pantau PA
            </span>
        </div>

        <!-- Risiko Tinggi -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition flex items-center justify-between">
            <div class="flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="shield-alert" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Risiko Tinggi (Perhatian)</p>
                    <h4 class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $totalRisikoTinggi }}</h4>
                </div>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60">
                Prioritas
            </span>
        </div>

    </div>

    <!-- Quick Tool Cards for Prodi & Dosen PA -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        
        <a href="{{ route('prodi.tree.show') }}" class="bg-white p-5 rounded-2xl border border-slate-200/80 hover:border-brand-300 shadow-xs transition group space-y-2">
            <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center group-hover:bg-brand-600 group-hover:text-white transition">
                <i data-lucide="git-merge" class="w-5 h-5"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-900 group-hover:text-brand-700 transition">Pohon Keputusan C4.5</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Lihat struktur visual cabang klasifikasi dan nilai threshold tiap atribut.</p>
        </a>

        <a href="{{ route('prodi.ews.index') }}" class="bg-white p-5 rounded-2xl border border-slate-200/80 hover:border-rose-300 shadow-xs transition group space-y-2">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white transition">
                <i data-lucide="bell-ring" class="w-5 h-5"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-900 group-hover:text-rose-700 transition">Early Warning System (EWS)</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Daftar mahasiswa terindikasi masalah IPK, kehadiran, atau SKS mengulang.</p>
        </a>

        <a href="{{ route('prodi.laporan.index') }}" class="bg-white p-5 rounded-2xl border border-slate-200/80 hover:border-blue-300 shadow-xs transition group space-y-2">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition">
                <i data-lucide="file-spreadsheet" class="w-5 h-5"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-900 group-hover:text-blue-700 transition">Laporan & Rekapitulasi</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Cetak laporan resmi bertanda tangan Kaprodi & PA atau ekspor Excel.</p>
        </a>

    </div>

    <!-- High Risk Alert Table for Immediate Consultation -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-2.5">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Mahasiswa Perlu Penanganan Segera</h4>
            </div>
            <a href="{{ route('prodi.ews.index') }}" class="text-xs font-bold text-brand-700 hover:text-brand-800 hover:underline">
                Buka EWS Center ➜
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100">
                        <th class="pb-2.5 font-semibold">NIM</th>
                        <th class="pb-2.5 font-semibold">Nama Mahasiswa</th>
                        <th class="pb-2.5 font-semibold">Semester</th>
                        <th class="pb-2.5 font-semibold">IPK</th>
                        <th class="pb-2.5 font-semibold">Kehadiran</th>
                        <th class="pb-2.5 font-semibold">Faktor Risiko</th>
                        <th class="pb-2.5 font-semibold text-right">Aksi Bimbingan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($highRiskStudents as $akd)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 font-mono font-bold text-rose-700">{{ $akd->mahasiswa->nim }}</td>
                            <td class="py-3 font-semibold text-slate-900">{{ $akd->mahasiswa->nama }}</td>
                            <td class="py-3 text-slate-500">Sem {{ $akd->semester }}</td>
                            <td class="py-3 font-bold {{ $akd->ipk < 2.75 ? 'text-rose-600' : 'text-slate-900' }}">{{ $akd->ipk }}</td>
                            <td class="py-3 font-semibold {{ $akd->persentase_kehadiran < 75 ? 'text-rose-600' : 'text-slate-600' }}">
                                {{ $akd->persentase_kehadiran }}%
                            </td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60">
                                    {{ $akd->label_risiko_aktual }}
                                </span>
                            </td>
                            <td class="py-3 text-right">
                                <a href="{{ route('prodi.prediksi.single') }}?mahasiswa_id={{ $akd->mahasiswa_id }}" class="px-2.5 py-1 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold text-[11px] border border-brand-200/60 transition">
                                    Simulasi PA ➜
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400">Tidak ada mahasiswa yang berada dalam kategori risiko tinggi saat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
