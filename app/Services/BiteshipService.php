<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiteshipService
{
    protected ?string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.biteship.api_key');
        $this->baseUrl = config('services.biteship.base_url', 'https://api.biteship.com');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    protected function request(string $method, string $path, array $data = []): ?array
    {
        if (!$this->isConfigured()) {
            Log::info('Biteship: API key not configured');
            return null;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->$method($this->baseUrl . $path, $data);

        if (!$response->successful()) {
            Log::error('Biteship API error', [
                'method' => $method,
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        return $response->json();
    }

    public function getShippingRates(array $origin, array $destination, array $items): array
    {
        $payload = [
            'origin_area_id' => $origin['area_id'] ?? 'IDCGK101',
            'origin_latitude' => $origin['latitude'] ?? -6.229728,
            'origin_longitude' => $origin['longitude'] ?? 106.689431,
            'destination_area_id' => $destination['area_id'] ?? null,
            'destination_latitude' => $destination['latitude'] ?? null,
            'destination_longitude' => $destination['longitude'] ?? null,
            'couriers' => implode(',', $destination['couriers'] ?? ['jne', 'jnt', 'sicepat']),
            'items' => $items,
        ];

        $result = $this->request('post', '/v1/rates/couriers', $payload);

        if (!$result || !isset($result['pricing'])) {
            return [];
        }

        return $this->formatRates($result['pricing']);
    }

    public function createShipment(Order $order): ?array
    {
        $payload = [
            'origin_contact_name' => config('app.name', 'Diginiaga'),
            'origin_contact_phone' => config('services.biteship.origin_phone', '08123456789'),
            'origin_address' => $order->product->warehouse->address ?? config('services.biteship.origin_address', 'Jakarta'),
            'origin_area_id' => $order->product->warehouse->mengantar_area_id ?? config('services.biteship.origin_area_id', 'IDCGK101'),
            'destination_contact_name' => $order->customer_name,
            'destination_contact_phone' => $order->customer_phone,
            'destination_address' => $order->customer_address,
            'destination_postal_code' => $order->customer_postal_code,
            'courier_company' => strtolower($order->shipping_courier) ?: 'jne',
            'courier_type' => $order->shipping_service ?: 'reg',
            'delivery_type' => 'now',
            'items' => [[
                'name' => $order->product->name,
                'description' => $order->product->description ?? $order->product->name,
                'value' => $order->unit_price,
                'quantity' => $order->qty,
                'weight' => 1000,
            ]],
        ];

        $result = $this->request('post', '/v1/orders', $payload);

        if (!$result || !isset($result['id'])) {
            Log::error('Biteship create order failed', ['order' => $order->order_number]);
            return null;
        }

        Shipment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'biteship_order_id' => $result['id'],
                'courier_name' => $order->shipping_courier,
                'tracking_number' => $result['courier']['waybill_id'] ?? null,
                'status' => $result['status'] ?? 'confirmed',
                'status_history' => [['status' => $result['status'] ?? 'confirmed', 'note' => 'Order created', 'updated_at' => now()->toIso8601String()]],
                'shipped_at' => now(),
            ]
        );

        $order->update(['order_status' => 'shipped']);

        return $result;
    }

    public function getTracking(string $biteshipOrderId): ?array
    {
        $result = $this->request('get', '/v1/orders/' . $biteshipOrderId);

        if (!$result) {
            return null;
        }

        $shipment = Shipment::where('biteship_order_id', $biteshipOrderId)->first();
        if ($shipment) {
            $shipment->update([
                'status' => $result['status'] ?? $shipment->status,
                'status_history' => $result['history'] ?? [],
                'tracking_number' => $result['courier']['waybill_id'] ?? $shipment->tracking_number,
                'delivered_at' => ($result['status'] === 'delivered') ? now() : $shipment->delivered_at,
            ]);

            $this->syncOrderStatus($shipment);
        }

        return $result;
    }

    public function syncTrackingForActiveShipments(): void
    {
        $shipments = Shipment::whereNotNull('biteship_order_id')
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->get();

        foreach ($shipments as $shipment) {
            try {
                $this->getTracking($shipment->biteship_order_id);
            } catch (\Exception $e) {
                Log::error('Biteship tracking sync error', [
                    'shipment_id' => $shipment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    protected function syncOrderStatus(Shipment $shipment): void
    {
        $order = $shipment->order;
        if (!$order) return;

        $statusMap = [
            'confirmed' => 'shipped',
            'allocated' => 'shipped',
            'picking_up' => 'shipped',
            'picked' => 'shipped',
            'dropping_off' => 'shipped',
            'in_transit' => 'shipped',
            'delivered' => 'delivered',
            'cancelled' => 'cancelled',
        ];

        $newOrderStatus = $statusMap[$shipment->status] ?? null;
        if ($newOrderStatus && $order->order_status !== $newOrderStatus) {
            $order->update(['order_status' => $newOrderStatus]);

            if ($newOrderStatus === 'delivered') {
                app(WhatsAppService::class)->sendDelivered($order);
            }
        }
    }

    protected function formatRates(array $pricing): array
    {
        $couriers = [];
        $grouped = [];

        foreach ($pricing as $rate) {
            $courierCode = $rate['courier_code'] ?? $rate['courier_name'] ?? 'unknown';
            if (!isset($grouped[$courierCode])) {
                $grouped[$courierCode] = [
                    'code' => $courierCode,
                    'name' => strtoupper($courierCode),
                    'services' => [],
                ];
            }
            $grouped[$courierCode]['services'][] = [
                'name' => $rate['courier_service_name'] ?? 'REG',
                'description' => $rate['description'] ?? $rate['courier_service_name'] ?? 'Reguler',
                'cost' => $rate['price'] ?? 0,
                'etd' => $rate['duration'] ?? '2-3 hari',
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

    public function searchArea(string $query): array
    {
        $result = $this->request('get', '/v1/maps/areas', [
            'countries' => 'ID',
            'input' => $query,
            'type' => 'single'
        ]);

        return $result['areas'] ?? [];
    }
}
