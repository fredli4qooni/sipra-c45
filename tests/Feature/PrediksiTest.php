<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\C45Model;
use App\Models\Prediksi;

class PrediksiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Mahasiswa $mahasiswa;
    protected C45Model $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', 'admin')->first();
        $this->mahasiswa = Mahasiswa::first();
        $this->model = C45Model::active()->first();
    }

    public function test_admin_can_access_prediksi_index_and_single_form(): void
    {
        $resIndex = $this->actingAs($this->admin)->get(route('admin.prediksi.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Sistem Prediksi Risiko Akademik');

        $resSingle = $this->actingAs($this->admin)->get(route('admin.prediksi.single'));
        $resSingle->assertStatus(200);
        $resSingle->assertSee('Simulasi Prediksi Risiko Tunggal');
    }

    public function test_admin_can_execute_single_prediction(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.prediksi.single.store'), [
            'mahasiswa_id' => $this->mahasiswa->id,
            'nim' => $this->mahasiswa->nim,
            'nama_mahasiswa' => $this->mahasiswa->nama,
            'semester' => 4,
            'ipk' => 3.80,
            'ips' => 3.75,
            'sks_semester' => 22,
            'sks_tidak_lulus' => 0,
            'persentase_kehadiran' => 95.0,
            'status_cuti' => 0,
        ]);

        $prediksi = Prediksi::latest()->first();
        $this->assertNotNull($prediksi);
        $this->assertEquals('Risiko Rendah', $prediksi->hasil_klasifikasi);
        $this->assertNotEmpty($prediksi->rekomendasi_akademik);

        $response->assertRedirect(route('admin.prediksi.show', $prediksi));

        $resShow = $this->actingAs($this->admin)->get(route('admin.prediksi.show', $prediksi));
        $resShow->assertStatus(200);
        $resShow->assertSee('Lembar Hasil Klasifikasi Risiko Akademik');
    }

    public function test_admin_and_prodi_can_download_batch_template(): void
    {
        $responseAdmin = $this->actingAs($this->admin)->get(route('admin.prediksi.batch.template'));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertHeader('Content-Disposition', 'attachment; filename="Template_Prediksi_Massal_C45.xlsx"');

        $prodi = User::where('role', 'prodi')->first();
        $responseProdi = $this->actingAs($prodi)->get(route('prodi.prediksi.batch.template'));
        $responseProdi->assertStatus(200);
        $responseProdi->assertHeader('Content-Disposition', 'attachment; filename="Template_Prediksi_Massal_C45.xlsx"');
    }

    public function test_admin_can_execute_batch_prediction_with_excel_file(): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['NIM', 'Nama Mahasiswa', 'Semester', 'IPS', 'IPK', 'SKS Semester', 'SKS Tidak Lulus', 'Kehadiran (%)', 'Status Cuti'],
            ['2271029001', 'Siswa Batch A', 4, 3.80, 3.75, 22, 0, 95.0, 'Tidak'],
            ['2271029002', 'Siswa Batch B', 6, 2.10, 2.20, 14, 8, 65.0, 'Ya'],
        ]);

        $tempPath = tempnam(sys_get_temp_dir(), 'xlsx_test') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tempPath);

        $file = new \Illuminate\Http\UploadedFile(
            $tempPath,
            'test_batch_prediksi.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($this->admin)->post(route('admin.prediksi.batch.store'), [
            'file' => $file,
        ]);

        $batch = \App\Models\PrediksiBatch::latest()->first();
        $this->assertNotNull($batch);
        $this->assertEquals(2, $batch->total_records);
        $this->assertEquals(0, $batch->skipped_records);

        $response->assertRedirect(route('admin.prediksi.batch.show', $batch));

        $showResponse = $this->actingAs($this->admin)->get(route('admin.prediksi.batch.show', $batch));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Siswa Batch A');
        $showResponse->assertSee('Siswa Batch B');

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }
    }

    public function test_batch_prediction_skips_invalid_rows_and_records_error_logs(): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['NIM', 'Nama Mahasiswa', 'Semester', 'IPS', 'IPK', 'SKS Semester', 'SKS Tidak Lulus', 'Kehadiran (%)', 'Status Cuti'],
            ['2271029003', 'Siswa Valid', 4, 3.50, 3.60, 20, 0, 90.0, 'Tidak'],
            ['2271029004', 'Siswa IPK Rusak', 4, 3.50, 5.50, 20, 0, 90.0, 'Tidak'],
            ['2271029005', 'Siswa Kehadiran Rusak', 4, 3.50, 3.50, 20, 0, 150.0, 'Tidak'],
        ]);

        $tempPath = tempnam(sys_get_temp_dir(), 'xlsx_invalid') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tempPath);

        $file = new \Illuminate\Http\UploadedFile(
            $tempPath,
            'test_invalid_rows.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($this->admin)->post(route('admin.prediksi.batch.store'), [
            'file' => $file,
        ]);

        $batch = \App\Models\PrediksiBatch::latest()->first();
        $this->assertNotNull($batch);
        $this->assertEquals(1, $batch->total_records);
        $this->assertEquals(2, $batch->skipped_records);
        $this->assertCount(2, $batch->error_logs);

        $showResponse = $this->actingAs($this->admin)->get(route('admin.prediksi.batch.show', $batch));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Catatan Validasi: 2 Baris Data Dilewati');
        $showResponse->assertSee('Siswa IPK Rusak');
        $showResponse->assertSee('Siswa Kehadiran Rusak');

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }
    }
}
