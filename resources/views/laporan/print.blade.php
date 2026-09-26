<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan_Rekapitulasi_Risiko_Akademik_UIN_RIL_{{ date('Ymd_His') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Times+New+Roman&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 12mm 15mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #0f172a;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Screen Sheet Preview (A4 Dimensions) */
        .sheet {
            background: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            margin: 0 auto 2.5rem auto;
            padding: 16mm 18mm;
            width: 100%;
            max-width: 210mm;
            min-height: 297mm;
            box-sizing: border-box;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sheet-body {
            flex: 1 0 auto;
        }

        .sheet-footer {
            flex-shrink: 0;
            margin-top: 1.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid #94a3b8;
        }

        /* Table Styling */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #334155;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .sheet {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: none !important;
                min-height: 270mm !important;
                page-break-after: always !important;
                break-after: page !important;
            }
            .sheet:last-of-type {
                page-break-after: auto !important;
                break-after: auto !important;
            }
            .page-break-avoid {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            tr {
                page-break-inside: avoid !important;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
        }
    </style>
</head>
<body class="py-6 px-4 sm:px-6">

    <!-- Sticky Print Action Toolbar (Hidden during Print) -->
    <div class="max-w-[210mm] mx-auto mb-6 flex flex-col sm:flex-row items-center justify-between gap-4 no-print bg-slate-900 text-white p-4 rounded-xl shadow-xl border border-slate-800">
        <div class="flex items-center space-x-3">
            <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
            <div>
                <p class="text-xs font-bold font-sans">Mode Pratinjau Cetak Laporan Resmi (A4)</p>
                <p class="text-[11px] text-slate-400 font-sans">
                    Total: <strong class="text-emerald-400">{{ $totalRecords }} Mahasiswa</strong> • Terbagi dalam <strong class="text-white">{{ $totalPages }} Halaman</strong>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 font-sans">
            <!-- Density Selector Dropdown -->
            <form method="GET" action="{{ url()->current() }}" class="inline-flex items-center space-x-1.5 mr-2">
                @foreach(request()->except(['limit_p1', 'limit_p2']) as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <label for="density" class="text-[11px] text-slate-400 font-medium">Kerapatan:</label>
                <select id="density" onchange="applyDensity(this.value)" class="bg-slate-800 border border-slate-700 text-white text-xs rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    <option value="15,22" {{ $page1Limit == 15 && $subsequentLimit == 22 ? 'selected' : '' }}>Standar (15 / 22)</option>
                    <option value="12,18" {{ $page1Limit == 12 && $subsequentLimit == 18 ? 'selected' : '' }}>Renggang (12 / 18)</option>
                    <option value="18,25" {{ $page1Limit == 18 && $subsequentLimit == 25 ? 'selected' : '' }}>Rapat (18 / 25)</option>
                </select>
            </form>

            <button type="button" onclick="window.close()" class="px-3.5 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-300 transition">
                Tutup
            </button>
            <button type="button" onclick="window.print()" class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-xs font-bold text-white shadow-md transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                </svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    @php $globalCounter = 0; @endphp

    <!-- Multi-Page Sheets Container -->
    @foreach($pages as $pageIdx => $pageRecords)
        @php 
            $pageNum = $pageIdx + 1;
            $isFirstPage = ($pageNum === 1);
            $isLastPage = ($pageNum === $totalPages);
        @endphp

        <!-- Visual Page Indicator for Screen Preview -->
        <div class="no-print max-w-[210mm] mx-auto text-center text-xs font-bold text-slate-500 mb-2 font-sans flex items-center justify-center gap-2">
            <span class="h-px bg-slate-300 w-16"></span>
            <span>Halaman {{ $pageNum }} dari {{ $totalPages }}</span>
            <span class="h-px bg-slate-300 w-16"></span>
        </div>

        <!-- A4 Page Sheet -->
        <div class="sheet" id="sheet-page-{{ $pageNum }}">
            
            <div class="sheet-body">
                
                @if($isFirstPage)
                    <!-- Kop Surat Resmi UIN Raden Intan Lampung (Page 1) -->
                    <div class="border-b-4 border-double border-slate-900 pb-3 text-center space-y-0.5">
                        <h4 class="text-xs sm:text-sm font-bold tracking-wider uppercase leading-tight">KEMENTERIAN AGAMA REPUBLIK INDONESIA</h4>
                        <h2 class="text-base sm:text-lg font-black tracking-wide uppercase leading-tight">UNIVERSITAS ISLAM NEGERI RADEN INTAN LAMPUNG</h2>
                        <h3 class="text-sm sm:text-base font-bold uppercase leading-tight">FAKULTAS SAINS DAN TEKNOLOGI</h3>
                        <h3 class="text-xs sm:text-sm font-bold uppercase text-emerald-800 leading-tight">PROGRAM STUDI SISTEM INFORMASI</h3>
                        <p class="text-[10px] sm:text-[11px] text-slate-600 leading-normal pt-1">
                            Alamat: Jl. Letkol H. Endro Suratmin Sukarame Bandar Lampung 35131 • Telp: (0721) 703260 • Website: www.radenintan.ac.id
                        </p>
                    </div>

                    <!-- Document Title & Meta -->
                    <div class="text-center my-4 space-y-1">
                        <h3 class="text-sm sm:text-base font-black uppercase tracking-wider underline">
                            REKAPITULASI HASIL PREDIKSI RISIKO AKADEMIK MAHASISWA
                        </h3>
                        <p class="text-xs text-slate-700">
                            Metode Klasifikasi: <strong>Decision Tree C4.5</strong> • Model: <strong>{{ $activeModel->nama_model ?? 'Model Baseline C4.5' }}</strong>
                        </p>
                        <p class="text-[11px] text-slate-500 font-sans">
                            Dicetak pada tanggal: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB
                        </p>
                    </div>

                    <!-- Summary Statistics Table (Page 1 Only) -->
                    <div class="mb-4">
                        <table class="w-full text-xs text-center border border-slate-800 font-sans">
                            <thead class="bg-slate-100 font-bold border-b border-slate-800">
                                <tr>
                                    <th class="p-1.5 border-r border-slate-800">Total Mahasiswa Terdaftar</th>
                                    <th class="p-1.5 border-r border-slate-800 text-emerald-700">Risiko Rendah (Aman)</th>
                                    <th class="p-1.5 border-r border-slate-800 text-amber-700">Risiko Sedang (Waspada)</th>
                                    <th class="p-1.5 text-rose-700">Risiko Tinggi (Perhatian)</th>
                                </tr>
                            </thead>
                            <tbody class="font-bold text-xs sm:text-sm">
                                <tr>
                                    <td class="p-1.5 border-r border-slate-800 font-mono">{{ $totalRecords }} Data</td>
                                    <td class="p-1.5 border-r border-slate-800 text-emerald-700 font-mono">
                                        {{ $totalRendah }} ({{ $totalRecords > 0 ? round(($totalRendah / $totalRecords) * 100) : 0 }}%)
                                    </td>
                                    <td class="p-1.5 border-r border-slate-800 text-amber-700 font-mono">
                                        {{ $totalSedang }} ({{ $totalRecords > 0 ? round(($totalSedang / $totalRecords) * 100) : 0 }}%)
                                    </td>
                                    <td class="p-1.5 text-rose-700 font-mono">
                                        {{ $totalTinggi }} ({{ $totalRecords > 0 ? round(($totalTinggi / $totalRecords) * 100) : 0 }}%)
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <!-- Continuation Header (Page 2..N) -->
                    <div class="border-b-2 border-slate-800 pb-2 mb-4 flex items-center justify-between text-xs">
                        <div>
                            <p class="font-bold uppercase tracking-wider text-[11px] text-slate-800 leading-tight">
                                KEMENTERIAN AGAMA RI • UNIVERSITAS ISLAM NEGERI RADEN INTAN LAMPUNG
                            </p>
                            <p class="text-[10px] text-slate-600 font-semibold leading-tight">
                                FAKULTAS SAINS DAN TEKNOLOGI • PROGRAM STUDI SISTEM INFORMASI
                            </p>
                        </div>
                        <div class="text-right font-sans">
                            <p class="text-[10px] text-slate-700 font-bold uppercase tracking-wide">
                                Lanjutan Rekapitulasi Prediksi Risiko Akademik
                            </p>
                            <p class="text-[9px] text-slate-500 font-mono">
                                Model: {{ Str::limit($activeModel->nama_model ?? 'C4.5', 30) }}
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Data Table Chunk -->
                <div class="mb-4">
                    <table class="w-full text-[10.5px] border border-slate-800 border-collapse">
                        <thead class="bg-slate-100 font-bold border-b border-slate-800 text-center font-sans">
                            <tr>
                                <th class="p-1 border border-slate-800 w-8">No</th>
                                <th class="p-1 border border-slate-800 w-24">NIM</th>
                                <th class="p-1 border border-slate-800 text-left">Nama Mahasiswa</th>
                                <th class="p-1 border border-slate-800 w-11">Angk.</th>
                                <th class="p-1 border border-slate-800 w-10">Sem</th>
                                <th class="p-1 border border-slate-800 w-10">IPS</th>
                                <th class="p-1 border border-slate-800 w-10">IPK</th>
                                <th class="p-1 border border-slate-800 w-10">SKS</th>
                                <th class="p-1 border border-slate-800 w-12">Hadir</th>
                                <th class="p-1 border border-slate-800 w-9">Cuti</th>
                                <th class="p-1 border border-slate-800 w-28">Hasil Klasifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($pageRecords as $r)
                                @php $globalCounter++; @endphp
                                <tr class="{{ $globalCounter % 2 === 0 ? 'bg-slate-50/60' : '' }}">
                                    <td class="p-1 border border-slate-800 text-center font-mono">{{ $globalCounter }}</td>
                                    <td class="p-1 border border-slate-800 font-mono text-center font-bold">{{ $r->mahasiswa->nim ?? '-' }}</td>
                                    <td class="p-1 border border-slate-800 font-medium pl-1.5">{{ $r->mahasiswa->nama ?? 'Mahasiswa Telah Dihapus' }}</td>
                                    <td class="p-1 border border-slate-800 text-center">{{ $r->mahasiswa->angkatan ?? '-' }}</td>
                                    <td class="p-1 border border-slate-800 text-center">{{ $r->semester }}</td>
                                    <td class="p-1 border border-slate-800 text-center font-mono">{{ $r->ips }}</td>
                                    <td class="p-1 border border-slate-800 text-center font-mono font-bold">{{ $r->ipk }}</td>
                                    <td class="p-1 border border-slate-800 text-center font-mono">{{ $r->sks_semester }}</td>
                                    <td class="p-1 border border-slate-800 text-center font-mono">{{ $r->persentase_kehadiran }}%</td>
                                    <td class="p-1 border border-slate-800 text-center">{{ $r->status_cuti ? 'Ya' : 'Tidak' }}</td>
                                    <td class="p-1 border border-slate-800 text-center font-bold font-sans text-[10px]
                                        @if($r->label_risiko_aktual === 'Risiko Rendah') text-emerald-800
                                        @elseif($r->label_risiko_aktual === 'Risiko Sedang') text-amber-800
                                        @else text-rose-800
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

                @if($isLastPage)
                    <!-- Signature Section (Only on the Final Page) -->
                    <div class="page-break-avoid mt-6 pt-3 grid grid-cols-2 text-center text-xs">
                        <div>
                            <p>Mengetahui,</p>
                            <p class="font-bold mt-0.5">Dosen Pembimbing Akademik (PA)</p>
                            <div class="h-16"></div>
                            <p class="font-bold underline">( Dosen Pembimbing Akademik, M.T.I. )</p>
                            <p class="text-[10px] text-slate-600 font-sans">NIP. 198503102012011002</p>
                        </div>
                        <div>
                            <p>Bandar Lampung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                            <p class="font-bold mt-0.5">Ketua Program Studi Sistem Informasi</p>
                            <div class="h-16"></div>
                            <p class="font-bold underline">Dr. Kaprodi Sistem Informasi, M.Kom.</p>
                            <p class="text-[10px] text-slate-600 font-sans">NIP. 197905152008011005</p>
                        </div>
                    </div>
                @endif

            </div>

            <!-- Official Sheet Footer -->
            <div class="sheet-footer flex items-center justify-between text-[10px] text-slate-500 font-sans">
                <span>Dokumen Rekapitulasi SIPRA-C4.5 • Fakultas Sains dan Teknologi UIN Raden Intan Lampung</span>
                <span class="font-bold">Halaman {{ $pageNum }} dari {{ $totalPages }}</span>
            </div>

        </div>
    @endforeach

    <script>
        function applyDensity(val) {
            const parts = val.split(',');
            const url = new URL(window.location.href);
            url.searchParams.set('limit_p1', parts[0]);
            url.searchParams.set('limit_p2', parts[1]);
            window.location.href = url.toString();
        }
    </script>

</body>
</html>
