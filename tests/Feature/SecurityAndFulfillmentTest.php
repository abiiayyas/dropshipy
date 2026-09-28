<?php

namespace Tests\Feature;

use App\Models\LandingPage;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\OrderCancellationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAndFulfillmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_is_not_available(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [])->assertNotFound();
    }

    public function test_operator_cannot_access_admin_only_catalog_management(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);

        $this->actingAs($operator)
            ->get(route('admin.products.index'))
            ->assertForbidden();
    }

    public function test_inactive_landing_page_cannot_open_checkout(): void
    {
        $landingPage = $this->landingPage(['is_active' => false]);

        $this->get(route('checkout.form', $landingPage->slug))->assertNotFound();
    }

    public function test_inactive_variant_cannot_open_checkout(): void
    {
        $product = $this->product(['has_variants' => true]);
        $landingPage = LandingPage::create([
            'product_id' => $product->id,
            'slug' => 'produk-variant',
            'is_active' => true,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sell_price' => 100000,
            'stock' => 1,
            'is_active' => false,
        ]);

        $this->get(route('checkout.form', ['slug' => $landingPage->slug, 'variant_id' => $variant->id]))
            ->assertNotFound();
    }

    public function test_tracking_requires_an_opaque_token_or_the_customer_phone(): void
    {
        $order = $this->order();

        $this->get('/track/' . $order->order_number)->assertNotFound();
        $this->get(route('tracking.show', ['publicToken' => $order->public_token]))->assertOk();

        $this->post(route('tracking.lookup'), [
            'order_number' => $order->order_number,
            'customer_phone' => '081234567890',
        ])->assertOk();

        $this->post(route('tracking.lookup'), [
            'order_number' => $order->order_number,
            'customer_phone' => '081234567899',
        ])->assertRedirect()->assertSessionHas('error');
    }

    public function test_cancellation_restores_variant_stock_once(): void
    {
        $product = $this->product(['has_variants' => true]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sell_price' => 100000,
            'cost_price' => 50000,
            'stock' => 0,
            'is_active' => true,
        ]);

        $order = $this->order(['product_id' => $product->id, 'product_variant_id' => $variant->id]);
        $cancellation = app(OrderCancellationService::class);

        $cancellation->cancel($order, 'expired');
        $cancellation->cancel($order, 'expired');

        $this->assertSame(1, $variant->fresh()->stock);
        $this->assertSame('cancelled', $order->fresh()->order_status);
        $this->assertSame('expired', $order->fresh()->payment_status);
    }
    public function test_processing_cod_order_is_in_the_supplier_queue(): void
    {
        $order = $this->order([
            'is_cod' => true,
            'payment_method' => 'cod',
            'order_status' => 'processing',
        ]);
        $operator = User::factory()->create(['role' => 'operator']);

        $this->actingAs($operator)
            ->get(route('admin.orders.supplier-queue'))
            ->assertOk()
            ->assertSee($order->order_number);
    }

    public function test_only_canonical_youtube_urls_are_exposed_as_embeds(): void
    {
        $landingPage = $this->landingPage(['embed_code' => '<script>alert(1)</script>']);

        $this->assertNull($landingPage->youtube_embed_url);

        $landingPage->embed_code = 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ';

        $this->assertSame('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', $landingPage->youtube_embed_url);
    }

    public function test_landing_page_rejects_raw_embed_html(): void
    {
        $product = $this->product();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.landing-pages.store'), [
                'product_id' => $product->id,
                'slug' => 'embed-html-rejected',
                'embed_code' => '<script>alert(1)</script>',
            ])
            ->assertSessionHasErrors('embed_code');
    }

    private function order(array $attributes = []): Order
    {
        $landingPage = $this->landingPage();

        return Order::create(array_merge([
            'landing_page_id' => $landingPage->id,
            'product_id' => $landingPage->product_id,
            'customer_name' => 'Customer Test',
            'customer_phone' => '081234567890',
            'customer_address' => 'Jl. Test No. 1',
            'customer_city' => 'Jakarta',
            'customer_province' => 'DKI Jakarta',
            'customer_postal_code' => '10110',
            'qty' => 1,
            'unit_price' => 100000,
            'shipping_courier' => 'jne',
            'shipping_service' => 'REG',
            'shipping_cost' => 10000,
            'total_amount' => 110000,
        ], $attributes));
    }

    private function landingPage(array $attributes = []): LandingPage
    {
        $product = $this->product();

        return LandingPage::create(array_merge([
            'product_id' => $product->id,
            'slug' => 'produk-' . fake()->unique()->slug(2),
            'is_active' => true,
        ], $attributes));
    }

    private function product(array $attributes = []): Product
    {
        return Product::create(array_merge([
            'name' => 'Produk Test',
            'sell_price' => 100000,
            'cost_price' => 50000,
            'warehouse_id' => Warehouse::query()->value('id'),
            'is_active' => true,
        ], $attributes));
    }
}
