<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan_Risiko_Akademik_UIN_RIL_{{ date('Ymd_His') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Times+New+Roman&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN for Print Layout -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111827;
            background-color: #f3f4f6;
        }
        @media print {
            body {
                background-color: #ffffff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-after: always;
            }
            .print-shadow-none {
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    <!-- Print Action Bar -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print bg-slate-900 text-white p-4 rounded-2xl shadow-xl">
        <div class="flex items-center space-x-3">
            <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
            <p class="text-xs font-semibold">Mode Pratinjau Cetak Laporan Resmi</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="window.close()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold transition">
                Tutup
            </button>
            <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-xs font-bold shadow transition flex items-center">
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Paper Container -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 shadow-2xl rounded-xl print-shadow-none text-slate-900 border border-slate-200">
        
        <!-- Kop Surat Resmi UIN Raden Intan Lampung -->
        <div class="border-b-4 border-double border-slate-900 pb-4 text-center space-y-1">
            <h4 class="text-sm font-bold tracking-wider uppercase">KEMENTERIAN AGAMA REPUBLIK INDONESIA</h4>
            <h2 class="text-lg font-black tracking-wide uppercase">UNIVERSITAS ISLAM NEGERI RADEN INTAN LAMPUNG</h2>
            <h3 class="text-base font-bold uppercase">FAKULTAS SAINS DAN TEKNOLOGI</h3>
            <h3 class="text-sm font-bold uppercase text-emerald-800">PROGRAM STUDI SISTEM INFORMASI</h3>
            <p class="text-[11px] text-slate-600">
                Alamat: Jl. Letkol H. Endro Suratmin Sukarame Bandar Lampung 35131 • Telp: (0721) 703260 • Website: www.radenintan.ac.id
            </p>
        </div>

        <!-- Document Title -->
        <div class="text-center my-6 space-y-1">
            <h3 class="text-base font-black uppercase tracking-wider underline">
                REKAPITULASI HASIL PREDIKSI RISIKO AKADEMIK MAHASISWA
            </h3>
            <p class="text-xs text-slate-600">
                Metode Klasifikasi: <strong>Decision Tree C4.5</strong> • Model: {{ $activeModel->nama_model ?? 'Model Baseline' }}
            </p>
            <p class="text-xs text-slate-500">
                Dicetak pada tanggal: {{ date('d F Y, H:i') }} WIB
            </p>
        </div>

        <!-- Summary Statistics Table -->
        <div class="mb-6">
            <table class="w-full text-xs border border-slate-800 text-center">
                <thead class="bg-slate-100 font-bold border-b border-slate-800">
                    <tr>
                        <th class="p-2 border-r border-slate-800">Total Mahasiswa Terdaftar</th>
                        <th class="p-2 border-r border-slate-800 text-emerald-700">Risiko Rendah (Aman)</th>
                        <th class="p-2 border-r border-slate-800 text-amber-700">Risiko Sedang (Waspada)</th>
                        <th class="p-2 text-rose-700">Risiko Tinggi (Perhatian)</th>
                    </tr>
                </thead>
                <tbody class="font-bold text-sm">
                    <tr>
                        <td class="p-2 border-r border-slate-800">{{ $totalRecords }} Data</td>
                        <td class="p-2 border-r border-slate-800 text-emerald-700">
                            {{ $totalRendah }} ({{ $totalRecords > 0 ? round(($totalRendah / $totalRecords) * 100) : 0 }}%)
                        </td>
                        <td class="p-2 border-r border-slate-800 text-amber-700">
                            {{ $totalSedang }} ({{ $totalRecords > 0 ? round(($totalSedang / $totalRecords) * 100) : 0 }}%)
                        </td>
                        <td class="p-2 text-rose-700">
                            {{ $totalTinggi }} ({{ $totalRecords > 0 ? round(($totalTinggi / $totalRecords) * 100) : 0 }}%)
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Detail Table -->
        <div class="mb-8">
            <table class="w-full text-[11px] border border-slate-800 border-collapse">
                <thead class="bg-slate-100 font-bold border-b border-slate-800 text-center">
                    <tr>
                        <th class="p-1.5 border border-slate-800 w-8">No</th>
                        <th class="p-1.5 border border-slate-800">NIM</th>
                        <th class="p-1.5 border border-slate-800 text-left">Nama Mahasiswa</th>
                        <th class="p-1.5 border border-slate-800">Angk.</th>
                        <th class="p-1.5 border border-slate-800">Sem</th>
                        <th class="p-1.5 border border-slate-800">IPS</th>
                        <th class="p-1.5 border border-slate-800">IPK</th>
                        <th class="p-1.5 border border-slate-800">SKS</th>
                        <th class="p-1.5 border border-slate-800">Kehadiran</th>
                        <th class="p-1.5 border border-slate-800">Cuti</th>
                        <th class="p-1.5 border border-slate-800">Hasil Klasifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($records as $idx => $r)
                        <tr class="{{ $idx % 2 === 1 ? 'bg-slate-50' : '' }}">
                            <td class="p-1.5 border border-slate-800 text-center">{{ $idx + 1 }}</td>
                            <td class="p-1.5 border border-slate-800 font-mono text-center font-bold">{{ $r->mahasiswa->nim }}</td>
                            <td class="p-1.5 border border-slate-800 font-medium">{{ $r->mahasiswa->nama }}</td>
                            <td class="p-1.5 border border-slate-800 text-center">{{ $r->mahasiswa->angkatan }}</td>
                            <td class="p-1.5 border border-slate-800 text-center">{{ $r->semester }}</td>
                            <td class="p-1.5 border border-slate-800 text-center">{{ $r->ips }}</td>
                            <td class="p-1.5 border border-slate-800 text-center font-bold">{{ $r->ipk }}</td>
                            <td class="p-1.5 border border-slate-800 text-center">{{ $r->sks_semester }}</td>
                            <td class="p-1.5 border border-slate-800 text-center">{{ $r->persentase_kehadiran }}%</td>
                            <td class="p-1.5 border border-slate-800 text-center">{{ $r->status_cuti ? 'Ya' : 'Tidak' }}</td>
                            <td class="p-1.5 border border-slate-800 text-center font-bold
                                @if($r->label_risiko_aktual === 'Risiko Rendah') text-emerald-700
                                @elseif($r->label_risiko_aktual === 'Risiko Sedang') text-amber-700
                                @else text-rose-700
                                @endif">
                                {{ $r->label_risiko_aktual }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="p-4 border border-slate-800 text-center text-slate-500">Tidak ada rekam data untuk laporan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Signature Section -->
        <div class="grid grid-cols-2 text-center text-xs pt-6">
            <div>
                <p>Mengetahui,</p>
                <p class="font-bold mt-0.5">Dosen Pembimbing Akademik (PA)</p>
                <div class="h-20"></div>
                <p class="font-bold underline">( Dosen Pembimbing Akademik, M.T.I. )</p>
                <p class="text-[11px] text-slate-600">NIP. 198503102012011002</p>
            </div>
            <div>
                <p>Bandar Lampung, {{ date('d F Y') }}</p>
                <p class="font-bold mt-0.5">Ketua Program Studi Sistem Informasi</p>
                <div class="h-20"></div>
                <p class="font-bold underline">Dr. Kaprodi Sistem Informasi, M.Kom.</p>
                <p class="text-[11px] text-slate-600">NIP. 197905152008011005</p>
            </div>
        </div>

    </div>

</body>
</html>
