<?php

namespace App\Http\Controllers;

use App\Models\C45Model;
use App\Models\C45Rule;
use App\Models\Mahasiswa;
use App\Models\Prediksi;
use App\Models\PrediksiBatch;
use App\Services\C45EngineService;
use App\Services\DataPreprocessingService;
use App\Services\SpreadsheetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PrediksiController extends Controller
{
    /**
     * Display list of prediction history
     */
    public function index(Request $request)
    {
        $query = Prediksi::with(['mahasiswa', 'model', 'batch']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_mahasiswa', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        if ($risiko = $request->input('hasil_klasifikasi')) {
            $query->where('hasil_klasifikasi', $risiko);
        }

        if ($batchId = $request->input('batch_id')) {
            $query->where('batch_id', $batchId);
        }

        $prediksis = $query->latest()->paginate(10)->withQueryString();
        $batches = PrediksiBatch::latest()->take(10)->get();

        // Statistics
        $totalPrediksi = Prediksi::count();
        $totalRendah = Prediksi::where('hasil_klasifikasi', 'Risiko Rendah')->count();
        $totalSedang = Prediksi::where('hasil_klasifikasi', 'Risiko Sedang')->count();
        $totalTinggi = Prediksi::where('hasil_klasifikasi', 'Risiko Tinggi')->count();
        $activeModel = C45Model::active()->first();

        return view('prediksi.index', compact(
            'prediksis',
            'batches',
            'totalPrediksi',
            'totalRendah',
            'totalSedang',
            'totalTinggi',
            'activeModel'
        ));
    }

    /**
     * Show single prediction form
     */
    public function createSingle(Request $request)
    {
        $activeModel = C45Model::active()->first();
        if (!$activeModel) {
            $activeModel = C45Model::latest()->first();
        }

        $mahasiswas = Mahasiswa::with('latestAkademik')->orderBy('nim', 'asc')->get();
        $selectedMahasiswaId = $request->input('mahasiswa_id');
        $selectedMahasiswa = $selectedMahasiswaId ? Mahasiswa::with('latestAkademik')->find($selectedMahasiswaId) : null;

        return view('prediksi.single', compact('activeModel', 'mahasiswas', 'selectedMahasiswa'));
    }

    /**
     * Process single prediction request
     */
    public function storeSingle(Request $request)
    {
        $activeModel = C45Model::active()->first();
        if (!$activeModel) {
            $activeModel = C45Model::latest()->first();
            if (!$activeModel) {
                return redirect()->route('admin.c45.create')
                    ->with('error', 'Belum ada model C4.5 yang terlatih. Silakan latih model terlebih dahulu.');
            }
        }

        $validated = $request->validate([
            'mahasiswa_id' => ['nullable', 'exists:mahasiswas,id'],
            'nim' => ['nullable', 'string', 'max:30'],
            'nama_mahasiswa' => ['required', 'string', 'max:150'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'ipk' => ['required', 'numeric', 'min:0', 'max:4.00'],
            'ips' => ['required', 'numeric', 'min:0', 'max:4.00'],
            'sks_semester' => ['required', 'integer', 'min:0', 'max:30'],
            'sks_tidak_lulus' => ['required', 'integer', 'min:0', 'max:60'],
            'persentase_kehadiran' => ['required', 'numeric', 'min:0', 'max:100.00'],
            'status_cuti' => ['nullable', 'boolean'],
        ]);

        $ipk = (float) $validated['ipk'];
        $ips = (float) $validated['ips'];
        $sksSemester = (int) $validated['sks_semester'];
        $sksTidakLulus = (int) $validated['sks_tidak_lulus'];
        $kehadiran = (float) $validated['persentase_kehadiran'];
        $isCuti = $request->boolean('status_cuti');

        // Preprocess categories
        $inputCategories = [
            'kategori_ipk' => DataPreprocessingService::categorizeIpk($ipk),
            'kategori_ips' => DataPreprocessingService::categorizeIps($ips),
            'kategori_sks' => DataPreprocessingService::categorizeSks($sksSemester),
            'kategori_kehadiran' => DataPreprocessingService::categorizeKehadiran($kehadiran),
            'status_cuti' => $isCuti ? 'Ya' : 'Tidak',
            'sks_tidak_lulus' => $sksTidakLulus > 0 ? 'Ada' : 'Tidak Ada',
        ];

        // Predict with Active C4.5 Tree
        $treeArray = json_decode($activeModel->tree_structure_json, true) ?? [];
        $predictionResult = C45EngineService::predictWithTree($treeArray, $inputCategories);

        $decision = $predictionResult['decision'] ?? 'Risiko Sedang';
        $confidence = $predictionResult['confidence'] ?? 80.0;

        // Match Rule if any
        $matchedRule = $this->findMatchingRule($activeModel->id, $inputCategories);

        // Generate Academic Recommendation
        $rekomendasi = $this->generateRecommendation($decision, $ipk, $kehadiran, $sksTidakLulus, $isCuti);
        $statusDo = ($decision === 'Risiko Tinggi' || $isCuti || $ipk < 2.25) ? 'Berisiko DO' : 'Tidak Berisiko DO';

        $prediksi = Prediksi::create([
            'mahasiswa_id' => $validated['mahasiswa_id'] ?? null,
            'model_id' => $activeModel->id,
            'rule_id' => $matchedRule?->id,
            'nim' => $validated['nim'] ?? null,
            'nama_mahasiswa' => $validated['nama_mahasiswa'],
            'semester' => $validated['semester'],
            'input_params_json' => array_merge($validated, $inputCategories),
            'hasil_klasifikasi' => $decision,
            'status_do' => $statusDo,
            'confidence_score' => $confidence,
            'rekomendasi_akademik' => $rekomendasi,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.prediksi.show', $prediksi)
            ->with('success', "Prediksi berhasil dihitung menggunakan Model '{$activeModel->nama_model}'!");
    }

    /**
     * Show single prediction detailed report / certificate
     */
    public function show(Prediksi $prediksi)
    {
        $prediksi->load(['mahasiswa.dosenPa', 'model', 'rule', 'creator']);

        $treeArray = [];
        if ($prediksi->model && !empty($prediksi->model->tree_structure_json)) {
            $treeArray = is_array($prediksi->model->tree_structure_json)
                ? $prediksi->model->tree_structure_json
                : (json_decode($prediksi->model->tree_structure_json, true) ?? []);
        }

        $inputParams = $prediksi->input_params_json ?? [];
        $decisionTrace = C45EngineService::traceDecisionPath($treeArray, $inputParams);

        return view('prediksi.show', compact('prediksi', 'decisionTrace'));
    }

    /**
     * Download Excel batch prediction import template
     */
    public function downloadBatchTemplate(): StreamedResponse
    {
        return SpreadsheetService::downloadBatchPredictionTemplate();
    }

    /**
     * Detect column indices from spreadsheet header row dynamically
     */
    public static function detectColumnIndices(array $headerRow): array
    {
        $normalizedHeaders = [];
        foreach ($headerRow as $idx => $val) {
            $clean = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string) $val)));
            $normalizedHeaders[$idx] = $clean;
        }

        $aliasMap = [
            'nim' => ['nim', 'nomorinduk', 'nomorindukmahasiswa', 'studentnumber', 'noid'],
            'nama' => ['nama', 'namamahasiswa', 'namalengkap', 'studentname', 'name'],
            'semester' => ['semester', 'smt', 'sem'],
            'ips' => ['ips', 'ipsemester', 'indeksprestasisemester', 'gpa'],
            'ipk' => ['ipk', 'ipkumulatif', 'indeksprestasikumulatif', 'cgpa'],
            'sks_semester' => ['skssemester', 'sks', 'sksdiambil', 'credits'],
            'sks_tidak_lulus' => ['skstidaklulus', 'sksgagal', 'skstl', 'failedcredits', 'tidaklulus'],
            'kehadiran' => ['kehadiran', 'persentasekehadiran', 'presensi', 'kehadiranpersen', 'attendance'],
            'status_cuti' => ['statuscuti', 'cuti', 'iscuti', 'leave'],
        ];

        $detected = [];
        foreach ($aliasMap as $field => $aliases) {
            foreach ($normalizedHeaders as $idx => $clean) {
                if (in_array($clean, $aliases, true)) {
                    $detected[$field] = $idx;
                    break;
                }
            }
        }

        // If at least 2 essential academic columns (ipk and kehadiran) are detected by header
        if (isset($detected['ipk']) && isset($detected['kehadiran'])) {
            return $detected;
        }

        // Otherwise fallback based on total columns:
        // 9-column standard batch template
        if (count($headerRow) <= 10) {
            return [
                'nim' => 0,
                'nama' => 1,
                'semester' => 2,
                'ips' => 3,
                'ipk' => 4,
                'sks_semester' => 5,
                'sks_tidak_lulus' => 6,
                'kehadiran' => 7,
                'status_cuti' => 8,
            ];
        }

        // Legacy 17-column academic dataset format
        return [
            'nim' => 0,
            'nama' => 1,
            'semester' => 8,
            'ips' => 10,
            'ipk' => 11,
            'sks_semester' => 12,
            'sks_tidak_lulus' => 14,
            'kehadiran' => 15,
            'status_cuti' => 16,
        ];
    }

    /**
     * Show batch prediction upload form
     */
    public function createBatch()
    {
        $activeModel = C45Model::active()->first();
        return view('prediksi.batch', compact('activeModel'));
    }

    /**
     * Process batch prediction from uploaded Excel file with flexible mapping and row validation
     */
    public function storeBatch(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        $activeModel = C45Model::active()->first() ?? C45Model::latest()->first();
        if (!$activeModel) {
            return back()->with('error', 'Belum ada model C4.5 yang tersedia untuk melakukan prediksi.');
        }

        $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (count($rows) <= 1) {
            return back()->with('error', 'Berkas spreadsheet kosong atau tidak memiliki baris data.');
        }

        $headerRow = array_shift($rows); // Extract header
        $colMap = self::detectColumnIndices($headerRow);

        $treeArray = json_decode($activeModel->tree_structure_json, true) ?? [];
        $batchCode = 'BATCH-' . date('Ymd-His') . '-' . strtoupper(substr(uniqid(), -4));

        DB::beginTransaction();

        try {
            $totalRecords = 0;
            $totalRendah = 0;
            $totalSedang = 0;
            $totalTinggi = 0;
            $errorLogs = [];

            $batch = PrediksiBatch::create([
                'batch_code' => $batchCode,
                'file_name' => $request->file('file')->getClientOriginalName(),
                'model_id' => $activeModel->id,
                'total_records' => 0,
                'total_rendah' => 0,
                'total_sedang' => 0,
                'total_tinggi' => 0,
                'skipped_records' => 0,
                'error_logs' => null,
                'created_by' => Auth::id(),
            ]);

            foreach ($rows as $rowIndex => $row) {
                $excelRowNum = $rowIndex + 2;

                // Skip entirely empty row
                $nonEmptyCells = array_filter($row, fn($cell) => $cell !== null && trim((string)$cell) !== '');
                if (empty($nonEmptyCells)) {
                    continue;
                }

                $nim = trim((string) ($row[$colMap['nim'] ?? 0] ?? ''));
                $nama = trim((string) ($row[$colMap['nama'] ?? 1] ?? ''));

                if (empty($nama) && empty($nim)) {
                    $errorLogs[] = [
                        'row' => $excelRowNum,
                        'nim' => '-',
                        'nama' => '-',
                        'reason' => 'NIM dan Nama Mahasiswa kosong.',
                    ];
                    continue;
                }

                $ipkRaw = trim((string) ($row[$colMap['ipk'] ?? 4] ?? ''));
                $cleanIpkStr = str_replace(',', '.', $ipkRaw);
                if ($cleanIpkStr === '' || !is_numeric($cleanIpkStr) || (float)$cleanIpkStr < 0.0 || (float)$cleanIpkStr > 4.00) {
                    $errorLogs[] = [
                        'row' => $excelRowNum,
                        'nim' => $nim ?: '-',
                        'nama' => $nama ?: '-',
                        'reason' => "Nilai IPK '{$ipkRaw}' tidak valid (wajib berupa angka antara 0.00 - 4.00).",
                    ];
                    continue;
                }
                $ipk = (float) $cleanIpkStr;

                $kehadiranRaw = trim((string) ($row[$colMap['kehadiran'] ?? 7] ?? ''));
                $cleanKehadiranStr = str_replace(',', '.', $kehadiranRaw);
                if ($cleanKehadiranStr === '' || !is_numeric($cleanKehadiranStr) || (float)$cleanKehadiranStr < 0.0 || (float)$cleanKehadiranStr > 100.0) {
                    $errorLogs[] = [
                        'row' => $excelRowNum,
                        'nim' => $nim ?: '-',
                        'nama' => $nama ?: '-',
                        'reason' => "Persentase kehadiran '{$kehadiranRaw}' tidak valid (wajib berupa angka 0 - 100%).",
                    ];
                    continue;
                }
                $kehadiran = (float) $cleanKehadiranStr;

                $ipsRaw = trim((string) ($row[$colMap['ips'] ?? 3] ?? ''));
                $cleanIpsStr = str_replace(',', '.', $ipsRaw);
                $ips = is_numeric($cleanIpsStr) ? max(0.0, min(4.00, (float)$cleanIpsStr)) : $ipk;

                $semesterRaw = (int) ($row[$colMap['semester'] ?? 2] ?? 1);
                $semester = ($semesterRaw >= 1 && $semesterRaw <= 14) ? $semesterRaw : 1;

                $sksSemester = max(0, min(30, (int) ($row[$colMap['sks_semester'] ?? 5] ?? 20)));
                $sksTidakLulus = max(0, (int) ($row[$colMap['sks_tidak_lulus'] ?? 6] ?? 0));

                $cutiStr = strtolower(trim((string) ($row[$colMap['status_cuti'] ?? 8] ?? 'tidak')));
                $isCuti = in_array($cutiStr, ['ya', '1', 'true', 'cuti', 'y']);

                $inputCategories = [
                    'kategori_ipk' => DataPreprocessingService::categorizeIpk($ipk),
                    'kategori_ips' => DataPreprocessingService::categorizeIps($ips),
                    'kategori_sks' => DataPreprocessingService::categorizeSks($sksSemester),
                    'kategori_kehadiran' => DataPreprocessingService::categorizeKehadiran($kehadiran),
                    'status_cuti' => $isCuti ? 'Ya' : 'Tidak',
                    'sks_tidak_lulus' => $sksTidakLulus > 0 ? 'Ada' : 'Tidak Ada',
                ];

                $predResult = C45EngineService::predictWithTree($treeArray, $inputCategories);
                $decision = $predResult['decision'] ?? 'Risiko Sedang';
                $confidence = $predResult['confidence'] ?? 85.0;

                if ($decision === 'Risiko Rendah') $totalRendah++;
                elseif ($decision === 'Risiko Sedang') $totalSedang++;
                elseif ($decision === 'Risiko Tinggi') $totalTinggi++;

                $matchedRule = $this->findMatchingRule($activeModel->id, $inputCategories);
                $rekomendasi = $this->generateRecommendation($decision, $ipk, $kehadiran, $sksTidakLulus, $isCuti);
                $statusDo = ($decision === 'Risiko Tinggi' || $isCuti || $ipk < 2.25) ? 'Berisiko DO' : 'Tidak Berisiko DO';

                // Look up student if NIM matches
                $mhs = Mahasiswa::where('nim', $nim)->first();

                Prediksi::create([
                    'mahasiswa_id' => $mhs?->id,
                    'model_id' => $activeModel->id,
                    'rule_id' => $matchedRule?->id,
                    'batch_id' => $batch->id,
                    'nim' => $nim ?: null,
                    'nama_mahasiswa' => $nama ?: ($mhs?->nama ?? 'Mahasiswa'),
                    'semester' => $semester,
                    'input_params_json' => array_merge([
                        'nim' => $nim,
                        'nama_mahasiswa' => $nama,
                        'semester' => $semester,
                        'ipk' => $ipk,
                        'ips' => $ips,
                        'sks_semester' => $sksSemester,
                        'sks_tidak_lulus' => $sksTidakLulus,
                        'persentase_kehadiran' => $kehadiran,
                        'status_cuti' => $isCuti,
                    ], $inputCategories),
                    'hasil_klasifikasi' => $decision,
                    'status_do' => $statusDo,
                    'confidence_score' => $confidence,
                    'rekomendasi_akademik' => $rekomendasi,
                    'created_by' => Auth::id(),
                ]);

                $totalRecords++;
            }

            if ($totalRecords === 0) {
                DB::rollBack();
                $errDetails = !empty($errorLogs)
                    ? ': ' . implode(' | ', array_map(fn($e) => "Baris {$e['row']} ({$e['reason']})", array_slice($errorLogs, 0, 3)))
                    : '.';
                return back()->withInput()->with('error', 'Gagal memproses batch prediksi: Tidak ada baris data mahasiswa yang valid ditemukan' . $errDetails);
            }

            $skippedCount = count($errorLogs);

            $batch->update([
                'total_records' => $totalRecords,
                'total_rendah' => $totalRendah,
                'total_sedang' => $totalSedang,
                'total_tinggi' => $totalTinggi,
                'skipped_records' => $skippedCount,
                'error_logs' => !empty($errorLogs) ? $errorLogs : null,
            ]);

            DB::commit();

            $redirectRoute = auth()->user()->isAdmin()
                ? route('admin.prediksi.batch.show', $batch)
                : route('prodi.prediksi.batch.show', $batch);

            $flashMsg = "Prediksi massal selesai! Total {$totalRecords} mahasiswa berhasil diklasifikasikan.";
            if ($skippedCount > 0) {
                $flashMsg .= " Catatan: {$skippedCount} baris data dilewati karena format data tidak valid.";
            }

            return redirect($redirectRoute)->with('success', $flashMsg);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses batch prediksi: ' . $e->getMessage());
        }
    }

    /**
     * Show batch prediction detailed results
     */
    public function showBatch(PrediksiBatch $batch)
    {
        $batch->load(['model', 'creator', 'prediksis.mahasiswa']);
        $prediksis = $batch->prediksis()->paginate(15);

        return view('prediksi.batch_show', compact('batch', 'prediksis'));
    }

    /**
     * Export batch prediction results to Excel
     */
    public static function exportBatch(PrediksiBatch $batch): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Hasil Prediksi ' . substr($batch->batch_code, 0, 15));

        $headers = [
            'A1' => 'No',
            'B1' => 'NIM',
            'C1' => 'Nama Mahasiswa',
            'D1' => 'Semester',
            'E1' => 'IPK',
            'F1' => 'Kehadiran (%)',
            'G1' => 'Status Cuti',
            'H1' => 'Hasil Prediksi Risiko',
            'I1' => 'Potensi Drop Out',
            'J1' => 'Confidence (%)',
            'K1' => 'Rekomendasi Akademik',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '047857']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $prediksis = $batch->prediksis()->get();
        $rowIdx = 2;

        foreach ($prediksis as $i => $item) {
            $input = $item->input_params_json ?? [];
            $sheet->setCellValue('A' . $rowIdx, $i + 1);
            $sheet->setCellValue('B' . $rowIdx, $item->nim ?? '-');
            $sheet->setCellValue('C' . $rowIdx, $item->nama_mahasiswa);
            $sheet->setCellValue('D' . $rowIdx, $item->semester);
            $sheet->setCellValue('E' . $rowIdx, $input['ipk'] ?? '-');
            $sheet->setCellValue('F' . $rowIdx, $input['persentase_kehadiran'] ?? '-');
            $sheet->setCellValue('G' . $rowIdx, ($input['status_cuti'] ?? false) ? 'Ya' : 'Tidak');
            $sheet->setCellValue('H' . $rowIdx, $item->hasil_klasifikasi);
            $sheet->setCellValue('I' . $rowIdx, $item->status_do);
            $sheet->setCellValue('J' . $rowIdx, $item->confidence_score);
            $sheet->setCellValue('K' . $rowIdx, $item->rekomendasi_akademik);
            $rowIdx++;
        }

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="Hasil_Prediksi_' . $batch->batch_code . '.xlsx"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Helper to match an input with stored rules
     */
    protected function findMatchingRule(int $modelId, array $inputCategories): ?C45Rule
    {
        $rules = C45Rule::where('model_id', $modelId)->get();

        foreach ($rules as $rule) {
            $conditions = $rule->conditions_json ?? [];
            $isMatch = true;

            foreach ($conditions as $cond) {
                $attr = $cond['attribute'] ?? '';
                $val = $cond['value'] ?? '';

                if (($inputCategories[$attr] ?? null) !== $val) {
                    $isMatch = false;
                    break;
                }
            }

            if ($isMatch && !empty($conditions)) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * Helper to generate proactive academic intervention recommendation text
     */
    protected function generateRecommendation(string $decision, float $ipk, float $kehadiran, int $sksTidakLulus, bool $isCuti): string
    {
        if ($decision === 'Risiko Rendah') {
            return "Performa akademik sangat baik (IPK: {$ipk}, Kehadiran: {$kehadiran}%). Mahasiswa berpeluang tinggi lulus tepat waktu. Disarankan untuk mulai merancang topik proposal skripsi/tugas akhir dan aktif mengikuti kegiatan ilmiah/magang.";
        } elseif ($decision === 'Risiko Sedang') {
            $catatan = [];
            if ($ipk <= 3.00) $catatan[] = "peningkatan nilai IPK";
            if ($kehadiran < 85.0) $catatan[] = "peningkatan absensi perkuliahan di atas 85%";
            if ($sksTidakLulus > 0) $catatan[] = "perbaikan mata kuliah mengulang ({$sksTidakLulus} SKS)";
            $catatanStr = !empty($catatan) ? implode(", ", $catatan) : "pemantauan studi berkala";

            return "Perlu pendampingan Dosen Pembimbing Akademik (Dosen PA). Mahasiswa disarankan fokus pada {$catatanStr}, serta membatasi pengambilan beban SKS maksimal 20 SKS pada semester berikutnya.";
        } else {
            return "PERINGATAN DINI (EARLY WARNING): Mahasiswa berpotensi mengalami kendala kelulusan atau risiko Drop Out (IPK: {$ipk}, Kehadiran: {$kehadiran}%). Direkomendasikan segera dilakukan pemanggilan konseling akademik khusus bersama Kaprodi/Dosen PA, pembatasan beban SKS, dan program remidial intensif.";
        }
    }
}
