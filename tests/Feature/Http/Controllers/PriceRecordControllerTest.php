<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Commodity;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceRecordControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_index_page_loads_successfully(): void
    {
        $this->actingAsUser();
        $response = $this->get(route('price-records.index'));

        $response->assertStatus(200);
        $response->assertSee('Data Harga Komoditas');
    }

    public function test_create_page_loads_successfully(): void
    {
        $this->actingAsUser();
        Commodity::factory()->create(['name' => 'Beras']);
        Region::factory()->create(['name' => 'Jakarta', 'type' => 'province']);

        $response = $this->get(route('price-records.create'));

        $response->assertStatus(200);
        $response->assertSee('Catat Harga Baru');
    }

    public function test_can_store_price_record(): void
    {
        $this->actingAsUser();
        $commodity = Commodity::factory()->create(['name' => 'Beras Premium']);
        $region = Region::factory()->create(['name' => 'Jakarta', 'type' => 'province']);

        $response = $this->post(route('price-records.store'), [
            'commodity_id' => $commodity->id,
            'region_id' => $region->id,
            'price' => 15000,
            'recorded_date' => '2024-01-15',
            'source' => 'Pasar Induk',
            'notes' => 'Harga stabil',
        ]);

        $response->assertRedirect(route('price-records.index'));
        $this->assertDatabaseHas('price_records', [
            'commodity_id' => $commodity->id,
            'region_id' => $region->id,
            'price' => 15000,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAsUser();
        $response = $this->post(route('price-records.store'), []);

        $response->assertSessionHasErrors(['commodity_id', 'region_id', 'price', 'recorded_date']);
    }
}
