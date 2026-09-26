<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mahasiswa;
use App\Models\DataAkademik;

class DataAkademikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mahasiswas = Mahasiswa::all();

        // Sample variations for dataset simulation
        $records = [
            // NIM => [Semester, IPS, IPK, SKS, SKS_Total, SKS_Tidak_Lulus, Kehadiran %, Cuti, Label Risiko, Label DO]
            '2271020052' => [4, 3.82, 3.78, 22, 86, 0, 95.0, false, 'Risiko Rendah', 'Tidak Berisiko'],
            '2271020001' => [4, 3.50, 3.45, 21, 84, 0, 90.0, false, 'Risiko Rendah', 'Tidak Berisiko'],
            '2271020002' => [4, 2.60, 2.55, 18, 68, 6, 72.0, false, 'Risiko Tinggi', 'Berisiko'],
            '2271020003' => [4, 3.10, 3.05, 20, 80, 0, 82.0, false, 'Risiko Sedang', 'Tidak Berisiko'],
            '2271020004' => [4, 2.30, 2.20, 15, 58, 9, 65.0, false, 'Risiko Tinggi', 'Berisiko'],
            '2271020005' => [4, 3.65, 3.60, 22, 86, 0, 92.0, false, 'Risiko Rendah', 'Tidak Berisiko'],
            '2271020006' => [4, 2.80, 2.85, 19, 74, 3, 78.0, false, 'Risiko Sedang', 'Tidak Berisiko'],
            '2271020007' => [4, 3.90, 3.88, 24, 90, 0, 98.0, false, 'Risiko Rendah', 'Tidak Berisiko'],
            '2271020008' => [4, 2.10, 2.15, 14, 52, 12, 60.0, false, 'Risiko Tinggi', 'Berisiko'],
            '2271020009' => [4, 3.20, 3.18, 20, 80, 0, 84.0, false, 'Risiko Sedang', 'Tidak Berisiko'],
            '2271020010' => [4, 2.45, 2.40, 16, 62, 8, 70.0, false, 'Risiko Tinggi', 'Berisiko'],

            '2171020001' => [6, 3.70, 3.65, 21, 128, 0, 94.0, false, 'Risiko Rendah', 'Tidak Berisiko'],
            '2171020002' => [6, 3.40, 3.35, 20, 124, 0, 88.0, false, 'Risiko Rendah', 'Tidak Berisiko'],
            '2171020003' => [6, 2.50, 2.48, 16, 102, 10, 68.0, false, 'Risiko Tinggi', 'Berisiko'],
            '2171020004' => [6, 3.00, 2.95, 19, 118, 2, 80.0, false, 'Risiko Sedang', 'Tidak Berisiko'],
            '2171020005' => [6, 1.80, 2.05, 12, 88, 18, 50.0, true,  'Risiko Tinggi', 'Berisiko'],
            '2171020006' => [6, 3.85, 3.80, 22, 132, 0, 96.0, false, 'Risiko Rendah', 'Tidak Berisiko'],
            '2171020007' => [6, 2.75, 2.80, 18, 112, 4, 76.0, false, 'Risiko Sedang', 'Tidak Berisiko'],
            '2171020008' => [6, 3.60, 3.55, 21, 126, 0, 91.0, false, 'Risiko Rendah', 'Tidak Berisiko'],
            '2171020009' => [6, 2.20, 2.30, 15, 96, 14, 62.0, false, 'Risiko Tinggi', 'Berisiko'],
            '2171020010' => [6, 3.15, 3.10, 20, 120, 0, 83.0, false, 'Risiko Sedang', 'Tidak Berisiko'],
        ];

        foreach ($mahasiswas as $mhs) {
            $data = $records[$mhs->nim] ?? [4, 3.00, 3.00, 20, 80, 0, 85.0, false, 'Risiko Sedang', 'Tidak Berisiko'];

            $akademik = new DataAkademik([
                'mahasiswa_id' => $mhs->id,
                'semester' => $data[0],
                'tahun_akademik' => '2023/2024 Genap',
                'ips' => $data[1],
                'ipk' => $data[2],
                'sks_semester' => $data[3],
                'sks_total' => $data[4],
                'sks_tidak_lulus' => $data[5],
                'persentase_kehadiran' => $data[6],
                'jumlah_kehadiran' => (int) round(($data[6] / 100) * 16),
                'total_pertemuan' => 16,
                'status_cuti' => $data[7],
                'label_risiko_aktual' => $data[8],
                'label_do_aktual' => $data[9],
                'keterangan' => 'Data riwayat akademik semester ' . $data[0],
            ]);

            $akademik->calculateCategories();
            $akademik->save();
        }
    }
}
