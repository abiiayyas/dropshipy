<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function show(string $publicToken)
    {
        $order = Order::with(['product', 'shipment', 'landingPage'])
            ->where('public_token', $publicToken)
            ->firstOrFail();

        $trackingData = null;
        if ($order && $order->shipment) {
            $mengantar = app(\App\Services\MengantarService::class);
            $trackingData = $mengantar->getTracking($order->shipment->tracking_number);
        }

        return view('tracking.show', compact('order', 'trackingData'));
    }

    public function track(Request $request)
    {
        $validated = $request->validate([
            'order_number' => 'required|string|max:50',
            'customer_phone' => 'required|string|max:20',
        ]);

        $order = Order::with(['product', 'shipment'])
            ->where('order_number', $validated['order_number'])
            ->first();

        if (! $order || ! hash_equals($this->normalizePhone($order->customer_phone), $this->normalizePhone($validated['customer_phone']))) {
            return back()->with('error', 'Order tidak ditemukan. Periksa kembali data Anda.');
        }

        $trackingData = null;
        if ($order && $order->shipment) {
            $mengantar = app(\App\Services\MengantarService::class);
            $trackingData = $mengantar->getTracking($order->shipment->tracking_number);
        }

        return view('tracking.show', compact('order', 'trackingData'));
    }

    private function normalizePhone(string $phone): string
    {
        return preg_replace('/\\D+/', '', $phone);
    }
}
