<?php

namespace Tests\Feature;

use App\Models\LandingPage;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LandingPageWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_landing_page_can_save_a_draft(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->product('Draft Product');

        $response = $this->actingAs($admin)->post(route('admin.landing-pages.store'), [
            'product_id' => $product->id,
            'slug' => 'draft-product',
            'template' => 'shopee',
            'is_active' => '0',
        ]);

        $response->assertRedirect(route('admin.landing-pages.index'));
        $this->assertDatabaseHas('landing_pages', [
            'slug' => 'draft-product',
            'is_active' => false,
        ]);
    }

    public function test_edit_keeps_inactive_product_available_and_appends_slider_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->product('Inactive Product', ['is_active' => false]);
        $oldImage = 'landing-pages/slider/old.webp';
        Storage::disk('public')->put($oldImage, 'old image');
        $landingPage = LandingPage::create([
            'product_id' => $product->id,
            'slug' => 'inactive-product',
            'image_slider' => json_encode([$oldImage]),
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.landing-pages.edit', $landingPage))
            ->assertOk()
            ->assertSeeText('Inactive Product');

        $response = $this->actingAs($admin)->put(
            route('admin.landing-pages.update', $landingPage),
            [
                'product_id' => $product->id,
                'slug' => 'inactive-product',
                'template' => 'shopee',
                'slider_images' => [UploadedFile::fake()->image('new.jpg', 20, 20)],
                'keep_slider_images' => '1',
            ]
        );

        $response->assertRedirect(route('admin.landing-pages.index'));
        $landingPage->refresh();
        $images = json_decode($landingPage->image_slider, true);

        $this->assertCount(2, $images);
        $this->assertContains($oldImage, $images);
        Storage::disk('public')->assertExists($oldImage);
        $this->assertFalse($landingPage->is_active);
    }

    private function product(string $name, array $attributes = []): Product
    {
        $warehouse = Warehouse::create(['name' => 'Gudang Landing Test']);

        return Product::create(array_merge([
            'name' => $name,
            'sell_price' => 100000,
            'cost_price' => 50000,
            'warehouse_id' => $warehouse->id,
            'is_active' => true,
        ], $attributes));
    }
}
