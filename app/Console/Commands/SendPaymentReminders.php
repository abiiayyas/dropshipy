<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderCancellationService;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class SendPaymentReminders extends Command
{
    protected $signature = 'orders:send-payment-reminders';
    protected $description = 'Kirim reminder WA ke customer yang belum menyelesaikan pembayaran';

    public function handle(WhatsAppService $whatsapp, OrderCancellationService $cancellation): void
    {
        $this->info('Sending payment reminders...');

        $orders = Order::with('product')
            ->where('order_status', 'pending_payment')
            ->where('payment_status', 'pending')
            ->where('is_cod', false)
            ->where('reminder_count', '<', 2)
            ->where('created_at', '<', now()->subHours(3))
            ->get();

        $count = 0;

        foreach ($orders as $order) {
            $message = $this->buildReminderMessage($order);

            if ($whatsapp->sendMessage($order->customer_phone, $message)) {
                $order->increment('reminder_count');
                $count++;
            }

            if ($order->created_at->diffInHours(now()) > 24 && $order->reminder_count >= 1) {
                $cancellation->cancel($order, 'expired');
            }
        }

        $this->info("Sent {$count} reminders.");
    }

    protected function buildReminderMessage(Order $order): string
    {
        return "Halo *{$order->customer_name}*!\n\n"
            . "Order kamu #{$order->order_number} masih menunggu pembayaran nih 😊\n\n"
            . "📦 *{$order->product->name}*\n"
            . "💰 Total: Rp " . number_format($order->total_amount, 0, ',', '.') . "\n\n"
            . "Silakan selesaikan pembayaran melalui link:\n"
            . route('checkout.payment', ['publicToken' => $order->public_token]) . "\n\n"
            . "Kalau ada kendala, hubungi kami ya. Terima kasih! 🙏";
    }
}
