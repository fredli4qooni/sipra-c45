<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\C45Model;

class C45TrainingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', 'admin')->first();
    }

    public function test_admin_can_access_c45_index_and_create_pages(): void
    {
        $responseIndex = $this->actingAs($this->admin)->get(route('admin.c45.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Data Mining Decision Tree C4.5');

        $responseCreate = $this->actingAs($this->admin)->get(route('admin.c45.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Training Model C4.5 Baru');
    }

    public function test_admin_can_train_new_c45_model(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.c45.store'), [
            'nama_model' => 'Model C4.5 Test Otomatis',
            'deskripsi' => 'Pengujian unit training algoritma C4.5',
            'split_ratio' => '80:20',
            'random_seed' => 777,
            'features' => ['kategori_ipk', 'kategori_kehadiran', 'kategori_sks'],
            'is_active' => 1,
        ]);

        $this->assertDatabaseHas('c45_models', [
            'nama_model' => 'Model C4.5 Test Otomatis',
            'random_seed' => 777,
            'is_active' => true,
        ]);

        $model = C45Model::where('nama_model', 'Model C4.5 Test Otomatis')->first();
        $this->assertNotNull($model);
        $this->assertGreaterThan(0, $model->rules()->count());

        $response->assertRedirect(route('admin.c45.show', $model));

        $showResponse = $this->actingAs($this->admin)->get(route('admin.c45.show', $model));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Confusion Matrix');
        $showResponse->assertSee('Seed: 777');
    }
}
