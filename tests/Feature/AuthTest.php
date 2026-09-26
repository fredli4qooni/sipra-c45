<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Portal Prediksi Akademik');
    }

    public function test_admin_can_login_and_redirect_to_admin_dashboard(): void
    {
        $response = $this->post('/login', [
            'login_identifier' => 'admin@uinril.ac.id',
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        // Follow redirect to dashboard
        $dashResponse = $this->get(route('dashboard'));
        $dashResponse->assertRedirect(route('admin.dashboard'));

        $adminDash = $this->get(route('admin.dashboard'));
        $adminDash->assertStatus(200);
        $adminDash->assertSee('Dashboard Administrator');
    }

    public function test_mahasiswa_can_login_with_nim(): void
    {
        $response = $this->post('/login', [
            'login_identifier' => '2271020052',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $dashResponse = $this->get(route('dashboard'));
        $dashResponse->assertRedirect(route('mahasiswa.dashboard'));

        $mhsDash = $this->get(route('mahasiswa.dashboard'));
        $mhsDash->assertStatus(200);
        $mhsDash->assertSee('Pingky Hera Veliyanti');
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_help_center(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->get(route('bantuan'));
        $response->assertStatus(200);
        $response->assertSee('Pusat Bantuan & Panduan Sistem');
        $response->assertSee('Landasan Matematis Algoritma C4.5');
    }

    public function test_authenticated_user_can_view_profile_page(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->get(route('profile.edit'));
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Profil Akun');
        $response->assertSee($user->email);
    }

    public function test_user_can_upload_avatar(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = User::first();
        $file = \Illuminate\Http\UploadedFile::fake()->image('profile.jpg', 200, 200);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'phone' => '08123456789',
            'avatar' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('Updated Name', $user->name);
        $this->assertNotNull($user->avatar);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($user->avatar);
        $this->assertNotNull($user->avatar_url);
    }

    public function test_user_can_remove_avatar(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = User::first();
        $file = \Illuminate\Http\UploadedFile::fake()->image('profile.png', 100, 100);
        $path = $file->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'remove_avatar' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertNull($user->avatar);
        $this->assertNull($user->avatar_url);
    }
}
