<?php

namespace Tests\Feature;

use App\Models\LandingPage;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_lists_only_active_products_with_active_landing_pages(): void
    {
        $visible = $this->product('Visible Product');
        $visibleLandingPage = LandingPage::create([
            'product_id' => $visible->id,
            'slug' => 'visible-product',
            'is_active' => true,
        ]);

        $inactiveProduct = $this->product('Inactive Product', ['is_active' => false]);
        LandingPage::create([
            'product_id' => $inactiveProduct->id,
            'slug' => 'inactive-product',
            'is_active' => true,
        ]);

        $unpublished = $this->product('Unpublished Product');
        LandingPage::create([
            'product_id' => $unpublished->id,
            'slug' => 'unpublished-product',
            'is_active' => false,
        ]);

        $this->get(route('storefront.index'))
            ->assertOk()
            ->assertSeeText('Visible Product')
            ->assertSee(route('lp.show', $visibleLandingPage->slug))
            ->assertDontSeeText('Inactive Product')
            ->assertDontSeeText('Unpublished Product');
    }

    public function test_homepage_search_uses_admin_product_name_and_keeps_landing_page_checkout_entry_point(): void
    {
        $match = $this->product('Canvas Travel Bag');
        $landingPage = LandingPage::create([
            'product_id' => $match->id,
            'slug' => 'canvas-travel-bag',
            'is_active' => true,
        ]);
        $this->product('Ceramic Mug');

        $this->get(route('storefront.index', ['q' => 'travel']))
            ->assertOk()
            ->assertSeeText('Canvas Travel Bag')
            ->assertSee(route('lp.show', $landingPage->slug))
            ->assertDontSeeText('Ceramic Mug');
    }

    public function test_tracking_index_is_available_from_storefront_navigation(): void
    {
        $this->get(route('tracking.index'))
            ->assertOk()
            ->assertSeeText('Cek Status Order');
    }
    public function test_landing_page_ignores_legacy_serialized_model_cache(): void
    {
        $product = $this->product('Cache Safe Product');
        $landingPage = LandingPage::create([
            'product_id' => $product->id,
            'slug' => 'cache-safe-product',
            'is_active' => true,
        ]);

        DB::table('cache')->insert([
            'key' => config('cache.prefix') . 'lp_data_' . $landingPage->slug,
            'value' => serialize($landingPage),
            'expiration' => now()->addHour()->timestamp,
        ]);

        $this->get(route('lp.show', $landingPage->slug))
            ->assertOk()
            ->assertSeeText('Cache Safe Product');
    }


    private function product(string $name, array $attributes = []): Product
    {
        return Product::create(array_merge([
            'name' => $name,
            'sell_price' => 100000,
            'cost_price' => 50000,
            'warehouse_id' => Warehouse::query()->value('id'),
            'is_active' => true,
        ], $attributes));
    }
}
