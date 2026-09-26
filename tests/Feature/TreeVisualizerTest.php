<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\C45Model;

class TreeVisualizerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', 'admin')->first();
    }

    public function test_admin_can_view_decision_tree_visualization(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.tree.show'));
        $response->assertStatus(200);
        $response->assertSee('Struktur Hierarki Pohon Keputusan');
    }

    public function test_admin_can_view_rules_explorer(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.rules.index'));
        $response->assertStatus(200);
        $response->assertSee('Rules Explorer (IF - THEN)');
    }
}
