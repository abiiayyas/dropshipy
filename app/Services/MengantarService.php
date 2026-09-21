<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MengantarService
{
    protected ?string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.mengantar.api_key');
        $this->baseUrl = config('services.mengantar.base_url', 'https://app.mengantar.com');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    protected function request(string $method, string $path, array $data = []): ?array
    {
        if (!$this->isConfigured()) {
            Log::info('Mengantar: API key not configured');
            return null;
        }

        $url = rtrim($this->baseUrl, '/') . '/api/public/' . $this->apiKey . '/' . ltrim($path, '/');
        
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ]);

        if (strtoupper($method) === 'GET') {
            $response = $response->get($url, $data);
        } else {
            // Mengantar API documentation uses form-data (-F) in cURL examples for POST
            $response = $response->asMultipart()->post($url, $data);
        }

        if (!$response->successful()) {
            Log::error('Mengantar API error', [
                'method' => $method,
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        return $response->json();
    }

    public function getShippingRates(array $origin, array $destination, array $items): array
    {
        if (empty($origin['area_id']) || empty($destination['area_id'])) {
            return [];
        }

        $totalWeight = array_sum(array_column($items, 'weight'));
        $totalWeightKg = ceil($totalWeight / 1000);
        if ($totalWeightKg < 1) $totalWeightKg = 1;

        $totalValue = array_sum(array_column($items, 'value'));

        $payload = [
            'origin_id' => $origin['area_id'],
            'destination_id' => $destination['area_id'],
            'courier' => 'all',
            'weight' => $totalWeightKg,
            'COD_AMOUNT' => $totalValue, 
        ];

        $result = $this->request('GET', 'order/estimate', $payload);

        if (!$result || !isset($result['data']) || empty($result['data'])) {
            return [];
        }

        return $this->formatRates($result['data']);
    }

    public function createShipment(Order $order): ?array
    {
        $warehouse = $order->product->warehouse ?? null;

        if (!$warehouse || !$warehouse->mengantar_address_id) {
            Log::error('Mengantar createShipment failed: Warehouse Mengantar Address ID missing', ['order_id' => $order->id]);
            return null;
        }

        // Use exact shipping courier code stored in DB (e.g. SiCepat, Sap)
        $courier = $order->shipping_courier ?: 'JNE';

        $pickup = [
            'type' => 'dropOff',
            'address_id' => $warehouse->mengantar_address_id,
        ];

        $productName = $order->product->name;
        if ($order->product_variant_id) {
            $variant = \App\Models\ProductVariant::find($order->product_variant_id);
            if ($variant) $productName .= ' - ' . $variant->name;
        }

        $goodsValue = $order->unit_price * $order->qty;
        $orderData = [
            'customerAddress' => $order->customer_address,
            'customerName' => $order->customer_name,
            'customerAddressDataId' => $order->customer_area_id,
            'customerPhone' => $order->customer_phone,
            'parcelContent' => $productName,
            'weight' => max(1, $order->qty),
            'quantity' => $order->qty,
        ];

        if ($order->is_cod) {
            $orderData['COD'] = $order->total_amount;
        } else {
            $orderData['goodsValue'] = $goodsValue;
        }

        $payload = [
            'courier' => $courier,
            'pickup' => json_encode($pickup),
            'orders' => json_encode([$orderData])
        ];

        $result = $this->request('POST', 'order', $payload);

        if ($result && isset($result['data'][0])) {
            return $result['data'][0];
        }

        return null;
    }

    public function getTracking(string $trackingNumber): ?array
    {
        $result = $this->request('GET', 'order', [
            'tracking_id' => $trackingNumber
        ]);

        if ($result && isset($result['data'][0])) {
            return $result['data'][0];
        }

        return null;
    }

    public function syncTrackingForActiveShipments(): void
    {
        $shipments = Shipment::whereNotIn('status', ['delivered', 'returned', 'cancelled'])
            ->whereNotNull('tracking_number')
            ->get();

        $whatsapp = app(WhatsAppService::class);

        foreach ($shipments as $shipment) {
            try {
                $trackingData = $this->getTracking($shipment->tracking_number);
                
                if ($trackingData && isset($trackingData['status'])) {
                    $status = strtolower($trackingData['status']);
                    
                    if ($status !== strtolower($shipment->status)) {
                        $shipment->status = $trackingData['status'];
                        
                        if ($status === 'delivered') {
                            $shipment->delivered_at = now();
                            $shipment->save();

                            $order = $shipment->order;
                            if ($order && $order->order_status !== 'delivered') {
                                $order->order_status = 'delivered';
                                $order->save();
                                
                                $whatsapp->sendDelivered($order);
                            }
                        } else {
                            $shipment->save();
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to sync tracking for shipment ' . $shipment->id, [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    protected function formatRates(array $pricing): array
    {
        $grouped = [];

        foreach ($pricing as $courierCode => $rate) {
            // Skip unsupported or empty prices
            if (isset($rate['unsupported']) && $rate['unsupported']) continue;
            
            $code = $courierCode; // Keep exact case like SiCepat, JNE, JT
            if (!isset($grouped[$code])) {
                $grouped[$code] = [
                    'code' => $code,
                    'name' => strtoupper($courierCode),
                    'supports_cod' => !(isset($rate['unsupported_cod']) && $rate['unsupported_cod']),
                    'services' => [],
                ];
            }
            
            $grouped[$code]['services'][] = [
                'name' => 'REG',
                'description' => 'Reguler (' . ($rate['estimatedDate'] ?? '2-4 hari') . ')',
                'cost' => $rate['estimatedPrice'] ?? $rate['price'] ?? 0,
                'etd' => $rate['estimatedDate'] ?? '2-4 hari',
            ];
        }

        return array_values($grouped);
    }

    protected function fallbackRates(): array
    {
        return [
            ['code' => 'jne', 'name' => 'JNE', 'services' => [
                ['name' => 'REG', 'description' => 'Reguler', 'cost' => 9000, 'etd' => '2-3 hari'],
                ['name' => 'YES', 'description' => 'Yakin Esok Sampai', 'cost' => 25000, 'etd' => '1 hari'],
            ]],
            ['code' => 'jnt', 'name' => 'J&T Express', 'services' => [
                ['name' => 'EZ', 'description' => 'Reguler', 'cost' => 9000, 'etd' => '2-3 hari'],
            ]],
            ['code' => 'sicepat', 'name' => 'SiCepat', 'services' => [
                ['name' => 'REG', 'description' => 'Reguler', 'cost' => 9000, 'etd' => '2-3 hari'],
                ['name' => 'BEST', 'description' => 'Besok Sampai', 'cost' => 22000, 'etd' => '1 hari'],
            ]],
        ];
    }

    public function createAddress(string $areaId, string $address, string $phone, string $name, string $pic): ?string
    {
        $payload = [
            'PICKUP_AUTOFILL' => $areaId,
            'PICKUP_ADDRESS' => $address,
            'PICKUP_PIC_PHONE' => $phone,
            'PICKUP_PIC' => $pic,
            'PICKUP_NAME' => $name,
        ];

        $result = $this->request('POST', 'address', $payload);

        if ($result && isset($result['data']['_id'])) {
            return $result['data']['_id'];
        }

        return null;
    }

    public function searchArea(string $query): array
    {
        $result = $this->request('GET', 'address/search', [
            'keyword' => $query
        ]);

        if (!$result || !isset($result['data'])) {
            return [];
        }

        // Map to Biteship response format so frontend doesn't break
        return array_map(function ($area) {
            return [
                'id' => $area['_id'] ?? '',
                'name' => ($area['SUBDISTRICT_NAME'] ?? '') . ', ' . ($area['DISTRICT_NAME'] ?? ''),
                'administrative_division_level_1_name' => $area['PROVINCE_NAME'] ?? '',
                'administrative_division_level_2_name' => $area['CITY_NAME'] ?? '',
                'postal_code' => $area['ZIP_CODE'] ?? '',
            ];
        }, $result['data']);
    }
}
