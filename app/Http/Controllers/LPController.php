<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Models\Order;
use App\Services\BiteshipService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class LPController extends Controller
{
    public function show(Request $request, string $slug)
    {
        LandingPage::where('slug', $slug)->where('is_active', true)->increment('visits');

        $landingPage = \Illuminate\Support\Facades\Cache::remember("lp_data_{$slug}", 3600, function () use ($slug) {
            return LandingPage::with(['product.options.optionValues', 'product.variants.optionValues'])
                ->withCount('orders')->where('slug', $slug)
                ->where('is_active', true)
                ->firstOrFail();
        });

        $utmParams = [
            'utm_source' => $request->query('utm_source'),
            'utm_medium' => $request->query('utm_medium'),
            'utm_campaign' => $request->query('utm_campaign'),
            'utm_content' => $request->query('utm_content'),
        ];

        $utmQuery = http_build_query(array_filter($utmParams));

        $template = $landingPage->template ?? 'shopee';
        $view = in_array($template, ['shopee', 'tokopedia', 'blibli', 'tiktokshop']) ? $template : 'shopee';

        return view('lp.show', compact('landingPage', 'utmQuery', 'view'));
    }

    public function getShippingOptions(Request $request, BiteshipService $biteship)
    {
        $request->validate([
            'destination_area_id' => 'nullable|string',
            'destination_city' => 'nullable|string',
            'landing_page_id' => 'required|exists:landing_pages,id',
        ]);

        $landingPage = LandingPage::with('product.warehouse')->findOrFail($request->landing_page_id);
        $originAreaId = $landingPage->product->warehouse->mengantar_area_id ?? config('services.biteship.origin_area_id', 'IDCGK101');

        $destination = [];
        if ($request->destination_area_id) {
            $destination['area_id'] = $request->destination_area_id;
        } elseif ($request->destination_city) {
            $destination['city'] = $request->destination_city;
        }
        $destination['couriers'] = ['jne', 'jnt', 'sicepat'];

        $couriers = $biteship->getShippingRates(
            ['area_id' => $originAreaId],
            $destination,
            [[
                'name' => 'Produk',
                'weight' => 1000,
                'quantity' => 1,
                'value' => 100000,
            ]]
        );

        return response()->json(['couriers' => $couriers]);
    }
    public function createOrder(Request $request, WhatsAppService $whatsapp, BiteshipService $biteship)
    {
        $validated = $request->validate([
            'landing_page_id' => 'required|exists:landing_pages,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'customer_city' => 'required|string|max:100',
            'customer_province' => 'required|string|max:100',
            'customer_postal_code' => 'required|string|max:10',
            'destination_area_id' => 'nullable|string',
            'shipping_courier' => 'required|string|max:50',
            'shipping_service' => 'nullable|string|max:50',
            'shipping_cost' => 'nullable|numeric|min:0',
            'qty' => 'nullable|integer|min:1',
            'is_cod' => 'nullable|boolean',
        ]);

        $landingPage = LandingPage::with('product.warehouse')->findOrFail($validated['landing_page_id']);

        $variant = null;
        if ($landingPage->product->has_variants) {
            $request->validate(['product_variant_id' => 'required|exists:product_variants,id']);
            $variant = \App\Models\ProductVariant::findOrFail($validated['product_variant_id']);
            if ($variant->product_id !== $landingPage->product_id) {
                abort(400, 'Invalid variant');
            }
        }

        $qty = $validated['qty'] ?? 1;
        $unitPrice = $variant ? $variant->sell_price : $landingPage->product->sell_price;

        $originAreaId = $landingPage->product->warehouse->mengantar_area_id ?? config('services.biteship.origin_area_id', 'IDCGK101');
        
        $destination = [];
        if (!empty($validated['destination_area_id'])) {
            $destination['area_id'] = $validated['destination_area_id'];
        } else {
            $destination['city'] = $validated['customer_city'];
        }
        $destination['couriers'] = [strtolower($validated['shipping_courier'])];

        $couriers = $biteship->getShippingRates(
            ['area_id' => $originAreaId],
            $destination,
            [[
                'name' => 'Produk',
                'weight' => 1000,
                'quantity' => $qty,
                'value' => $unitPrice * $qty,
            ]]
        );

        $verifiedShippingCost = null;
        foreach ($couriers as $courier) {
            if (strtolower($courier['code']) === strtolower($validated['shipping_courier'])) {
                foreach ($courier['services'] as $service) {
                    if (strtolower($service['name']) === strtolower($validated['shipping_service'] ?? 'reg')) {
                        $verifiedShippingCost = $service['cost'];
                        break 2;
                    }
                }
            }
        }

        if ($verifiedShippingCost === null) {
            abort(400, 'Kurir atau layanan pengiriman tidak valid.');
        }
        $totalAmount = ($unitPrice * $qty) + $verifiedShippingCost;
        $isCod = $request->boolean('is_cod');

        $order = \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $landingPage, $variant, $qty, $unitPrice, $verifiedShippingCost, $totalAmount, $isCod, $request) {
            if ($variant) {
                $variant = \App\Models\ProductVariant::where('id', $variant->id)->lockForUpdate()->first();
                if ($variant->stock < $qty) {
                    abort(400, 'Stok tidak mencukupi');
                }
                $variant->decrement('stock', $qty);
            }

            return Order::create([
                'landing_page_id' => $landingPage->id,
                'product_id' => $landingPage->product_id,
                'product_variant_id' => $variant ? $variant->id : null,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_address' => $validated['customer_address'],
                'customer_city' => $validated['customer_city'],
                'customer_province' => $validated['customer_province'],
                'customer_postal_code' => $validated['customer_postal_code'],
                'customer_area_id' => $validated['destination_area_id'] ?? null,
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'shipping_courier' => $validated['shipping_courier'],
                'shipping_service' => $validated['shipping_service'],
                'shipping_cost' => $verifiedShippingCost,
                'total_amount' => $totalAmount,
                'payment_method' => $isCod ? 'cod' : null,
                'is_cod' => $isCod,
                'utm_source' => $request->input('utm_source'),
                'utm_medium' => $request->input('utm_medium'),
                'utm_campaign' => $request->input('utm_campaign'),
                'utm_content' => $request->input('utm_content'),
            ]);
        });

        if ($isCod) {
            $whatsapp->sendOrderConfirmation($order, route('tracking.show', $order->order_number));
            return redirect()->route('checkout.cod', ['order' => $order->order_number]);
        }

        $paymentUrl = route('checkout.payment', ['order' => $order->order_number]);
        $whatsapp->sendOrderConfirmation($order, $paymentUrl);

        return redirect()->route('checkout.payment', ['order' => $order->order_number]);
    }
}
