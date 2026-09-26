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
}
