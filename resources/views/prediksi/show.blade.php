@extends('layouts.app')

@section('title', 'Hasil Prediksi Risiko: ' . $prediksi->nama_mahasiswa)
@section('subtitle', 'Hasil klasifikasi Decision Tree C4.5 & Rekomendasi Bimbingan Akademik')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Action Toolbar (Print & Back) -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ auth()->user()->isAdmin() ? route('admin.prediksi.index') : route('prodi.prediksi.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1.5"></i>
            Kembali ke Riwayat Prediksi
        </a>

        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition flex items-center">
                <i data-lucide="printer" class="w-3.5 h-3.5 mr-1.5 text-slate-600"></i>
                Cetak Hasil (PDF)
            </button>
            <a href="{{ auth()->user()->isAdmin() ? route('admin.prediksi.single') : route('prodi.prediksi.single') }}" class="px-3.5 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-xs font-bold text-white shadow-xs transition flex items-center">
                <i data-lucide="plus" class="w-3.5 h-3.5 mr-1.5"></i>
                Prediksi Lainnya
            </a>
        </div>
    </div>

    <!-- Main Certificate Result Card -->
    <div class="bg-white p-6 sm:p-10 rounded-3xl border border-slate-200/80 shadow-xs space-y-6 relative overflow-hidden">
        
        <!-- Header Information -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-2xl bg-brand-50 border border-brand-200 flex items-center justify-center text-brand-700 shadow-xs">
                    <i data-lucide="award" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">Lembar Hasil Klasifikasi Risiko Akademik</h3>
                    <p class="text-xs text-slate-500">SIPRA-C4.5 • Program Studi Sistem Informasi UIN Raden Intan Lampung</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[10px] text-slate-400 font-mono">ID: #PRED-{{ str_pad($prediksi->id, 5, '0', STR_PAD_LEFT) }}</p>
                <p class="text-xs text-slate-600 font-medium">{{ $prediksi->created_at->format('d F Y, H:i') }} WIB</p>
            </div>
        </div>

        <!-- Student Profile Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
            <div>
                <p class="text-[10px] text-slate-400 uppercase font-bold">Nama Mahasiswa</p>
                <p class="text-sm font-bold text-slate-900 mt-0.5">{{ $prediksi->nama_mahasiswa }}</p>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 uppercase font-bold">NIM</p>
                <p class="text-sm font-mono font-bold text-brand-700 mt-0.5">{{ $prediksi->nim ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 uppercase font-bold">Semester</p>
                <p class="text-sm font-bold text-slate-900 mt-0.5">Semester {{ $prediksi->semester }}</p>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 uppercase font-bold">Model C4.5</p>
                <p class="text-xs font-bold text-purple-700 truncate mt-0.5">{{ $prediksi->model->nama_model ?? 'Model Default' }}</p>
            </div>
        </div>

        <!-- Prominent Risk Classification Banner -->
        @php
            $risk = $prediksi->hasil_klasifikasi;
            $riskBg = match($risk) {
                'Risiko Rendah' => 'bg-brand-50 border-brand-200 text-brand-800',
                'Risiko Sedang' => 'bg-amber-50 border-amber-200 text-amber-800',
                default => 'bg-rose-50 border-rose-200 text-rose-800',
            };
            $riskTitle = match($risk) {
                'Risiko Rendah' => 'text-brand-800',
                'Risiko Sedang' => 'text-amber-800',
                default => 'text-rose-800',
            };
        @endphp
        <div class="p-6 rounded-2xl border {{ $riskBg }} flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
            <div class="space-y-1">
                <span class="text-[11px] uppercase tracking-wider font-bold opacity-80">Hasil Klasifikasi Algoritma C4.5</span>
                <h4 class="text-3xl font-black {{ $riskTitle }}">{{ $risk }}</h4>
                <p class="text-xs font-medium opacity-90">
                    Status Drop Out: <strong>{{ $prediksi->status_do }}</strong> • Tingkat Keyakinan (Confidence): <strong>{{ $prediksi->confidence_score }}%</strong>
                </p>
            </div>
            <div class="px-5 py-3 rounded-2xl bg-white border border-slate-200 text-center min-w-[150px] shadow-xs">
                <p class="text-[10px] text-slate-400 uppercase font-bold">Confidence</p>
                <p class="text-2xl font-black text-slate-900">{{ $prediksi->confidence_score }}%</p>
            </div>
        </div>

        <!-- Input Variables Breakdown -->
        @php $input = $prediksi->input_params_json ?? []; @endphp
        <div class="space-y-2">
            <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Variabel Prediktor yang Digunakan:</h5>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-500 block text-[10px]">IPK Kumulatif</span>
                    <strong class="text-slate-900 text-sm">{{ $input['ipk'] ?? '-' }}</strong>
                    <span class="text-[10px] text-slate-500 block">({{ $input['kategori_ipk'] ?? '-' }})</span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-500 block text-[10px]">IPS Terakhir</span>
                    <strong class="text-slate-900 text-sm">{{ $input['ips'] ?? '-' }}</strong>
                    <span class="text-[10px] text-slate-500 block">({{ $input['kategori_ips'] ?? '-' }})</span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-500 block text-[10px]">SKS Diambil</span>
                    <strong class="text-slate-900 text-sm">{{ $input['sks_semester'] ?? '-' }} SKS</strong>
                    <span class="text-[10px] text-slate-500 block">({{ $input['kategori_sks'] ?? '-' }})</span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-500 block text-[10px]">Kehadiran Kuliah</span>
                    <strong class="text-slate-900 text-sm">{{ $input['persentase_kehadiran'] ?? '-' }}%</strong>
                    <span class="text-[10px] text-slate-500 block">({{ $input['kategori_kehadiran'] ?? '-' }})</span>
                </div>
            </div>
        </div>

        <!-- Matched Rule Card if any -->
        @if($prediksi->rule)
            <div class="p-4 rounded-2xl bg-purple-50/50 border border-purple-200 space-y-1">
                <p class="text-[10px] font-bold text-purple-800 uppercase tracking-wider">Aturan Pohon Keputusan yang Terpicu:</p>
                <p class="text-xs font-mono text-purple-950">{{ $prediksi->rule->rule_text }}</p>
                <p class="text-[10px] text-purple-700">Kode: {{ $prediksi->rule->rule_code }} • Support: {{ $prediksi->rule->support_samples }} sampel</p>
            </div>
        @endif

        <!-- Proactive Academic Intervention Recommendation Box -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
            <div class="flex items-center space-x-2">
                <i data-lucide="lightbulb" class="w-4 h-4 text-amber-600"></i>
                <h5 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Rekomendasi Intervensi Akademik (Dosen PA / Prodi):</h5>
            </div>
            <p class="text-xs text-slate-700 leading-relaxed bg-white p-4 rounded-xl border border-slate-200">
                {{ $prediksi->rekomendasi_akademik }}
            </p>
        </div>

        <!-- Signature Footer (for Print Format) -->
        <div class="pt-8 border-t border-slate-200 grid grid-cols-2 text-center text-xs text-slate-600">
            <div>
                <p>Mengetahui,</p>
                <p class="font-bold text-slate-900 mt-1">Dosen Pembimbing Akademik</p>
                <div class="h-16"></div>
                <p class="font-bold text-slate-900 underline">( .................................................... )</p>
                <p class="text-[10px]">NIP. ........................................</p>
            </div>
            <div>
                <p>Bandar Lampung, {{ date('d F Y') }}</p>
                <p class="font-bold text-slate-900 mt-1">Ketua Program Studi Sistem Informasi</p>
                <div class="h-16"></div>
                <p class="font-bold text-slate-900 underline">Dr. Kaprodi Sistem Informasi, M.Kom.</p>
                <p class="text-[10px]">NIP. 197905152008011005</p>
            </div>
        </div>

    </div>

</div>
@endsection
