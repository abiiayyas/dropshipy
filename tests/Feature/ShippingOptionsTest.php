<?php

namespace Tests\Feature;

use App\Models\LandingPage;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\MengantarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ShippingOptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_shipping_options_use_mengantar_and_return_frontend_shape(): void
    {
        $warehouse = Warehouse::create([
            'name' => 'Gudang Test',
            'mengantar_area_id' => 'origin-area',
        ]);
        $product = Product::create([
            'name' => 'Produk Shipping Test',
            'sell_price' => 125000,
            'cost_price' => 50000,
            'warehouse_id' => $warehouse->id,
            'is_active' => true,
        ]);
        $landingPage = LandingPage::create([
            'product_id' => $product->id,
            'slug' => 'produk-shipping-test',
            'is_active' => true,
        ]);

        $mengantar = Mockery::mock(MengantarService::class);
        $mengantar->shouldReceive('getShippingRates')
            ->once()
            ->withArgs(function (array $origin, array $destination, array $items): bool {
                return $origin === ['area_id' => 'origin-area']
                    && $destination === ['area_id' => 'destination-area']
                    && $items[0]['value'] === 125000;
            })
            ->andReturn([
                ['code' => 'JNE', 'name' => 'JNE', 'supports_cod' => true, 'services' => [
                    ['name' => 'REG', 'description' => 'Reguler', 'cost' => 12000, 'etd' => '2-4 hari'],
                ]],
            ]);
        $this->app->instance(MengantarService::class, $mengantar);

        $this->get(route('lp.shipping', [
            'landing_page_id' => $landingPage->id,
            'destination_area_id' => 'destination-area',
        ]))
            ->assertOk()
            ->assertJsonPath('couriers.0.code', 'JNE')
            ->assertJsonPath('couriers.0.services.0.cost', 12000);
    }

    public function test_checkout_area_search_uses_mengantar(): void
    {
        $mengantar = Mockery::mock(MengantarService::class);
        $mengantar->shouldReceive('searchArea')
            ->once()
            ->with('Jakarta')
            ->andReturn([
                [
                    'id' => 'area-1',
                    'name' => 'Gambir, Gambir',
                    'administrative_division_level_1_name' => 'DKI Jakarta',
                    'administrative_division_level_2_name' => 'Jakarta Pusat',
                    'postal_code' => '10110',
                ],
            ]);
        $this->app->instance(MengantarService::class, $mengantar);

        $this->get(route('lp.search-area', ['q' => 'Jakarta']))
            ->assertOk()
            ->assertJsonPath('0.id', 'area-1');
    }
}
