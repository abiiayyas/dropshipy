<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_a_webp_product_image(): void
    {
        if (! function_exists('imagewebp')) {
            $this->markTestSkipped('PHP GD WebP encoding is not available in the test runtime.');
        }

        Storage::fake('public');

        $warehouse = Warehouse::create(['name' => 'Gudang Upload Test']);
        $admin = User::factory()->create(['role' => 'admin']);
        $image = $this->webpUpload();

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Produk WebP Test',
            'sell_price' => 100000,
            'cost_price' => 50000,
            'warehouse_id' => $warehouse->id,
            'is_active' => true,
            'images' => [$image],
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::query()->latest('id')->firstOrFail();
        $this->assertCount(1, $product->images);
        Storage::disk('public')->assertExists($product->images[0]);
    }

    private function webpUpload(): UploadedFile
    {
        $image = imagecreatetruecolor(2, 2);
        $color = imagecolorallocate($image, 40, 120, 220);
        imagefill($image, 0, 0, $color);

        ob_start();
        imagewebp($image, null, 80);
        $contents = ob_get_clean();
        imagedestroy($image);

        return UploadedFile::fake()->createWithContent('product.webp', $contents);
    }
}
