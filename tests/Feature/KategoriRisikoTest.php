<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\DataAkademik;
use App\Models\KategoriRisiko;

class KategoriRisikoTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $mahasiswaUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
        $this->mahasiswaUser = User::where('role', 'mahasiswa')->first();
    }

    public function test_admin_can_view_kategori_risiko_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.risiko.index'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Teks & Kategori Risiko');
        $response->assertSee('Risiko Tinggi');
        $response->assertSee('Risiko Sedang');
        $response->assertSee('Risiko Rendah');
    }

    public function test_admin_can_view_edit_page(): void
    {
        $risikoTinggi = KategoriRisiko::where('kode', 'tinggi')->first();
        $this->assertNotNull($risikoTinggi);

        $response = $this->actingAs($this->admin)->get(route('admin.risiko.edit', $risikoTinggi));
        $response->assertStatus(200);
        $response->assertSee('Edit Teks Peringatan: Risiko Tinggi');
        $response->assertSee('Template Draf Pesan WhatsApp Otomatis');
    }

    public function test_admin_can_update_custom_warning_text_and_it_shows_on_mahasiswa_dashboard(): void
    {
        $risikoTinggi = KategoriRisiko::where('kode', 'tinggi')->first();
        $customText = 'PERINGATAN KHUSUS ADMIN: Anda wajib menghadap Dosen PA dalam 3x24 jam untuk evaluasi kelulusan!';
        $customRekomendasi = 'Rekomendasi Kustom: Segera jadwalkan konsultasi khusus pembatasan SKS.';

        $response = $this->actingAs($this->admin)->put(route('admin.risiko.update', $risikoTinggi), [
            'label_badge' => 'Prioritas Super Kritis',
            'deskripsi_singkat' => 'Deskripsi kustom untuk evaluasi studi.',
            'pesan_peringatan' => $customText,
            'rekomendasi_studi' => $customRekomendasi,
            'panduan_konsultasi_pa' => "Poin 1: Bawa KHS terbaru\nPoin 2: Ajukan surat permohonan bimbingan",
            'template_wa' => "Pesan WA Kustom untuk {nama} (NPM: {nim}) kepada {dosen_pa}.",
        ]);

        $response->assertRedirect(route('admin.risiko.index'));
        $this->assertDatabaseHas('kategori_risikos', [
            'id' => $risikoTinggi->id,
            'label_badge' => 'Prioritas Super Kritis',
            'pesan_peringatan' => $customText,
            'rekomendasi_studi' => $customRekomendasi,
        ]);

        // Verify updated text appears when high-risk student opens their dashboard
        $mhs = Mahasiswa::where('nim', $this->mahasiswaUser->nim_nip)->first();
        $akademik = $mhs->latestAkademik;
        $akademik->update(['label_risiko_aktual' => 'Risiko Tinggi']);

        $mhsResponse = $this->actingAs($this->mahasiswaUser)->get(route('mahasiswa.dashboard'));
        $mhsResponse->assertStatus(200);
        $mhsResponse->assertSee($customText);
        $mhsResponse->assertSee($customRekomendasi);
        $mhsResponse->assertSee('Prioritas Super Kritis');
        $mhsResponse->assertSee('Poin 1: Bawa KHS terbaru');
    }

    public function test_admin_can_reset_custom_warning_text_to_default(): void
    {
        $risikoTinggi = KategoriRisiko::where('kode', 'tinggi')->first();
        $risikoTinggi->update([
            'pesan_peringatan' => 'Teks yang diubah sembarangan',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.risiko.reset', $risikoTinggi));
        $response->assertRedirect(route('admin.risiko.index'));

        $defaultPreset = KategoriRisiko::getDefaultPresets()['tinggi'];
        $this->assertDatabaseHas('kategori_risikos', [
            'id' => $risikoTinggi->id,
            'pesan_peringatan' => $defaultPreset['pesan_peringatan'],
        ]);
    }

    public function test_non_admin_cannot_access_or_modify_kategori_risiko(): void
    {
        $risikoTinggi = KategoriRisiko::where('kode', 'tinggi')->first();

        // Mahasiswa access
        $responseIndex = $this->actingAs($this->mahasiswaUser)->get(route('admin.risiko.index'));
        $responseIndex->assertStatus(403);

        $responseEdit = $this->actingAs($this->mahasiswaUser)->get(route('admin.risiko.edit', $risikoTinggi));
        $responseEdit->assertStatus(403);

        $responsePut = $this->actingAs($this->mahasiswaUser)->put(route('admin.risiko.update', $risikoTinggi), [
            'label_badge' => 'Hack',
            'pesan_peringatan' => 'Hack text',
            'rekomendasi_studi' => 'Hack text',
        ]);
        $responsePut->assertStatus(403);
    }
}
