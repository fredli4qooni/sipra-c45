@extends('layouts.app')

@section('title', 'Portal Akademik Mahasiswa')
@section('subtitle', 'Evaluasi Diri & Deteksi Dini Risiko Akademik')

@section('content')
<div class="space-y-6">

    @if(!$mahasiswa)
        <div class="p-6 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-center space-x-3">
            <i data-lucide="alert-triangle" class="w-6 h-6 flex-shrink-0 text-amber-600"></i>
            <div>
                <h4 class="font-bold">Data Mahasiswa Belum Ditautkan</h4>
                <p class="text-xs text-slate-600 mt-0.5">Akun Anda belum terhubung dengan data master mahasiswa. Silakan hubungi Admin Akademik untuk sinkronisasi NIM.</p>
            </div>
        </div>
    @else
        <!-- Student Info Header Card -->
        <div class="p-6 sm:p-8 rounded-xl bg-white border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center md:items-start justify-between gap-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-5 text-center sm:text-left">
                @if(auth()->user()->avatar_url)
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ $mahasiswa->nama }}" class="w-16 h-16 rounded-xl object-cover border border-slate-200 shadow-xs flex-shrink-0">
                @else
                    <div class="w-16 h-16 rounded-xl bg-brand-50 border border-brand-200 text-brand-700 font-extrabold text-2xl flex items-center justify-center shadow-xs flex-shrink-0 font-display">
                        {{ strtoupper(substr($mahasiswa->nama, 0, 2)) }}
                    </div>
                @endif
                <div>
                    <h3 class="text-xl font-extrabold text-slate-900 font-display">{{ $mahasiswa->nama }}</h3>
                    <p class="text-sm font-mono text-brand-700 font-bold mt-0.5">{{ $mahasiswa->nim }}</p>
                    <p class="text-xs text-slate-500 mt-1">
                        Program Studi Sistem Informasi • Angkatan {{ $mahasiswa->angkatan }} • Jalur {{ $mahasiswa->jalur_masuk ?? '-' }}
                    </p>
                </div>
            </div>

            <!-- Prominent Risk Status Badge -->
            @php
                $statusRisiko = $latestAkademik->label_risiko_aktual ?? 'Belum Dianalisis';
            @endphp
            <div class="p-4 rounded-xl border text-center min-w-[200px]
                @if($statusRisiko === 'Risiko Rendah') bg-brand-50 border-brand-200 text-brand-800
                @elseif($statusRisiko === 'Risiko Sedang') bg-amber-50 border-amber-200 text-amber-800
                @elseif($statusRisiko === 'Risiko Tinggi') bg-rose-50 border-rose-200 text-rose-800
                @else bg-slate-50 border-slate-200 text-slate-600
                @endif">
                <p class="text-[10px] uppercase tracking-wider font-bold">Status Deteksi Dini</p>
                <div class="inline-flex items-center justify-center gap-1.5 mt-0.5">
                    @if($statusRisiko === 'Risiko Rendah')
                        <i data-lucide="shield-check" class="w-5 h-5 text-brand-600"></i>
                    @elseif($statusRisiko === 'Risiko Sedang')
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600"></i>
                    @elseif($statusRisiko === 'Risiko Tinggi')
                        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600"></i>
                    @endif
                    <h4 class="text-lg font-black font-display">{{ $statusRisiko }}</h4>
                </div>
                <p class="text-[10px] text-slate-500 mt-0.5">Semester {{ $latestAkademik->semester ?? '-' }}</p>
            </div>
        </div>

        <!-- Academic Performance Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            <!-- IPK -->
            <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-xs text-center space-y-2">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">IPK Kumulatif</p>
                <h4 class="text-3xl font-extrabold text-slate-900 font-display">{{ $latestAkademik->ipk ?? '0.00' }}</h4>
                <span class="inline-block px-2.5 py-0.5 rounded-md text-[11px] font-bold
                    @if(($latestAkademik->ipk ?? 0) >= 3.25) bg-brand-50 text-brand-700 border border-brand-200
                    @elseif(($latestAkademik->ipk ?? 0) >= 2.75) bg-amber-50 text-amber-700 border border-amber-200
                    @else bg-rose-50 text-rose-700 border border-rose-200
                    @endif">
                    Kategori: {{ $latestAkademik->kategori_ipk ?? '-' }}
                </span>
            </div>

            <!-- SKS Selesai -->
            <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-xs text-center space-y-2">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">SKS Semester</p>
                <h4 class="text-3xl font-extrabold text-slate-900 font-display">{{ $latestAkademik->sks_semester ?? 0 }} SKS</h4>
                <span class="inline-block px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    SKS Gagal: {{ $latestAkademik->sks_tidak_lulus ?? 0 }}
                </span>
            </div>

            <!-- Kehadiran -->
            <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-xs text-center space-y-2">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Kehadiran Kuliah</p>
                <h4 class="text-3xl font-extrabold text-slate-900 font-display">{{ $latestAkademik->persentase_kehadiran ?? 0 }}%</h4>
                <span class="inline-block px-2.5 py-0.5 rounded-md text-[11px] font-bold
                    @if(($latestAkademik->persentase_kehadiran ?? 0) >= 85) bg-brand-50 text-brand-700 border border-brand-200
                    @elseif(($latestAkademik->persentase_kehadiran ?? 0) >= 75) bg-amber-50 text-amber-700 border border-amber-200
                    @else bg-rose-50 text-rose-700 border border-rose-200
                    @endif">
                    Kategori: {{ $latestAkademik->kategori_kehadiran ?? '-' }}
                </span>
            </div>

        </div>

        <!-- Academic Recommendations Box -->
        <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs space-y-3">
            <div class="flex items-center space-x-2 pb-2 border-b border-slate-100">
                <i data-lucide="lightbulb" class="w-4 h-4 text-amber-500"></i>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Rekomendasi Rencana Studi</h4>
            </div>

            <div class="p-4 rounded-lg bg-brand-50/50 border border-brand-200/80 text-xs text-slate-700 leading-relaxed">
                <p class="font-semibold text-brand-900 mb-1 flex items-center gap-1.5">
                    <i data-lucide="{{ $configRisiko?->icon ?? 'award' }}" class="w-4 h-4 text-brand-600 flex-shrink-0"></i>
                    <span>{{ $configRisiko?->label_badge ?? 'Panduan Rencana Studi' }}</span>
                </p>
                <p>{{ $configRisiko?->rekomendasi_studi ?? 'Performa akademik Anda sangat memuaskan. Anda berada di jalur yang tepat untuk lulus tepat waktu pada semester 8.' }}</p>
            </div>
        </div>

        <!-- Dosen PA Consultation & Advisory Card -->
        @php
            $dosen = $mahasiswa->dosenPa;
            $rawPhone = $dosen->phone ?? '';
            $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            }
            $waMessage = $configRisiko 
                ? $configRisiko->formatWaMessage($mahasiswa, $dosen, $latestAkademik)
                : "Assalamu'alaikum Wr. Wb. Bapak/Ibu " . ($dosen->name ?? 'Dosen PA') . ", perkenalkan saya " . $mahasiswa->nama . " (NPM: " . $mahasiswa->nim . "), mahasiswa bimbingan akademik Anda di Prodi Sistem Informasi. Sehubungan dengan hasil evaluasi akademik SIPRA-C4.5 (Status: " . $statusRisiko . "), saya bermaksud memohon izin dan arahan untuk berkonsultasi mengenai rencana studi saya. Terima kasih.";
            $waUrl = !empty($cleanPhone) ? "https://wa.me/{$cleanPhone}?text=" . rawurlencode($waMessage) : null;
            $mailSubject = "Konsultasi Bimbingan Akademik - " . $mahasiswa->nama . " (" . $mahasiswa->nim . ")";
            $mailBody = "Yth. Bapak/Ibu " . ($dosen->name ?? 'Dosen Pembimbing Akademik') . ",\n\nPerkenalkan saya mahasiswa bimbingan akademik Anda:\n- Nama: " . $mahasiswa->nama . "\n- NPM: " . $mahasiswa->nim . "\n- Program Studi: Sistem Informasi\n- Status Deteksi Dini: " . $statusRisiko . "\n- IPK Kumulatif: " . ($latestAkademik->ipk ?? '0.00') . "\n- Kehadiran: " . ($latestAkademik->persentase_kehadiran ?? '0') . "%\n\nSehubungan dengan hasil evaluasi tersebut, saya memohon izin untuk berkonsultasi mengenai kelanjutan dan strategi rencana studi saya.\n\nTerima kasih,\n" . $mahasiswa->nama;
            $mailUrl = !empty($dosen?->email) ? "mailto:" . $dosen->email . "?subject=" . rawurlencode($mailSubject) . "&body=" . rawurlencode($mailBody) : null;
        @endphp

        <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
            <!-- Header Section -->
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center flex-shrink-0 shadow-2xs">
                        <i data-lucide="user-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-900 font-display">Bimbingan & Konsultasi Dosen Pembimbing Akademik (Dosen PA)</h4>
                        <p class="text-xs text-slate-500">Arahan resmi untuk konsultasi studi mahasiswa kepada Dosen Pembimbing</p>
                    </div>
                </div>
                @if($dosen)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200 self-start sm:self-auto">
                        <span class="w-2 h-2 rounded-full bg-brand-600 mr-2 animate-pulse"></span>
                        PA Aktif Terhubung
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 self-start sm:self-auto">
                        <i data-lucide="help-circle" class="w-3.5 h-3.5 mr-1.5"></i>
                        Belum Ditugaskan PA
                    </span>
                @endif
            </div>

            <div class="p-6 space-y-6">
                @if($dosen)
                    <!-- Advisor Profile & Direct Contact Row -->
                    <div class="p-5 rounded-xl bg-slate-50/80 border border-slate-200/90 flex flex-col md:flex-row md:items-center justify-between gap-5">
                        <div class="flex items-start space-x-4">
                            <div class="w-14 h-14 rounded-xl bg-brand-600 text-white font-extrabold text-xl flex items-center justify-center flex-shrink-0 shadow-xs font-display">
                                {{ strtoupper(substr($dosen->name, 0, 2)) }}
                            </div>
                            <div class="space-y-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-white border border-slate-200 text-slate-700 shadow-2xs">
                                    Dosen Pembimbing Akademik
                                </span>
                                <h5 class="text-base font-extrabold text-slate-900 font-display">{{ $dosen->name }}</h5>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500 font-mono">
                                    <span>NIP/NIDN: <strong class="text-slate-700">{{ $dosen->nim_nip ?? '-' }}</strong></span>
                                    <span>•</span>
                                    <span>Email: <strong class="text-slate-700">{{ $dosen->email }}</strong></span>
                                    @if($dosen->phone)
                                        <span>•</span>
                                        <span>No. HP/WA: <strong class="text-slate-700">{{ $dosen->phone }}</strong></span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Direct Contact Action Buttons -->
                        <div class="flex flex-wrap items-center gap-2.5 pt-2 md:pt-0">
                            @if($waUrl)
                                <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-xs hover:shadow-sm transition inline-flex items-center space-x-2 cursor-pointer">
                                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                                    <span>Konsultasi via WhatsApp</span>
                                </a>
                            @endif

                            @if($mailUrl)
                                <a href="{{ $mailUrl }}" class="px-4 py-2.5 rounded-lg bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 hover:text-slate-900 font-bold text-xs shadow-2xs transition inline-flex items-center space-x-2 cursor-pointer">
                                    <i data-lucide="mail" class="w-4 h-4 text-slate-500"></i>
                                    <span>Kirim Email</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Contextual Guidance Card Based on Configured Risk Level -->
                    @php
                        $alertBg = match($configRisiko?->warna ?? 'brand') {
                            'rose' => 'bg-rose-50/60 border-rose-200/90',
                            'amber' => 'bg-amber-50/60 border-amber-200/90',
                            default => 'bg-brand-50/60 border-brand-200/90',
                        };
                        $alertIconBg = match($configRisiko?->warna ?? 'brand') {
                            'rose' => 'bg-rose-500 text-white',
                            'amber' => 'bg-amber-500 text-white',
                            default => 'bg-brand-600 text-white',
                        };
                        $alertTitleColor = match($configRisiko?->warna ?? 'brand') {
                            'rose' => 'text-rose-900',
                            'amber' => 'text-amber-900',
                            default => 'text-brand-900',
                        };
                        $alertSubColor = match($configRisiko?->warna ?? 'brand') {
                            'rose' => 'text-rose-700',
                            'amber' => 'text-amber-700',
                            default => 'text-brand-700',
                        };
                    @endphp

                    <div class="p-5 rounded-xl border {{ $alertBg }} space-y-3">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-7 h-7 rounded-lg {{ $alertIconBg }} flex items-center justify-center flex-shrink-0">
                                <i data-lucide="{{ $configRisiko?->icon ?? 'alert-circle' }}" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h5 class="text-xs font-bold {{ $alertTitleColor }} uppercase tracking-wider">{{ $configRisiko?->label_badge ?? 'Panduan Bimbingan' }}</h5>
                                <p class="text-[11px] {{ $alertSubColor }}">{{ $configRisiko?->deskripsi_singkat ?? 'Panduan tindak lanjut evaluasi akademik mahasiswa' }}</p>
                            </div>
                        </div>

                        <p class="text-xs {{ $alertTitleColor }}/90 leading-relaxed">
                            {{ $configRisiko?->pesan_peringatan ?? 'Segera koordinasikan rencana studi semester depan bersama Dosen PA Anda.' }}
                        </p>

                        @if(!empty($configRisiko?->panduan_konsultasi_pa))
                            <div class="bg-white p-4 rounded-lg border border-slate-200 text-xs space-y-2">
                                <p class="font-bold text-slate-900 flex items-center gap-1.5">
                                    <i data-lucide="check-square" class="w-3.5 h-3.5 text-brand-600"></i>
                                    <span>Panduan & Hal yang Perlu Dikonsultasikan ke Dosen PA:</span>
                                </p>
                                <ul class="list-disc pl-5 space-y-1 text-slate-600 text-[11px] leading-relaxed">
                                    @foreach($configRisiko->panduan_konsultasi_pa as $poin)
                                        <li>{{ $poin }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                @else
                    <!-- No Advisor Assigned Notice -->
                    <div class="p-5 rounded-xl bg-amber-50/70 border border-amber-200 text-center space-y-3">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center mx-auto">
                            <i data-lucide="user-x" class="w-6 h-6"></i>
                        </div>
                        <div class="max-w-md mx-auto space-y-1">
                            <h5 class="text-sm font-bold text-slate-900">Dosen Pembimbing Akademik Belum Ditetapkan</h5>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Akun Anda belum memiliki data Dosen Pembimbing Akademik (Dosen PA). Silakan hubungi bagian Administrasi Akademik / Tata Usaha Program Studi Sistem Informasi untuk sinkronisasi pembimbing akademik Anda.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recorded Dosen PA Counseling History (If Any) -->
        @if($latestAkademik && $latestAkademik->status_intervensi && $latestAkademik->status_intervensi !== 'Belum Ditindaklanjuti')
            <div class="bg-white p-6 rounded-xl border border-indigo-200/90 shadow-xs space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center">
                            <i data-lucide="calendar-check" class="w-4 h-4"></i>
                        </div>
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Catatan Riwayat Bimbingan Terjadwal</h4>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-bold
                        @if($latestAkademik->status_intervensi === 'Selesai / Teratasi') bg-brand-50 text-brand-700 border border-brand-200
                        @elseif($latestAkademik->status_intervensi === 'Sedang Bimbingan') bg-blue-50 text-blue-700 border border-blue-200
                        @else bg-amber-50 text-amber-700 border border-amber-200 @endif">
                        {{ $latestAkademik->status_intervensi }}
                    </span>
                </div>

                <div class="p-4 rounded-lg bg-indigo-50/40 border border-indigo-100 text-xs space-y-2">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                        <p class="font-bold text-slate-900">
                            Bentuk Intervensi: <span class="text-indigo-900 font-semibold">{{ $latestAkademik->tindakan_intervensi ?? 'Konseling Akademik' }}</span>
                        </p>
                        @if($latestAkademik->tanggal_intervensi)
                            <p class="text-[11px] text-slate-500">
                                Tanggal: <strong class="text-slate-700">{{ $latestAkademik->tanggal_intervensi->format('d F Y') }}</strong>
                            </p>
                        @endif
                    </div>
                    @if($latestAkademik->catatan_intervensi)
                        <div class="pt-2 border-t border-indigo-100/80">
                            <p class="text-[11px] font-semibold text-slate-700">Catatan & Kesepakatan Solusi Bimbingan:</p>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed italic bg-white p-3 rounded-lg border border-slate-200">
                                "{{ $latestAkademik->catatan_intervensi }}"
                            </p>
                        </div>
                    @endif
                    @if($latestAkademik->dosenPa)
                        <p class="text-[10px] text-slate-400 text-right">Dosen Pembimbing: {{ $latestAkademik->dosenPa->name }}</p>
                    @endif
                </div>
            </div>
        @endif

    @endif

</div>
@endsection
