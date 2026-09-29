<?php

namespace Tests\Feature;

use App\Models\CustomerOrderClaim;
use App\Models\LandingPage;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\User;
use App\Services\MengantarService;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class CustomerAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_without_enabling_staff_access(): void
    {
        $response = $this->post(route('account.register.store'), [
            'name' => 'Buyer Test',
            'email' => 'buyer@example.com',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('account.dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'buyer@example.com',
            'phone' => '6281234567890',
            'is_customer' => true,
        ]);

        $this->get(route('admin.products.index'))->assertForbidden();
    }

    public function test_guest_and_customer_checkout_both_work_and_only_customer_checkout_is_linked(): void
    {
        $landingPage = $this->landingPage();
        $this->mockCheckoutServices();

        $guestResponse = $this->post(route('lp.order.create'), $this->checkoutData($landingPage));
        $guestResponse->assertRedirect();
        $guestOrder = Order::query()->latest('id')->firstOrFail();
        $this->assertNull($guestOrder->user_id);

        $customer = User::factory()->customer()->create();
        $customerResponse = $this->actingAs($customer)->post(
            route('lp.order.create'),
            $this->checkoutData($landingPage, ['customer_phone' => $customer->phone ?? '081234567890'])
        );
        $customerResponse->assertRedirect();

        $customerOrder = Order::query()->latest('id')->firstOrFail();
        $this->assertSame($customer->id, $customerOrder->user_id);
        $this->assertNull($guestOrder->fresh()->user_id);
    }

    public function test_customer_account_only_lists_owned_orders(): void
    {
        $customer = User::factory()->customer()->create();
        $ownedOrder = $this->order(['user_id' => $customer->id, 'order_number' => 'ORD-OWNED-001']);
        $otherOrder = $this->order(['order_number' => 'ORD-OTHER-001']);

        $this->actingAs($customer)
            ->get(route('account.dashboard'))
            ->assertOk()
            ->assertSeeText($ownedOrder->order_number)
            ->assertDontSeeText($otherOrder->order_number);

        $this->actingAs($customer)
            ->get(route('account.orders.show', $otherOrder->id))
            ->assertNotFound();
    }

    public function test_customer_can_claim_guest_order_after_whatsapp_code_verification(): void
    {
        $customer = User::factory()->customer()->create(['phone' => '6281234567890']);
        $order = $this->order([
            'order_number' => 'ORD-GUEST-001',
            'customer_phone' => '081234567890',
        ]);
        $whatsapp = Mockery::mock(WhatsAppService::class);
        $whatsapp->shouldReceive('sendMessage')->once()->andReturn(true);
        $this->app->instance(WhatsAppService::class, $whatsapp);

        $this->actingAs($customer)
            ->post(route('account.orders.claim.store'), [
                'order_reference' => $order->order_number,
                'phone' => '081234567890',
            ])
            ->assertRedirect(route('account.orders.claim.verify.form'));

        $claim = CustomerOrderClaim::query()->firstOrFail();
        $claim->update(['code_hash' => Hash::make('123456')]);

        $this->actingAs($customer)
            ->post(route('account.orders.claim.verify'), ['code' => '123456'])
            ->assertRedirect(route('account.dashboard'));

        $this->assertSame($customer->id, $order->fresh()->user_id);
        $this->assertNotNull($claim->fresh()->verified_at);
    }

    public function test_guest_tracking_accepts_shipment_tracking_number_and_phone(): void
    {
        $order = $this->order(['customer_phone' => '081234567890']);
        Shipment::create([
            'order_id' => $order->id,
            'courier_name' => 'JNE',
            'tracking_number' => 'JNE-TRACK-001',
        ]);

        $this->post(route('tracking.lookup'), [
            'order_reference' => 'JNE-TRACK-001',
            'customer_phone' => '081234567890',
        ])->assertOk()->assertSeeText($order->order_number);
    }

    private function mockCheckoutServices(): void
    {
        config(['services.mengantar.origin_area_id' => 'origin-area']);
        $mengantar = Mockery::mock(MengantarService::class);
        $mengantar->shouldReceive('getShippingRates')->zeroOrMoreTimes()->andReturn([
            ['code' => 'jne', 'services' => [['name' => 'REG', 'cost' => 10000]]],
        ]);
        $this->app->instance(MengantarService::class, $mengantar);

        $whatsapp = Mockery::mock(WhatsAppService::class);
        $whatsapp->shouldReceive('sendOrderConfirmation')->zeroOrMoreTimes();
        $this->app->instance(WhatsAppService::class, $whatsapp);
    }

    private function checkoutData(LandingPage $landingPage, array $overrides = []): array
    {
        return array_merge([
            'landing_page_id' => $landingPage->id,
            'customer_name' => 'Customer Test',
            'customer_phone' => '081234567890',
            'customer_address' => 'Jl. Test No. 1',
            'customer_city' => 'Jakarta',
            'customer_province' => 'DKI Jakarta',
            'customer_postal_code' => '10110',
            'destination_area_id' => 'ID-AREA-1',
            'shipping_courier' => 'jne',
            'shipping_service' => 'REG',
            'shipping_cost' => 0,
            'is_cod' => true,
        ], $overrides);
    }

    private function landingPage(): LandingPage
    {
        $product = Product::create([
            'name' => 'Produk Checkout Test',
            'sell_price' => 100000,
            'cost_price' => 50000,
            'is_active' => true,
        ]);

        return LandingPage::create([
            'product_id' => $product->id,
            'slug' => 'produk-checkout-test-' . fake()->unique()->numberBetween(100, 999),
            'is_active' => true,
        ]);
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
}
