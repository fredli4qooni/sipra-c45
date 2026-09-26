<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\DataAkademik;
use App\Models\C45Model;
use App\Models\Prediksi;
use App\Models\PrediksiBatch;

class BlackBoxSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $prodi;
    protected User $dosenPa;
    protected User $mahasiswaUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
        $this->prodi = User::where('role', 'prodi')->first();
        $this->dosenPa = User::where('role', 'dosen_pa')->first();
        $this->mahasiswaUser = User::where('role', 'mahasiswa')->first();
    }

    /**
     * Scenario 1: Admin Complete Workflow
     */
    public function test_blackbox_admin_full_workflow(): void
    {
        // 1. Login
        $loginRes = $this->post('/login', [
            'login_identifier' => 'admin@uinril.ac.id',
            'password' => 'admin123',
        ]);
        $loginRes->assertRedirect(route('dashboard'));

        // 2. Access Admin Dashboard
        $dashRes = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $dashRes->assertStatus(200);
        $dashRes->assertSee('Dashboard Administrator');

        // 3. Create Student
        $mhsRes = $this->actingAs($this->admin)->post(route('admin.mahasiswa.store'), [
            'nim' => '2271029998',
            'nama' => 'Budi Blackbox Test',
            'angkatan' => 2022,
            'jenis_kelamin' => 'L',
            'jalur_masuk' => 'SNBP',
            'status_mahasiswa' => 'Aktif',
        ]);
        $mhsRes->assertRedirect(route('admin.mahasiswa.index'));
        $mhs = Mahasiswa::where('nim', '2271029998')->first();
        $this->assertNotNull($mhs);

        // 4. Create Academic Record
        $akdRes = $this->actingAs($this->admin)->post(route('admin.akademik.store'), [
            'mahasiswa_id' => $mhs->id,
            'semester' => 4,
            'tahun_akademik' => '2023/2024 Genap',
            'ips' => 3.80,
            'ipk' => 3.75,
            'sks_semester' => 22,
            'sks_total' => 88,
            'sks_tidak_lulus' => 0,
            'persentase_kehadiran' => 96.0,
            'status_cuti' => 0,
        ]);
        $akdRes->assertRedirect(route('admin.akademik.index'));

        // 5. Train C4.5 Model
        $trainRes = $this->actingAs($this->admin)->post(route('admin.c45.store'), [
            'nama_model' => 'Model C4.5 Blackbox Validation',
            'deskripsi' => 'Pengujian end-to-end model',
            'split_ratio' => '80:20',
            'features' => ['kategori_ipk', 'kategori_kehadiran', 'kategori_sks'],
            'is_active' => 1,
        ]);
        $model = C45Model::where('nama_model', 'Model C4.5 Blackbox Validation')->first();
        $this->assertNotNull($model);
        $trainRes->assertRedirect(route('admin.c45.show', $model));

        // 6. Perform Single Prediction
        $predRes = $this->actingAs($this->admin)->post(route('admin.prediksi.single.store'), [
            'mahasiswa_id' => $mhs->id,
            'nim' => $mhs->nim,
            'nama_mahasiswa' => $mhs->nama,
            'semester' => 4,
            'ipk' => 3.75,
            'ips' => 3.80,
            'sks_semester' => 22,
            'sks_tidak_lulus' => 0,
            'persentase_kehadiran' => 96.0,
            'status_cuti' => 0,
        ]);
        $prediksi = Prediksi::latest()->first();
        $this->assertNotNull($prediksi);
        $this->assertEquals('Risiko Rendah', $prediksi->hasil_klasifikasi);
        $predRes->assertRedirect(route('admin.prediksi.show', $prediksi));

        // 7. Check EWS Center
        $ewsRes = $this->actingAs($this->admin)->get(route('admin.ews.index'));
        $ewsRes->assertStatus(200);

        // 8. Generate Official Printable Report
        $printRes = $this->actingAs($this->admin)->get(route('admin.laporan.print'));
        $printRes->assertStatus(200);
        $printRes->assertSee('UNIVERSITAS ISLAM NEGERI RADEN INTAN LAMPUNG');
    }

    /**
     * Scenario 2: Prodi & Dosen PA Workflow
     */
    public function test_blackbox_prodi_and_dosen_pa_workflow(): void
    {
        $loginRes = $this->post('/login', [
            'login_identifier' => 'kaprodi@uinril.ac.id',
            'password' => 'prodi123',
        ]);
        $loginRes->assertRedirect(route('dashboard'));

        $dashRes = $this->actingAs($this->prodi)->get(route('prodi.dashboard'));
        $dashRes->assertStatus(200);
        $dashRes->assertSee('Monitoring Risiko Akademik Mahasiswa');

        $treeRes = $this->actingAs($this->prodi)->get(route('prodi.tree.show'));
        $treeRes->assertStatus(200);

        $rulesRes = $this->actingAs($this->prodi)->get(route('prodi.rules.index'));
        $rulesRes->assertStatus(200);

        $ewsRes = $this->actingAs($this->prodi)->get(route('prodi.ews.index'));
        $ewsRes->assertStatus(200);
    }

    /**
     * Scenario 3: Mahasiswa Workflow
     */
    public function test_blackbox_mahasiswa_workflow(): void
    {
        $loginRes = $this->post('/login', [
            'login_identifier' => '2271020052',
            'password' => 'password',
        ]);
        $loginRes->assertRedirect(route('dashboard'));

        $dashRes = $this->actingAs($this->mahasiswaUser)->get(route('mahasiswa.dashboard'));
        $dashRes->assertStatus(200);
        $dashRes->assertSee('Pingky Hera Veliyanti');
        $dashRes->assertSee('Status Deteksi Dini');
    }
}
