<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Commodity;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_dashboard_page_loads_successfully(): void
    {
        $this->actingAsUser();
        Commodity::factory()->create(['name' => 'Beras Premium']);
        Region::factory()->create(['name' => 'Jakarta', 'type' => 'province']);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    public function test_dashboard_returns_correct_view(): void
    {
        $this->actingAsUser();
        $response = $this->get(route('dashboard'));

        $response->assertViewIs('dashboard.index');
    }

    public function test_dashboard_has_viewmodel(): void
    {
        $this->actingAsUser();
        $response = $this->get(route('dashboard'));

        $response->assertViewHas('viewModel');
    }
}
