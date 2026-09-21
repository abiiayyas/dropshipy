<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::withCount('orders')
            ->withCount('landingPages')
            ->latest()
            ->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $warehouses = \App\Models\Warehouse::orderBy('name')->get();
        return view('admin.products.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku_supplier' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'sell_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_active' => 'boolean',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        if ($request->hasFile('images')) {
            $paths = [];
            $manager = new ImageManager(new Driver());
            
            foreach ($request->file('images') as $imageFile) {
                $image = $manager->read($imageFile->getRealPath());
                $image->scaleDown(width: 1000);
                
                $filename = 'products/' . Str::random(40) . '.webp';
                Storage::disk('public')->put($filename, (string) $image->toWebp(80));
                
                $paths[] = $filename;
            }
            $validated['images'] = $paths;
        }

        $validated['has_variants'] = $request->boolean('has_variants');

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $product->load(['options.optionValues', 'variants.optionValues']);
        $warehouses = \App\Models\Warehouse::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'warehouses'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku_supplier' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'sell_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_active' => 'boolean',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        if ($request->hasFile('images')) {
            $paths = $product->images ?? [];
            $manager = new ImageManager(new Driver());
            
            foreach ($request->file('images') as $imageFile) {
                $image = $manager->read($imageFile->getRealPath());
                $image->scaleDown(width: 1000);
                
                $filename = 'products/' . Str::random(40) . '.webp';
                Storage::disk('public')->put($filename, (string) $image->toWebp(80));
                
                $paths[] = $filename;
            }
            $validated['images'] = $paths;
        }

        $validated['has_variants'] = $request->boolean('has_variants');

        $product->update($validated);

        if ($product->has_variants && $request->filled('variants_data')) {
            $data = json_decode($request->variants_data, true);
            if (is_array($data)) {
                $validOptionIds = [];
                $validValueIds = [];
                $validVariantIds = [];

                // Process Options & Values
                foreach ($data['options'] ?? [] as $optData) {
                    $option = $product->options()->firstOrCreate(['name' => $optData['name']]);
                    $validOptionIds[] = $option->id;

                    foreach ($optData['values'] ?? [] as $valStr) {
                        $valStr = trim($valStr);
                        if(empty($valStr)) continue;
                        
                        $val = $option->optionValues()->firstOrCreate(['value' => $valStr]);
                        $validValueIds[] = $val->id;
                    }
                }

                // Delete removed options/values
                \App\Models\ProductOptionValue::whereIn('product_option_id', $product->options->pluck('id'))
                    ->whereNotIn('id', $validValueIds)->delete();
                $product->options()->whereNotIn('id', $validOptionIds)->delete();

                // Process Variants
                foreach ($data['variants'] ?? [] as $varData) {
                    $variant = $product->variants()->updateOrCreate(
                        ['id' => $varData['id'] ?? null],
                        [
                            'sku' => empty($varData['sku']) ? null : $varData['sku'],
                            'sell_price' => $varData['sell_price'],
                            'cost_price' => $varData['cost_price'] ?? 0,
                            'stock' => $varData['stock'] ?? 0,
                            'is_active' => true,
                        ]
                    );
                    $validVariantIds[] = $variant->id;

                    // Sync combination
                    $combinationValueIds = [];
                    foreach ($varData['combination'] ?? [] as $optName => $optValStr) {
                        $option = $product->options()->where('name', $optName)->first();
                        if ($option) {
                            $val = $option->optionValues()->where('value', $optValStr)->first();
                            if ($val) {
                                $combinationValueIds[] = $val->id;
                            }
                        }
                    }
                    $variant->optionValues()->sync($combinationValueIds);
                }

                // Delete removed variants
                $product->variants()->whereNotIn('id', $validVariantIds)->delete();
            }
        } elseif (!$product->has_variants) {
            // If unchecked, delete all variants
            $product->options()->delete(); // cascade deletes values
            $product->variants()->delete(); // cascade deletes pivot
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
