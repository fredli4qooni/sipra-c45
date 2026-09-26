<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class EwsTest extends TestCase
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

    public function test_admin_and_prodi_can_view_ews_alert_center(): void
    {
        $responseAdmin = $this->actingAs($this->admin)->get(route('admin.ews.index'));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Early Warning System (EWS) Alert Center');

        $responseProdi = $this->actingAs($this->prodi)->get(route('prodi.ews.index'));
        $responseProdi->assertStatus(200);
        $responseProdi->assertSee('Early Warning System (EWS) Alert Center');
    }
}
