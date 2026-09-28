<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\PhoneNumber;

use Illuminate\Http\Request;
class TrackingController extends Controller
{
    public function index()
    {
        return view('tracking.show');
    }

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
            'order_reference' => ['nullable', 'string', 'max:100', 'required_without:order_number'],
            'order_number' => ['nullable', 'string', 'max:100'],
            'customer_phone' => ['required', 'string', 'max:20'],
        ]);

        $reference = trim($validated['order_reference'] ?? $validated['order_number'] ?? '');
        $order = Order::with(['product', 'shipment'])
            ->where(function ($query) use ($reference): void {
                $query->where('order_number', $reference)
                    ->orWhereHas('shipment', fn ($shipments) => $shipments->where('tracking_number', $reference));
            })
            ->first();

        if (! $order || ! hash_equals(PhoneNumber::normalize($order->customer_phone), PhoneNumber::normalize($validated['customer_phone']))) {
            return back()->with('error', 'Order tidak ditemukan. Periksa kembali nomor order atau resi dan nomor WhatsApp Anda.');
        }

        $trackingData = null;
        if ($order->shipment) {
            $mengantar = app(\App\Services\MengantarService::class);
            $trackingData = $mengantar->getTracking($order->shipment->tracking_number);
        }

        return view('tracking.show', compact('order', 'trackingData'));
    }
}
