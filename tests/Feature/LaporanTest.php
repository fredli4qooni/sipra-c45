<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $prodi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', 'admin')->first();
        $this->prodi = User::where('role', 'prodi')->first();
    }

    public function test_admin_can_view_laporan_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index'));
        $response->assertStatus(200);
        $response->assertSee('Laporan & Rekapitulasi');
    }

    public function test_admin_can_view_official_print_report(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.print'));
        $response->assertStatus(200);
        $response->assertSee('REKAPITULASI HASIL PREDIKSI RISIKO AKADEMIK MAHASISWA');
        $response->assertSee('UNIVERSITAS ISLAM NEGERI RADEN INTAN LAMPUNG');
    }

    public function test_admin_can_export_laporan_excel(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.export'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'attachment; filename="Export_Data_Akademik_'));
    }
}
