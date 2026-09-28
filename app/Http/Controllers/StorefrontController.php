<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query()
            ->where('is_active', true)
            ->whereHas('landingPages', fn ($landingPages) => $landingPages->where('is_active', true))
            ->with([
                'landingPages' => fn ($landingPages) => $landingPages
                    ->where('is_active', true)
                    ->latest('id'),
                'variants' => fn ($variants) => $variants
                    ->where('is_active', true)
                    ->orderBy('sell_price'),
            ]);

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function ($products) use ($search): void {
                $products->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('sku_supplier', 'like', "%{$search}%");
            });
        }

        match ($request->query('sort')) {
            'price_asc' => $query->orderBy('sell_price'),
            'price_desc' => $query->orderByDesc('sell_price'),
            default => $query->latest('products.created_at'),
        };

        $products = $query->paginate(12)->withQueryString();

        return view('home', [
            'products' => $products,
            'search' => $search,
            'sort' => $request->query('sort', 'latest'),
            'storeName' => Setting::get('store_name', config('app.name', 'Toko Resmi')),
        ]);
    }
}
