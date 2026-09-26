<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Models\Mahasiswa;
use App\Models\DataAkademik;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpreadsheetService
{
    /**
     * Generate and stream download of pre-formatted Excel Import Template
     */
    public static function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Mahasiswa');

        // Headers
        $headers = [
            'A1' => 'NIM',
            'B1' => 'Nama Mahasiswa',
            'C1' => 'Angkatan',
            'D1' => 'Jenis Kelamin (L/P)',
            'E1' => 'Jalur Masuk',
            'F1' => 'No HP',
            'G1' => 'Alamat',
            'H1' => 'Tinggal Dengan',
            'I1' => 'Semester',
            'J1' => 'Tahun Akademik',
            'K1' => 'IPS',
            'L1' => 'IPK',
            'M1' => 'SKS Semester',
            'N1' => 'SKS Total Kumulatif',
            'O1' => 'SKS Tidak Lulus',
            'P1' => 'Kehadiran (%)',
            'Q1' => 'Status Cuti (Ya/Tidak)',
            'R1' => 'Label Risiko Aktual (Opsional)',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Header Styling
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '047857'], // Emerald-700
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '064E3B'],
                ],
            ],
        ];

        $sheet->getStyle('A1:R1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Sample Example Row 1
        $sampleData = [
            '2271020052', 'Pingky Hera Veliyanti', 2022, 'P', 'SPAN-PTKIN', '089876543210', 'Bandar Lampung', 'Orang Tua',
            4, '2023/2024 Genap', 3.82, 3.78, 22, 86, 0, 95.0, 'Tidak', 'Risiko Rendah'
        ];

        // Sample Example Row 2
        $sampleData2 = [
            '2271020099', 'Contoh Mahasiswa Risiko Tinggi', 2022, 'L', 'Mandiri', '081234567899', 'Lampung Selatan', 'Kos',
            4, '2023/2024 Genap', 2.10, 2.20, 14, 56, 12, 60.0, 'Tidak', 'Risiko Tinggi'
        ];

        $sheet->fromArray([$sampleData, $sampleData2], null, 'A2');

        // Auto size columns
        foreach (range('A', 'R') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="Template_Import_SIPRA_C45.xlsx"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Import students & academic records from an uploaded Excel or CSV file
     */
    public static function importData($file): array
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (count($rows) <= 1) {
            throw new \Exception('File Excel kosong atau tidak memiliki baris data.');
        }

        // Remove header row
        $header = array_shift($rows);

        $totalRead = 0;
        $successCount = 0;
        $skippedCount = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                $nim = trim((string) ($row[0] ?? ''));
                $nama = trim((string) ($row[1] ?? ''));

                if (empty($nim) || empty($nama)) {
                    $skippedCount++;
                    continue;
                }

                $totalRead++;

                $angkatan = (int) ($row[2] ?? date('Y'));
                $jenisKelamin = strtoupper(trim((string) ($row[3] ?? 'L')));
                $jenisKelamin = in_array($jenisKelamin, ['L', 'P']) ? $jenisKelamin : 'L';
                $jalurMasuk = trim((string) ($row[4] ?? ''));
                $noHp = trim((string) ($row[5] ?? ''));
                $alamat = trim((string) ($row[6] ?? ''));
                $tinggalDengan = trim((string) ($row[7] ?? ''));

                // Academic fields
                $semester = (int) ($row[8] ?? 1);
                $tahunAkademik = trim((string) ($row[9] ?? ''));
                $ips = (float) str_replace(',', '.', (string) ($row[10] ?? 0));
                $ipk = (float) str_replace(',', '.', (string) ($row[11] ?? 0));
                $sksSemester = (int) ($row[12] ?? 0);
                $sksTotal = (int) ($row[13] ?? 0);
                $sksTidakLulus = (int) ($row[14] ?? 0);
                $kehadiran = (float) str_replace(',', '.', (string) ($row[15] ?? 0));
                $cutiStr = strtolower(trim((string) ($row[16] ?? 'tidak')));
                $isCuti = in_array($cutiStr, ['ya', '1', 'true', 'cuti']);
                $labelRisiko = trim((string) ($row[17] ?? ''));

                // 1. Create or Update Mahasiswa
                $mahasiswa = Mahasiswa::updateOrCreate(
                    ['nim' => $nim],
                    [
                        'nama' => $nama,
                        'angkatan' => $angkatan > 0 ? $angkatan : 2022,
                        'jenis_kelamin' => $jenisKelamin,
                        'jalur_masuk' => $jalurMasuk ?: null,
                        'no_hp' => $noHp ?: null,
                        'alamat' => $alamat ?: null,
                        'tinggal_dengan' => $tinggalDengan ?: null,
                        'status_mahasiswa' => $isCuti ? 'Cuti' : 'Aktif',
                    ]
                );

                // 2. Create or Update Data Akademik for the semester
                $akademik = DataAkademik::updateOrCreate(
                    [
                        'mahasiswa_id' => $mahasiswa->id,
                        'semester' => $semester,
                    ],
                    [
                        'tahun_akademik' => $tahunAkademik ?: null,
                        'ips' => $ips,
                        'ipk' => $ipk,
                        'sks_semester' => $sksSemester,
                        'sks_total' => $sksTotal,
                        'sks_tidak_lulus' => $sksTidakLulus,
                        'persentase_kehadiran' => $kehadiran,
                        'status_cuti' => $isCuti,
                        'label_risiko_aktual' => $labelRisiko ?: DataPreprocessingService::determineHeuristicRisk($ipk, $kehadiran, $sksTidakLulus, $isCuti),
                        'label_do_aktual' => ($isCuti || $ipk < 2.50) ? 'Berisiko' : 'Tidak Berisiko',
                    ]
                );

                // Auto calculate C4.5 categories
                $akademik->calculateCategories();
                $akademik->save();

                $successCount++;
            }

            DB::commit();

            return [
                'total_read' => $totalRead,
                'success_count' => $successCount,
                'skipped_count' => $skippedCount,
                'errors' => $errors,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Export academic dataset to downloadable Excel
     */
    public static function exportData($query = null): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Akademik Mahasiswa');

        $headers = [
            'A1' => 'No',
            'B1' => 'NIM',
            'C1' => 'Nama Mahasiswa',
            'D1' => 'Angkatan',
            'E1' => 'Jenis Kelamin',
            'F1' => 'Semester',
            'G1' => 'IPS',
            'H1' => 'IPK',
            'I1' => 'Kategori IPK',
            'J1' => 'SKS Semester',
            'K1' => 'SKS Total',
            'L1' => 'SKS Tidak Lulus',
            'M1' => 'Kehadiran (%)',
            'N1' => 'Kategori Kehadiran',
            'O1' => 'Status Cuti',
            'P1' => 'Status Risiko Aktual',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $sheet->getStyle('A1:P1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '047857']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $data = ($query ?? DataAkademik::with('mahasiswa')->orderBy('semester')->orderBy('mahasiswa_id'))->get();

        $rowIdx = 2;
        foreach ($data as $i => $item) {
            $sheet->setCellValue('A' . $rowIdx, $i + 1);
            $sheet->setCellValue('B' . $rowIdx, $item->mahasiswa->nim ?? '-');
            $sheet->setCellValue('C' . $rowIdx, $item->mahasiswa->nama ?? '-');
            $sheet->setCellValue('D' . $rowIdx, $item->mahasiswa->angkatan ?? '-');
            $sheet->setCellValue('E' . $rowIdx, $item->mahasiswa->jenis_kelamin ?? '-');
            $sheet->setCellValue('F' . $rowIdx, $item->semester);
            $sheet->setCellValue('G' . $rowIdx, $item->ips);
            $sheet->setCellValue('H' . $rowIdx, $item->ipk);
            $sheet->setCellValue('I' . $rowIdx, $item->kategori_ipk);
            $sheet->setCellValue('J' . $rowIdx, $item->sks_semester);
            $sheet->setCellValue('K' . $rowIdx, $item->sks_total);
            $sheet->setCellValue('L' . $rowIdx, $item->sks_tidak_lulus);
            $sheet->setCellValue('M' . $rowIdx, $item->persentase_kehadiran);
            $sheet->setCellValue('N' . $rowIdx, $item->kategori_kehadiran);
            $sheet->setCellValue('O' . $rowIdx, $item->status_cuti ? 'Ya' : 'Tidak');
            $sheet->setCellValue('P' . $rowIdx, $item->label_risiko_aktual);
            $rowIdx++;
        }

        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="Export_Data_Akademik_' . date('Ymd_His') . '.xlsx"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Generate and stream download of pre-formatted Excel Batch Prediction Template
     */
    public static function downloadBatchPredictionTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Prediksi Massal');

        // Headers
        $headers = [
            'A1' => 'NIM',
            'B1' => 'Nama Mahasiswa',
            'C1' => 'Semester',
            'D1' => 'IPS',
            'E1' => 'IPK',
            'F1' => 'SKS Semester',
            'G1' => 'SKS Tidak Lulus',
            'H1' => 'Kehadiran (%)',
            'I1' => 'Status Cuti (Ya/Tidak)',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Header Styling
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F766E'], // Teal-700
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '134E4A'],
                ],
            ],
        ];

        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Realistic Sample Data Rows
        $sampleData = [
            ['2271020001', 'Ahmad Fauzi', 4, 3.80, 3.75, 22, 0, 95.0, 'Tidak'],
            ['2271020002', 'Budi Santoso', 6, 2.85, 2.90, 20, 2, 82.0, 'Tidak'],
            ['2271020003', 'Citra Dewi', 8, 1.85, 2.10, 14, 9, 65.0, 'Ya'],
            ['2271020004', 'Dimas Prayoga', 4, 3.20, 3.15, 20, 0, 88.0, 'Tidak'],
            ['2271020005', 'Eka Rahmawati', 6, 2.40, 2.60, 16, 6, 72.0, 'Tidak'],
        ];

        $sheet->fromArray($sampleData, null, 'A2');

        // Auto size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="Template_Prediksi_Massal_C45.xlsx"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
