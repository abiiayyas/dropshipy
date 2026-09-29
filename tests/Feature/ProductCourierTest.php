<?php

namespace Tests\Feature;

use App\Models\LandingPage;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductCourierTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_mengantar_courier_codes_for_a_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $warehouse = Warehouse::create(['name' => 'Gudang Courier Test']);

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Produk Courier Test',
            'sell_price' => 100000,
            'cost_price' => 50000,
            'warehouse_id' => $warehouse->id,
            'allowed_couriers' => 'JNE, jnt',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertSame(
            ['jne', 'jnt'],
            Product::query()->latest('id')->firstOrFail()->allowed_couriers
        );
    }

    public function test_shipping_options_only_return_allowed_mengantar_couriers(): void
    {
        $landingPage = $this->landingPage(['jne', 'jnt']);
        $this->fakeMengantarRates();

        $this->get(route('lp.shipping', [
            'landing_page_id' => $landingPage->id,
            'destination_area_id' => 'destination-area',
        ]))
            ->assertOk()
            ->assertJsonCount(2, 'couriers')
            ->assertJsonPath('couriers.0.code', 'JNE')
            ->assertJsonPath('couriers.1.code', 'JNT');
    }

    public function test_order_rejects_a_courier_not_allowed_by_the_product(): void
    {
        $landingPage = $this->landingPage(['jne']);
        $this->fakeMengantarRates();

        $this->post(route('lp.order.create'), [
            'landing_page_id' => $landingPage->id,
            'customer_name' => 'Customer Test',
            'customer_phone' => '081234567890',
            'customer_address' => 'Jl. Test No. 1',
            'customer_city' => 'Jakarta',
            'customer_province' => 'DKI Jakarta',
            'customer_postal_code' => '10110',
            'destination_area_id' => 'destination-area',
            'shipping_courier' => 'jnt',
            'shipping_service' => 'REG',
            'is_cod' => true,
        ])->assertStatus(400);
    }

    private function landingPage(array $allowedCouriers): LandingPage
    {
        $warehouse = Warehouse::create([
            'name' => 'Gudang Shipping Test',
            'mengantar_area_id' => 'origin-area',
        ]);
        $product = Product::create([
            'name' => 'Produk Shipping Test',
            'sell_price' => 100000,
            'cost_price' => 50000,
            'warehouse_id' => $warehouse->id,
            'allowed_couriers' => $allowedCouriers,
            'is_active' => true,
        ]);

        return LandingPage::create([
            'product_id' => $product->id,
            'slug' => 'produk-courier-' . uniqid(),
            'is_active' => true,
        ]);
    }

    private function fakeMengantarRates(): void
    {
        Http::fake([
            '*order/estimate*' => Http::response([
                'data' => [
                    'JNE' => ['estimatedPrice' => 10000, 'estimatedDate' => '2-3 hari'],
                    'JNT' => ['estimatedPrice' => 11000, 'estimatedDate' => '2-4 hari'],
                    'SiCepat' => ['estimatedPrice' => 12000, 'estimatedDate' => '2-4 hari'],
                ],
            ]),
        ]);
    }
}
