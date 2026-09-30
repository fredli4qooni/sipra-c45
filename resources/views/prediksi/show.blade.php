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
    <div class="bg-white p-6 sm:p-10 rounded-xl border border-slate-200/80 shadow-xs space-y-6 relative overflow-hidden">
        
        <!-- Header Information -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-lg bg-brand-50 border border-brand-200 flex items-center justify-center text-brand-700 shadow-xs">
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
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 p-4 rounded-lg bg-slate-50 border border-slate-200">
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
        <div class="p-6 rounded-xl border {{ $riskBg }} flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
            <div class="space-y-1">
                <span class="text-[11px] uppercase tracking-wider font-bold opacity-80">Hasil Klasifikasi Algoritma C4.5</span>
                <h4 class="text-3xl font-black {{ $riskTitle }}">{{ $risk }}</h4>
                <p class="text-xs font-medium opacity-90">
                    Status Drop Out: <strong>{{ $prediksi->status_do }}</strong> • Tingkat Keyakinan (Confidence): <strong>{{ $prediksi->confidence_score }}%</strong>
                </p>
            </div>
            <div class="px-5 py-3 rounded-lg bg-white border border-slate-200 text-center min-w-[150px] shadow-xs">
                <p class="text-[10px] text-slate-400 uppercase font-bold">Confidence</p>
                <p class="text-2xl font-black text-slate-900">{{ $prediksi->confidence_score }}%</p>
            </div>
        </div>

        <!-- Input Variables Breakdown -->
        @php $input = $prediksi->input_params_json ?? []; @endphp
        <div class="space-y-2">
            <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Variabel Prediktor yang Digunakan:</h5>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                    <span class="text-slate-500 block text-[10px]">IPK Kumulatif</span>
                    <strong class="text-slate-900 text-sm">{{ $input['ipk'] ?? '-' }}</strong>
                    <span class="text-[10px] text-slate-500 block">({{ $input['kategori_ipk'] ?? '-' }})</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                    <span class="text-slate-500 block text-[10px]">IPS Terakhir</span>
                    <strong class="text-slate-900 text-sm">{{ $input['ips'] ?? '-' }}</strong>
                    <span class="text-[10px] text-slate-500 block">({{ $input['kategori_ips'] ?? '-' }})</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                    <span class="text-slate-500 block text-[10px]">SKS Diambil</span>
                    <strong class="text-slate-900 text-sm">{{ $input['sks_semester'] ?? '-' }} SKS</strong>
                    <span class="text-[10px] text-slate-500 block">({{ $input['kategori_sks'] ?? '-' }})</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                    <span class="text-slate-500 block text-[10px]">Kehadiran Kuliah</span>
                    <strong class="text-slate-900 text-sm">{{ $input['persentase_kehadiran'] ?? '-' }}%</strong>
                    <span class="text-[10px] text-slate-500 block">({{ $input['kategori_kehadiran'] ?? '-' }})</span>
                </div>
            </div>
        </div>

        <!-- Matched Rule Card if any -->
        @if($prediksi->rule)
            <div class="p-4 rounded-lg bg-purple-50/50 border border-purple-200 space-y-1">
                <p class="text-[10px] font-bold text-purple-800 uppercase tracking-wider">Aturan Pohon Keputusan yang Terpicu:</p>
                <p class="text-xs font-mono text-purple-950">{{ $prediksi->rule->rule_text }}</p>
                <p class="text-[10px] text-purple-700">Kode: {{ $prediksi->rule->rule_code }} • Support: {{ $prediksi->rule->support_samples }} sampel</p>
            </div>
        @endif

        <!-- Explainable AI (XAI): Decision Path Trace Section -->
        @if(isset($decisionTrace) && ($decisionTrace['has_trace'] ?? false))
            <div class="p-6 rounded-xl bg-slate-50/80 border border-slate-200/90 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-200">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="git-branch" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h5 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Penelusuran Jalur Pohon Keputusan (Explainable AI - XAI)</h5>
                            <p class="text-[11px] text-slate-500">Transparansi inferensi logika pohon C4.5 dari Root hingga Leaf Node</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200 self-start sm:self-auto">
                        <i data-lucide="route" class="w-3 h-3 mr-1"></i>
                        {{ count($decisionTrace['steps']) }} Simpul Dilalui
                    </span>
                </div>

                <!-- Stepper Flow -->
                <div class="relative pl-6 space-y-6 before:absolute before:left-3 before:top-3 before:bottom-3 before:w-0.5 before:bg-slate-200">
                    @foreach($decisionTrace['steps'] as $idx => $step)
                        <div class="relative">
                            <!-- Step Dot -->
                            <div class="absolute -left-6 top-1 w-6 h-6 rounded-full bg-white border-2 border-purple-600 flex items-center justify-center text-[10px] font-bold text-purple-700 shadow-xs">
                                {{ $step['step'] }}
                            </div>

                            <div class="p-4 rounded-lg bg-white border border-slate-200 shadow-2xs space-y-2.5">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase {{ $step['step'] === 1 ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-purple-100 text-purple-800 border border-purple-200' }}">
                                            {{ $step['step'] === 1 ? 'Root Node' : 'Internal Node' }}
                                        </span>
                                        <h6 class="text-xs font-bold text-slate-900">{{ $step['attribute_label'] }}</h6>
                                    </div>
                                    <div class="flex items-center space-x-2 text-[10px] text-slate-500">
                                        @if($step['gain_ratio'] !== null)
                                            <span class="px-1.5 py-0.5 rounded-md bg-slate-100 border border-slate-200 font-mono">Gain Ratio: {{ number_format($step['gain_ratio'], 4) }}</span>
                                        @endif
                                        <span>{{ $step['samples_count'] ?? 0 }} Sampel</span>
                                    </div>
                                </div>

                                <!-- Evaluation and Branching details -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                    <div class="p-2.5 rounded-lg bg-purple-50/60 border border-purple-100">
                                        <p class="text-[10px] text-purple-700 font-medium">Nilai Mahasiswa Terdeteksi:</p>
                                        <p class="text-xs font-bold text-purple-950 mt-0.5">
                                            {{ $step['raw_value'] }}
                                            <span class="font-normal text-purple-700">({{ $step['category_value'] }})</span>
                                        </p>
                                    </div>
                                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                                        <p class="text-[10px] text-slate-500 font-medium">Pilihan Cabang Simpul:</p>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($step['available_branches'] as $b)
                                                @php $isChosen = ($b === $step['category_value']); @endphp
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-bold {{ $isChosen ? 'bg-purple-600 text-white shadow-2xs' : 'bg-white text-slate-500 border border-slate-200 opacity-60' }}">
                                                    @if($isChosen)<i data-lucide="check" class="w-3 h-3 mr-0.5"></i>@endif
                                                    {{ $b }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                @if(!empty($step['distribution']))
                                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500">
                                        <span>Distribusi Kelas di Simpul Ini:</span>
                                        <div class="flex items-center space-x-2">
                                            @foreach($step['distribution'] as $cls => $cnt)
                                                <span class="font-medium font-mono
                                                    @if($cls === 'Risiko Rendah') text-brand-700
                                                    @elseif($cls === 'Risiko Sedang') text-amber-700
                                                    @else text-rose-700 @endif">
                                                    {{ $cls }}: <strong>{{ $cnt }}</strong>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <!-- Terminal Leaf Node in Stepper -->
                    @if(isset($decisionTrace['terminal']))
                        @php
                            $term = $decisionTrace['terminal'];
                            $termBg = match($term['decision']) {
                                'Risiko Rendah' => 'bg-brand-50 border-brand-300 text-brand-900',
                                'Risiko Sedang' => 'bg-amber-50 border-amber-300 text-amber-900',
                                default => 'bg-rose-50 border-rose-300 text-rose-900',
                            };
                            $termIcon = match($term['decision']) {
                                'Risiko Rendah' => 'shield-check',
                                'Risiko Sedang' => 'alert-triangle',
                                default => 'alert-circle',
                            };
                        @endphp
                        <div class="relative">
                            <div class="absolute -left-6 top-1 w-6 h-6 rounded-full bg-white border-2 border-brand-600 flex items-center justify-center text-[10px] font-bold text-brand-700 shadow-xs">
                                <i data-lucide="flag" class="w-3 h-3 text-brand-600"></i>
                            </div>

                            <div class="p-4 rounded-lg border {{ $termBg }} shadow-2xs space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase bg-white/80 border border-current shadow-2xs">
                                        Terminal Leaf Node (Daun Keputusan)
                                    </span>
                                    <span class="text-[10px] font-bold">Keyakinan (Confidence): {{ $term['confidence'] }}%</span>
                                </div>
                                <div class="flex items-center space-x-3 pt-1">
                                    <div class="w-9 h-9 rounded-lg bg-white/80 border border-current flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="{{ $termIcon }}" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-bold opacity-80">Klasifikasi Akhir</p>
                                        <h6 class="text-base font-black">{{ $term['decision'] }}</h6>
                                    </div>
                                    <div class="ml-auto text-right text-xs">
                                        <p class="text-[10px] opacity-80">Didukung Oleh</p>
                                        <p class="font-bold">{{ $term['samples_count'] }} Data Sampel Latih</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Academic Inference Narrative Box -->
                @if(!empty($decisionTrace['narratives']))
                    <div class="p-4 rounded-lg bg-white border border-slate-200 space-y-2 text-xs">
                        <div class="flex items-center space-x-2 text-slate-800 font-bold">
                            <i data-lucide="file-text" class="w-3.5 h-3.5 text-brand-600"></i>
                            <span>Narasi Logika Inferensi (Explainable AI - XAI Narrative):</span>
                        </div>
                        <ol class="list-decimal pl-5 space-y-1.5 text-slate-600 leading-relaxed text-[11px]">
                            @foreach($decisionTrace['narratives'] as $nar)
                                <li>{!! $nar !!}</li>
                            @endforeach
                        </ol>
                    </div>
                @endif
            </div>
        @endif

        <!-- Proactive Academic Intervention Recommendation Box -->
        <div class="p-5 rounded-lg bg-slate-50 border border-slate-200 space-y-3">
            <div class="flex items-center space-x-2">
                <i data-lucide="lightbulb" class="w-4 h-4 text-amber-600"></i>
                <h5 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Rekomendasi & Saran Bimbingan Akademik (Dosen PA):</h5>
            </div>
            <p class="text-xs text-slate-700 leading-relaxed bg-white p-4 rounded-lg border border-slate-200">
                {{ $prediksi->rekomendasi_akademik }}
            </p>

            @php
                $pa = $prediksi->mahasiswa?->dosenPa;
                $paPhone = $pa ? preg_replace('/[^0-9]/', '', $pa->phone ?? '') : '';
                if (str_starts_with($paPhone, '0')) {
                    $paPhone = '62' . substr($paPhone, 1);
                }
                $predWaMsg = "Assalamu'alaikum Wr. Wb. Bapak/Ibu " . ($pa->name ?? 'Dosen PA') . ", perkenalkan saya " . $prediksi->nama_mahasiswa . " (NPM: " . ($prediksi->nim ?? '-') . "). Sehubungan dengan lembar hasil evaluasi prediksi akademik SIPRA-C4.5 (Status: " . $prediksi->hasil_klasifikasi . "), saya bermaksud memohon izin dan arahan untuk berkonsultasi mengenai kelanjutan rencana studi saya. Terima kasih.";
                $predWaUrl = !empty($paPhone) ? "https://wa.me/{$paPhone}?text=" . rawurlencode($predWaMsg) : null;
            @endphp

            @if($pa)
                <div class="p-4 rounded-lg bg-emerald-50/70 border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 font-bold text-xs shadow-2xs">
                            <i data-lucide="user-check" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">Dosen Pembimbing Akademik: <span class="text-emerald-950 font-extrabold">{{ $pa->name }}</span></p>
                            <p class="text-[11px] text-slate-500 font-mono">NIP/NIDN: {{ $pa->nim_nip ?? '-' }} • Email: {{ $pa->email }}</p>
                        </div>
                    </div>
                    @if($predWaUrl)
                        <a href="{{ $predWaUrl }}" target="_blank" rel="noopener noreferrer" class="no-print px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-2xs transition inline-flex items-center space-x-1.5 self-start sm:self-auto cursor-pointer">
                            <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                            <span>Hubungi Dosen PA via WhatsApp</span>
                        </a>
                    @endif
                </div>
            @endif
        </div>

        <!-- Signature Footer (for Print Format) -->
        <div class="pt-8 border-t border-slate-200 grid grid-cols-2 text-center text-xs text-slate-600">
            <div>
                <p>Mengetahui,</p>
                <p class="font-bold text-slate-900 mt-1">Dosen Pembimbing Akademik</p>
                <div class="h-16"></div>
                <p class="font-bold text-slate-900 underline">
                    ( {{ $prediksi->mahasiswa?->dosenPa?->name ?? '....................................................' }} )
                </p>
                <p class="text-[10px]">NIP. {{ $prediksi->mahasiswa?->dosenPa?->nim_nip ?? '........................................' }}</p>
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
