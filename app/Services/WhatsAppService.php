<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiKey;
    protected string $deviceId;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.whatsapp.api_key');
        $this->baseUrl = config('services.whatsapp.base_url', 'https://api.fonnte.com');
    }

    public function sendMessage(string $recipient, string $message): bool
    {
        if (empty($this->apiKey)) {
            Log::info('WhatsApp service not configured, logging message', [
                'recipient' => $recipient,
                'message' => $message,
            ]);
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->post($this->baseUrl . '/send', [
            'target' => $this->normalizePhone($recipient),
            'message' => $message,
            'countryCode' => '62',
        ]);

        if (!$response->successful()) {
            Log::error('WhatsApp send failed', [
                'recipient' => $recipient,
                'response' => $response->body(),
            ]);
            return false;
        }

        return true;
    }

    public function sendOrderConfirmation(Order $order, string $paymentUrl): void
    {
        $template = $this->getTemplate('order_created');
        $message = $this->renderTemplate($template, $order, ['payment_url' => $paymentUrl]);

        $this->logAndSend($order, 'order_created', $order->customer_phone, $message);
    }

    public function sendPaymentConfirmed(Order $order): void
    {
        $template = $this->getTemplate('payment_confirmed');
        $message = $this->renderTemplate($template, $order);

        $this->logAndSend($order, 'payment_confirmed', $order->customer_phone, $message);
    }

    public function sendTrackingNumber(Order $order): void
    {
        $shipment = $order->shipment;
        if (!$shipment) {
            return;
        }

        $template = $this->getTemplate('resi_generated');
        $message = $this->renderTemplate($template, $order, [
            'courier_name' => $shipment->courier_name,
            'tracking_number' => $shipment->tracking_number,
            'tracking_url' => route('tracking.show', ['publicToken' => $order->public_token]),
        ]);

        $this->logAndSend($order, 'resi_generated', $order->customer_phone, $message);
    }

    public function sendDelivered(Order $order): void
    {
        $template = $this->getTemplate('delivered');
        $message = $this->renderTemplate($template, $order);

        $this->logAndSend($order, 'delivered', $order->customer_phone, $message);
    }

    public function sendOperatorNotification(Order $order): void
    {
        $adminPhone = config('services.whatsapp.admin_phone');
        if (empty($adminPhone)) {
            return;
        }

        $message = "🔔 *Order Baru Masuk!*\n\n"
            . "Order: #{$order->order_number}\n"
            . "Produk: {$order->product->name}\n"
            . "Customer: {$order->customer_name}\n"
            . "Total: Rp " . number_format($order->total_amount, 0, ',', '.') . "\n\n"
            . "Cek dashboard: " . config('app.url') . '/admin/orders/' . $order->id;

        $this->logAndSend($order, 'operator_notification', $adminPhone, $message);
    }

    protected function getTemplate(string $eventType): string
    {
        $templates = [
            'order_created' => "Halo *{{customer_name}}*!\n\n"
                . "Order kamu #{{order_number}} sudah kami terima. Berikut detailnya:\n\n"
                . "📦 *{{product_name}}*\n"
                . "💰 Total: Rp {{total_amount}}\n\n"
                . "Silakan selesaikan pembayaran melalui link berikut:\n"
                . "{{payment_url}}\n\n"
                . "Terima kasih! 🙏",

            'payment_confirmed' => "Halo *{{customer_name}}*!\n\n"
                . "Pembayaran untuk order #{{order_number}} sudah kami terima ✅\n"
                . "Order kamu sedang kami proses dan akan segera dikirim.\n\n"
                . "Kami akan mengirimkan nomor resi setelah pengiriman.\n\n"
                . "Terima kasih! 🙏",

            'resi_generated' => "Halo *{{customer_name}}*!\n\n"
                . "Order #{{order_number}} sudah dikirim! 📦\n\n"
                . "Kurir: *{{courier_name}}*\n"
                . "No. Resi: *{{tracking_number}}*\n\n"
                . "Cek status pengiriman:\n"
                . "{{tracking_url}}\n\n"
                . "Terima kasih! 🙏",

            'delivered' => "Halo *{{customer_name}}*!\n\n"
                . "Order #{{order_number}} sudah sampai di tujuan 🎉\n\n"
                . "Terima kasih sudah berbelanja! Jika ada pertanyaan, silakan hubungi kami.",
        ];

        return $templates[$eventType] ?? '';
    }

    protected function renderTemplate(string $template, Order $order, array $extra = []): string
    {
        $replacements = array_merge([
            'customer_name' => $order->customer_name,
            'order_number' => $order->order_number,
            'product_name' => $order->product->name,
            'total_amount' => number_format($order->total_amount, 0, ',', '.'),
        ], $extra);

        foreach ($replacements as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }

        return $template;
    }

    protected function logAndSend(Order $order, string $eventType, string $recipient, string $message): void
    {
        $sent = $this->sendMessage($recipient, $message);

        NotificationLog::create([
            'order_id' => $order->id,
            'channel' => 'whatsapp',
            'event_type' => $eventType,
            'recipient' => $recipient,
            'message' => $message,
            'status' => $sent ? 'sent' : 'failed',
            'sent_at' => $sent ? now() : null,
        ]);
    }

    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '62')) {
            // already in format
        } else {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
