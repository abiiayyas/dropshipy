<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected string $serverKey;
    protected string $clientKey;
    protected bool $isProduction;
    protected string $baseUrl;
    protected OrderCancellationService $cancellation;

    public function __construct(OrderCancellationService $cancellation)
    {
        $this->serverKey = config('services.midtrans.server_key');
        $this->clientKey = config('services.midtrans.client_key');
        $this->isProduction = config('services.midtrans.is_production', false);
        $this->cancellation = $cancellation;
        $this->baseUrl = $this->isProduction
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';
    }

    public function createTransaction(Order $order): array
    {
        $items = [[
            'id' => $order->product->sku_supplier ?? $order->product_id,
            'price' => $order->unit_price,
            'quantity' => $order->qty,
            'name' => $order->product->name,
        ]];

        if ($order->shipping_cost > 0) {
            $items[] = [
                'id' => 'SHIPPING',
                'price' => $order->shipping_cost,
                'quantity' => 1,
                'name' => 'Ongkos Kirim - ' . ($order->shipping_courier ?? 'Kurir'),
            ];
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'phone' => $order->customer_phone,
                'billing_address' => [
                    'address' => $order->customer_address,
                    'city' => $order->customer_city,
                    'postal_code' => $order->customer_postal_code,
                ],
            ],
            'item_details' => $items,
            'callbacks' => [
                'finish' => config('app.url') . '/checkout/finish',
                'error' => config('app.url') . '/checkout/error',
                'pending' => config('app.url') . '/checkout/pending',
            ],
        ];

        $response = Http::withBasicAuth($this->serverKey, '')
            ->withOptions(['verify' => !$this->isProduction])
            ->post($this->baseUrl . '/snap/v1/transactions', $payload);

        if (!$response->successful()) {
            Log::error('Midtrans create transaction failed', [
                'response' => $response->json(),
                'order' => $order->order_number,
            ]);
            throw new \RuntimeException('Failed to create Midtrans transaction: ' . $response->body());
        }

        $data = $response->json();

        $order->update([
            'midtrans_order_id' => $data['order_id'] ?? null,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'midtrans_transaction_id' => $data['transaction_id'] ?? null,
            'payment_type' => null,
            'amount' => $order->total_amount,
            'status' => 'pending',
        ]);

        return $data;
    }

    public function verifyWebhookSignature(array $payload): bool
    {
        $signatureKey = $payload['signature_key'] ?? '';
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $serverKey = $this->serverKey;

        $computedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($computedSignature, $signatureKey);
    }

    public function handlePaymentNotification(array $payload): array
    {
        $orderNumber = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            Log::warning('Midtrans webhook: order not found', ['order_number' => $orderNumber]);
            throw new \RuntimeException('Order not found: ' . $orderNumber);
        }

        if ((int) round((float) ($payload['gross_amount'] ?? 0)) !== $order->total_amount) {
            Log::error('Midtrans webhook: gross amount mismatch', [
                'order_number' => $order->order_number,
                'gross_amount' => $payload['gross_amount'] ?? null,
            ]);
            throw new \RuntimeException('Invalid payment amount');
        }

        $payment = Payment::where('order_id', $order->id)
            ->where('midtrans_transaction_id', $payload['transaction_id'] ?? null)
            ->first();

        if ($payment) {
            $payment->update([
                'payment_type' => $payload['payment_type'] ?? null,
                'webhook_payload' => $payload,
                'status' => $transactionStatus,
            ]);
        }

        $newPaymentStatus = $this->mapPaymentStatus($transactionStatus, $fraudStatus);

        if ($newPaymentStatus === 'paid' && $order->payment_status !== 'paid') {
            if ($order->order_status === 'cancelled') {
                Log::warning('Midtrans webhook: payment received for cancelled order', [
                    'order_number' => $order->order_number,
                ]);

                return [
                    'order' => $order,
                    'event' => 'payment_updated',
                ];
            }

            $order->update([
                'payment_status' => 'paid',
                'order_status' => 'paid',
                'payment_method' => $payload['payment_type'] ?? $order->payment_method,
            ]);

            if ($payment) {
                $payment->update(['paid_at' => now()]);
            }

            return [
                'order' => $order,
                'event' => 'payment_confirmed',
            ];
        }

        if (in_array($newPaymentStatus, ['expired', 'failed'], true)) {
            $order = $this->cancellation->cancel($order, $newPaymentStatus);
        }

        return [
            'order' => $order,
            'event' => 'payment_updated',
        ];
    }

    protected function mapPaymentStatus(string $transactionStatus, ?string $fraudStatus): string
    {
        $statusMap = [
            'capture' => $fraudStatus === 'accept' ? 'paid' : 'failed',
            'settlement' => 'paid',
            'pending' => 'pending',
            'deny' => 'failed',
            'cancel' => 'failed',
            'expire' => 'expired',
            'failure' => 'failed',
            'refund' => 'refunded',
            'partial_refund' => 'refunded',
        ];

        return $statusMap[$transactionStatus] ?? 'pending';
    }
}
