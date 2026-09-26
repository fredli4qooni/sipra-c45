@extends('layouts.app')

@section('title', 'Pusat Bantuan & Panduan Sistem')
@section('subtitle', 'Dokumentasi operasional, panduan alur kerja multi-peran, dan penjelasan data mining C4.5')

@section('content')
<div class="space-y-6">

    <!-- Hero Banner Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200">
                    <i data-lucide="book-open" class="w-3.5 h-3.5 mr-1.5 text-brand-600"></i>
                    Buku Panduan & FAQ
                </span>
            </div>
            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Pusat Informasi & Bantuan SIPRA-C4.5</h3>
            <p class="text-xs text-slate-500 max-w-2xl leading-relaxed">
                Pelajari cara kerja sistem, formulasi matematis Decision Tree C4.5, panduan operasional berdasarkan peran pengguna, dan petunjuk penyelesaian masalah teknis.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="#faq" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition flex items-center">
                <i data-lucide="help-circle" class="w-4 h-4 mr-1.5 text-slate-500"></i>
                Pertanyaan Umum (FAQ)
            </a>
        </div>
    </div>

    <!-- 4 Quick Category Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <a href="#algoritma" class="bg-white p-5 rounded-2xl border border-slate-200/80 hover:border-brand-300 shadow-xs transition group space-y-2">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition">
                <i data-lucide="cpu" class="w-5 h-5"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-900 group-hover:text-brand-700 transition">Algoritma C4.5</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Formula Entropy, Gain Ratio, pembentukan pohon & ekstraksi aturan.</p>
        </a>

        <a href="#panduan-peran" class="bg-white p-5 rounded-2xl border border-slate-200/80 hover:border-brand-300 shadow-xs transition group space-y-2">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-900 group-hover:text-brand-700 transition">Panduan Multi-Role</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Alur kerja untuk Admin, Kaprodi, Dosen PA, dan Mahasiswa.</p>
        </a>

        <a href="#kriteria-risiko" class="bg-white p-5 rounded-2xl border border-slate-200/80 hover:border-brand-300 shadow-xs transition group space-y-2">
            <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center group-hover:bg-brand-600 group-hover:text-white transition">
                <i data-lucide="shield-alert" class="w-5 h-5"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-900 group-hover:text-brand-700 transition">Tingkat Risiko & EWS</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Kriteria indikator risiko rendah, sedang, tinggi, dan matriks evaluasi.</p>
        </a>

        <a href="#faq" class="bg-white p-5 rounded-2xl border border-slate-200/80 hover:border-brand-300 shadow-xs transition group space-y-2">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition">
                <i data-lucide="message-square" class="w-5 h-5"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-900 group-hover:text-brand-700 transition">Tanya Jawab (FAQ)</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Solusi kendala teknis impor Excel, format file, dan cetak surat.</p>
        </a>

    </div>

    <!-- Section 1: Algoritma Decision Tree C4.5 -->
    <div id="algoritma" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
        <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                <i data-lucide="calculator" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider">1. Landasan Matematis Algoritma C4.5</h4>
                <p class="text-xs text-slate-500">Tahapan perhitungan Entropy, Information Gain, Split Info, dan Gain Ratio</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs leading-relaxed text-slate-700">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                <h5 class="font-bold text-slate-900 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-amber-500 mr-2"></span>
                    1. Perhitungan Entropy (Tingkat Ketidakpastian)
                </h5>
                <p>Entropy mengukur derajat keacakan dalam kumpulan data sampel $S$:</p>
                <div class="p-2.5 bg-white rounded-xl border border-slate-200 font-mono text-[11px] text-slate-800 text-center font-bold">
                    Entropy(S) = - ∑ (p_i * log2(p_i))
                </div>
                <p class="text-[11px] text-slate-500">di mana $p_i$ adalah proporsi sampel kelas $i$ terhadap total data.</p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                <h5 class="font-bold text-slate-900 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-brand-500 mr-2"></span>
                    2. Perhitungan Information Gain
                </h5>
                <p>Mengukur efektivitas suatu atribut $A$ dalam mengelompokkan data:</p>
                <div class="p-2.5 bg-white rounded-xl border border-slate-200 font-mono text-[11px] text-slate-800 text-center font-bold">
                    Gain(S, A) = Entropy(S) - ∑ (|S_v| / |S| * Entropy(S_v))
                </div>
                <p class="text-[11px] text-slate-500">di mana $S_v$ adalah subset data dengan nilai atribut $A = v$.</p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                <h5 class="font-bold text-slate-900 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                    3. Perhitungan Split Information
                </h5>
                <p>Mencegah bias terhadap atribut yang memiliki banyak variasi nilai:</p>
                <div class="p-2.5 bg-white rounded-xl border border-slate-200 font-mono text-[11px] text-slate-800 text-center font-bold">
                    SplitInfo(S, A) = - ∑ (|S_v| / |S| * log2(|S_v| / |S|))
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                <h5 class="font-bold text-slate-900 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-purple-500 mr-2"></span>
                    4. Perhitungan Gain Ratio (Kriteria Pemilihan Node)
                </h5>
                <p>Atribut dengan nilai <strong>Gain Ratio tertinggi</strong> terpilih sebagai simpul cabang:</p>
                <div class="p-2.5 bg-white rounded-xl border border-slate-200 font-mono text-[11px] text-slate-800 text-center font-bold text-brand-700">
                    GainRatio(S, A) = Gain(S, A) / SplitInfo(S, A)
                </div>
            </div>
        </div>

        <!-- 6 Prediktor Variables Table -->
        <div class="space-y-3 pt-2">
            <h5 class="text-xs font-bold text-slate-900 uppercase tracking-wider">6 Variabel Prediktor Akademik yang Digunakan:</h5>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                    <span class="font-bold text-slate-900 block">1. Indeks Prestasi Kumulatif (IPK)</span>
                    <p class="text-[11px] text-slate-500">• Tinggi: &ge; 3.25<br>• Sedang: 2.75 - 3.24<br>• Rendah: &lt; 2.75</p>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                    <span class="font-bold text-slate-900 block">2. Indeks Prestasi Semester (IPS)</span>
                    <p class="text-[11px] text-slate-500">• Tinggi: &ge; 3.25<br>• Sedang: 2.75 - 3.24<br>• Rendah: &lt; 2.75</p>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                    <span class="font-bold text-slate-900 block">3. SKS Semester Diambil</span>
                    <p class="text-[11px] text-slate-500">• Beban Penuh: &ge; 20 SKS<br>• Beban Standar: 15 - 19 SKS<br>• Beban Rendah: &lt; 15 SKS</p>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                    <span class="font-bold text-slate-900 block">4. Persentase Kehadiran (%)</span>
                    <p class="text-[11px] text-slate-500">• Baik: &ge; 85%<br>• Cukup: 75% - 84.9%<br>• Kurang: &lt; 75% (Batas Ujian)</p>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                    <span class="font-bold text-slate-900 block">5. Status Cuti Akademik</span>
                    <p class="text-[11px] text-slate-500">• Ya: Mahasiswa sedang cuti<br>• Tidak: Aktif mengikuti perkuliahan</p>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                    <span class="font-bold text-slate-900 block">6. SKS Tidak Lulus (Nilai D/E)</span>
                    <p class="text-[11px] text-slate-500">• 0 SKS: Seluruh MK Lulus<br>• &gt; 0 SKS: Terdapat MK Mengulang</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Panduan Berdasarkan Peran Pengguna -->
    <div id="panduan-peran" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
        <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
            <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center">
                <i data-lucide="user-check" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider">2. Panduan Operasional Berdasarkan Peran</h4>
                <p class="text-xs text-slate-500">Tugas dan fitur yang tersedia untuk masing-masing hak akses pengguna</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
            <!-- Admin -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5">
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">Peran: Administrator</span>
                </div>
                <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
                    <li><strong>Kelola Mahasiswa:</strong> Tambah, ubah, dan hapus data master identitas mahasiswa.</li>
                    <li><strong>Kelola Data Akademik:</strong> Entri nilai per semester atau unggah berkas Excel sekaligus.</li>
                    <li><strong>Training Model C4.5:</strong> Latih model baru dengan rasio data splitting ($80:20, 70:30, 90:10$) dan jadikan sebagai model aktif.</li>
                    <li><strong>Prediksi Massal:</strong> Unggah spreadsheet seluruh mahasiswa angkatan untuk diklasifikasikan secara otomatis.</li>
                </ul>
            </div>

            <!-- Kaprodi & Dosen PA -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5">
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Peran: Kaprodi & Dosen PA</span>
                </div>
                <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
                    <li><strong>Monitoring EWS:</strong> Pantau daftar mahasiswa yang terindikasi IPK rendah (&lt;2.75) atau absensi (&lt;75%).</li>
                    <li><strong>Simulasi Prediksi Tunggal:</strong> Jalankan simulasi klasifikasi dengan fitur Auto-Fill mahasiswa saat sesi bimbingan.</li>
                    <li><strong>Pohon Keputusan & Rules:</strong> Pelajari aturan logika klasifikasi IF-THEN C4.5.</li>
                    <li><strong>Cetak Laporan:</strong> Buat laporan rekapitulasi bertanda tangan resmi berstandar kop surat akademik.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Section 3: Interpretasi Tingkat Risiko & EWS -->
    <div id="kriteria-risiko" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
        <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
            <div class="w-9 h-9 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider">3. Interpretasi Kategori Risiko & Tindak Lanjut</h4>
                <p class="text-xs text-slate-500">Tindakan intervensi akademik yang disarankan untuk tiap status klasifikasi</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <!-- Risiko Rendah -->
            <div class="p-4 rounded-2xl bg-brand-50/60 border border-brand-200/80 space-y-2">
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-600 text-white">🟢 Risiko Rendah (Aman)</span>
                <p class="text-slate-700">Mahasiswa berada di jalur yang tepat untuk lulus tepat waktu pada semester 8.</p>
                <p class="text-[11px] text-brand-800 font-semibold">• Tindakan: Rekomendasikan mulai merancang topik proposal skripsi dan magang MBKM.</p>
            </div>

            <!-- Risiko Sedang -->
            <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/80 space-y-2">
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500 text-white">🟡 Risiko Sedang (Waspada)</span>
                <p class="text-slate-700">Terdapat beberapa indikator yang berpotensi menghambat kelancaran studi (misal: kehadiran pas-pasan atau SKS mengulang).</p>
                <p class="text-[11px] text-amber-800 font-semibold">• Tindakan: Jadwalkan sesi konsultasi dengan Dosen Pembimbing Akademik.</p>
            </div>

            <!-- Risiko Tinggi -->
            <div class="p-4 rounded-2xl bg-rose-50/60 border border-rose-200/80 space-y-2">
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-600 text-white">🔴 Risiko Tinggi (Kritis)</span>
                <p class="text-slate-700">Mahasiswa memiliki probabilitas tinggi mengalami keterlambatan studi signifikan atau sanksi Drop Out (DO).</p>
                <p class="text-[11px] text-rose-800 font-semibold">• Tindakan: Intervensi khusus oleh Kaprodi & Dosen PA untuk restrukturisasi KRS dan perbaikan nilai.</p>
            </div>
        </div>
    </div>

    <!-- Section 4: Tanya Jawab (FAQ) -->
    <div id="faq" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center">
                <i data-lucide="help-circle" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider">4. Pertanyaan yang Sering Diajukan (FAQ)</h4>
                <p class="text-xs text-slate-500">Solusi cepat untuk pertanyaan umum seputar pengoperasian aplikasi</p>
            </div>
        </div>

        <div class="space-y-3 text-xs">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5">
                <h5 class="font-bold text-slate-900">Q: Bagaimana cara mengunggah data nilai mahasiswa dari file Excel?</h5>
                <p class="text-slate-600 leading-relaxed">
                    A: Masuk ke menu <strong>Data Akademik</strong>, klik tombol <strong>Unduh Template</strong> untuk memastikan nama kolom sesuai. Setelah data diisi, klik tombol <strong>Impor Excel</strong> dan pilih file spreadsheet Anda. Sistem akan memproses dan membuat data mahasiswa baru secara otomatis jika belum terdaftar.
                </p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5">
                <h5 class="font-bold text-slate-900">Q: Bagaimana jika akurasi model C4.5 yang baru dilatih lebih rendah dari model sebelumnya?</h5>
                <p class="text-slate-600 leading-relaxed">
                    A: Anda dapat membuka menu <strong>Model C4.5</strong>, melihat riwayat seluruh model yang pernah dilatih, dan mengklik tombol <strong>"Aktifkan"</strong> pada model dengan akurasi dan F1-Score terbaik untuk dijadikan acuan mesin prediksi sistem.
                </p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5">
                <h5 class="font-bold text-slate-900">Q: Apakah hasil prediksi dan laporan dapat dicetak dalam format PDF resmi?</h5>
                <p class="text-slate-600 leading-relaxed">
                    A: Ya. Pada halaman <strong>Surat Rekomendasi Prediksi</strong> dan menu <strong>Laporan & Rekapitulasi</strong>, tersedia tombol <strong>"Cetak Laporan Resmi (PDF)"</strong> dengan format kop surat standar UIN Raden Intan Lampung dan kolom tanda tangan Kaprodi/PA.
                </p>
            </div>
        </div>
    </div>

    <!-- Section 5: Informasi Skripsi & Kontak Peneliti -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-brand-200/80 shadow-xs flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="graduation-cap" class="w-6 h-6"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900">Penelitian Skripsi Mahasiswa</h4>
                <p class="text-xs text-slate-600 mt-0.5">
                    <strong>Pingky Hera Veliyanti</strong> (NIM: 2271020052)<br>
                    Program Studi Sistem Informasi, Fakultas Sains dan Teknologi, Universitas Islam Negeri Raden Intan Lampung
                </p>
            </div>
        </div>
        <div class="text-right text-xs text-slate-500 font-mono">
            <span>Tahun Akademik 2025/2026</span>
        </div>
    </div>

</div>
@endsection
