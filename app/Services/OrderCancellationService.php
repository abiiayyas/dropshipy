<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class OrderCancellationService
{
    public function cancel(Order $order, string $paymentStatus): Order
    {
        return DB::transaction(function () use ($order, $paymentStatus): Order {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($order->order_status !== 'cancelled' && $order->product_variant_id) {
                $variant = ProductVariant::query()->lockForUpdate()->find($order->product_variant_id);

                if ($variant) {
                    $variant->increment('stock', $order->qty);
                }
            }

            $order->update([
                'order_status' => 'cancelled',
                'payment_status' => $paymentStatus,
            ]);

            return $order;
        });
    }
}
