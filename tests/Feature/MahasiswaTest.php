<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Mahasiswa;

class MahasiswaTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', 'admin')->first();
    }

    public function test_admin_can_view_mahasiswa_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.mahasiswa.index'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Mahasiswa');
    }

    public function test_admin_can_create_new_mahasiswa(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.mahasiswa.store'), [
            'nim' => '2271029999',
            'nama' => 'Test Mahasiswa Baru',
            'angkatan' => 2022,
            'jenis_kelamin' => 'L',
            'jalur_masuk' => 'SNBP',
            'status_mahasiswa' => 'Aktif',
        ]);

        $response->assertRedirect(route('admin.mahasiswa.index'));
        $this->assertDatabaseHas('mahasiswas', ['nim' => '2271029999']);
    }

    public function test_admin_can_view_and_update_mahasiswa(): void
    {
        $mhs = Mahasiswa::where('nim', '2271020052')->first();

        $showResponse = $this->actingAs($this->admin)->get(route('admin.mahasiswa.show', $mhs));
        $showResponse->assertStatus(200);
        $showResponse->assertSee($mhs->nama);

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.mahasiswa.update', $mhs), [
            'nim' => $mhs->nim,
            'nama' => 'Pingky Hera Veliyanti (Updated)',
            'angkatan' => 2022,
            'jenis_kelamin' => 'P',
            'status_mahasiswa' => 'Aktif',
        ]);

        $updateResponse->assertRedirect(route('admin.mahasiswa.index'));
        $this->assertDatabaseHas('mahasiswas', ['nama' => 'Pingky Hera Veliyanti (Updated)']);
    }
}
