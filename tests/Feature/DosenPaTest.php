<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Mahasiswa;

class DosenPaTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $dosenPa;
    protected User $mahasiswaUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', 'admin')->first();
        $this->dosenPa = User::where('role', 'dosen_pa')->first();
        $this->mahasiswaUser = User::where('role', 'mahasiswa')->first();
    }

    public function test_admin_can_view_dosen_pa_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dosen.index'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Dosen Pembimbing Akademik');
        $response->assertSee($this->dosenPa->name);
    }

    public function test_non_admin_cannot_access_dosen_pa_index(): void
    {
        $response = $this->actingAs($this->dosenPa)->get(route('admin.dosen.index'));
        $response->assertStatus(403);

        $responseMhs = $this->actingAs($this->mahasiswaUser)->get(route('admin.dosen.index'));
        $responseMhs->assertStatus(403);
    }

    public function test_admin_can_create_new_dosen_pa(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.dosen.store'), [
            'name' => 'Bambang Kusuma, M.Kom.',
            'nim_nip' => '199001012020011003',
            'email' => 'bambang@uinril.ac.id',
            'password' => 'secret123',
            'phone' => '081234567800',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.dosen.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'bambang@uinril.ac.id',
            'role' => 'dosen_pa',
            'name' => 'Bambang Kusuma, M.Kom.',
        ]);
    }

    public function test_admin_can_view_dosen_pa_show_page_with_advisees(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dosen.show', $this->dosenPa));
        $response->assertStatus(200);
        $response->assertSee($this->dosenPa->name);
        $response->assertSee('Daftar Mahasiswa Bimbingan Akademik');
    }

    public function test_admin_can_update_dosen_pa(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.dosen.update', $this->dosenPa), [
            'name' => 'Dr. H. Ahmad Sudrajat, M.T.I. (Updated)',
            'nim_nip' => $this->dosenPa->nim_nip,
            'email' => $this->dosenPa->email,
            'phone' => '08999999999',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.dosen.index'));
        $this->assertDatabaseHas('users', [
            'id' => $this->dosenPa->id,
            'name' => 'Dr. H. Ahmad Sudrajat, M.T.I. (Updated)',
            'phone' => '08999999999',
        ]);
    }

    public function test_admin_can_delete_dosen_pa_and_advisees_are_unassigned(): void
    {
        // Create dummy Dosen PA with an assigned student
        $newDosen = User::create([
            'name' => 'Dosen Hapus Test',
            'email' => 'hapus@uinril.ac.id',
            'password' => bcrypt('password'),
            'role' => 'dosen_pa',
            'status' => 'active',
        ]);

        $mhs = Mahasiswa::first();
        $mhs->update(['dosen_pa_id' => $newDosen->id]);
        $this->assertEquals($newDosen->id, $mhs->fresh()->dosen_pa_id);

        $response = $this->actingAs($this->admin)->delete(route('admin.dosen.destroy', $newDosen));
        $response->assertRedirect(route('admin.dosen.index'));

        $this->assertDatabaseMissing('users', ['id' => $newDosen->id]);
        $this->assertNull($mhs->fresh()->dosen_pa_id);
    }
}
