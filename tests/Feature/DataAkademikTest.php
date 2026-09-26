<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\DataAkademik;

class DataAkademikTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', 'admin')->first();
    }

    public function test_admin_can_view_akademik_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.akademik.index'));
        $response->assertStatus(200);
        $response->assertSee('Dataset Riwayat Akademik');
    }

    public function test_admin_can_create_academic_record_with_auto_categories(): void
    {
        $mhs = Mahasiswa::first();

        $response = $this->actingAs($this->admin)->post(route('admin.akademik.store'), [
            'mahasiswa_id' => $mhs->id,
            'semester' => 7,
            'tahun_akademik' => '2024/2025 Ganjil',
            'ips' => 3.85,
            'ipk' => 3.75,
            'sks_semester' => 22,
            'sks_total' => 130,
            'sks_tidak_lulus' => 0,
            'persentase_kehadiran' => 96.0,
            'status_cuti' => 0,
        ]);

        $response->assertRedirect(route('admin.akademik.index'));

        $this->assertDatabaseHas('data_akademiks', [
            'mahasiswa_id' => $mhs->id,
            'semester' => 7,
            'kategori_ipk' => 'Tinggi',
            'kategori_sks' => 'Sangat Baik',
            'kategori_kehadiran' => 'Baik',
        ]);
    }

    public function test_admin_can_download_excel_template(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.akademik.template'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'attachment; filename="Template_Import_SIPRA_C45.xlsx"'));
    }
}
