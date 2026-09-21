<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\MengantarService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['product', 'landingPage', 'shipment'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('lp')) {
            $query->where('landing_page_id', $request->lp);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(25)->withQueryString();

        $statuses = ['pending_payment', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'];

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function show(Order $order)
    {
        $order->load(['product', 'landingPage', 'shipment', 'payments', 'notificationLogs']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order, WhatsAppService $whatsapp)
    {
        $request->validate([
            'order_status' => 'required|in:paid,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $order->order_status;

        $order->update([
            'order_status' => $request->order_status,
            'notes' => $request->notes ?: $order->notes,
        ]);

        if ($request->order_status === 'cancelled') {
            $order->update(['payment_status' => 'failed']);
            
            if ($oldStatus !== 'cancelled' && $order->product_variant_id) {
                $variant = \App\Models\ProductVariant::find($order->product_variant_id);
                if ($variant) {
                    $variant->increment('stock', $order->qty);
                }
            }
        } elseif ($oldStatus === 'cancelled' && $request->order_status !== 'cancelled') {
            if ($order->product_variant_id) {
                $variant = \App\Models\ProductVariant::find($order->product_variant_id);
                if ($variant) {
                    $variant->decrement('stock', $order->qty);
                }
            }
        }

        if ($request->order_status === 'delivered') {
            $whatsapp->sendDelivered($order);
        }

        return back()->with('success', 'Status order berhasil diupdate.');
    }

    public function updateTracking(Request $request, Order $order, MengantarService $mengantar, WhatsAppService $whatsapp)
    {
        $request->validate([
            'tracking_number' => 'required|string|max:100',
            'courier_name' => 'required|string|max:50',
        ]);

        $shipment = $order->shipment;

        if ($shipment) {
            $shipment->update([
                'tracking_number' => $request->tracking_number,
                'courier_name' => $request->courier_name,
                'status' => 'shipped',
                'shipped_at' => now(),
            ]);
        } else {
            $shipment = $order->shipment()->create([
                'courier_name' => $request->courier_name,
                'tracking_number' => $request->tracking_number,
                'status' => 'shipped',
                'shipped_at' => now(),
            ]);
        }

        $order->update([
            'order_status' => 'shipped',
            'shipping_courier' => $request->courier_name,
        ]);

        $whatsapp->sendTrackingNumber($order);

        return back()->with('success', 'Resi berhasil diinput, status diupdate, dan notifikasi WA dikirim ke customer.');
    }

    public function createMengantarShipment(Order $order, MengantarService $mengantar, WhatsAppService $whatsapp)
    {
        $result = $mengantar->createShipment($order);

        if (!$result || (!isset($result['ORDER_ID']) && !isset($result['cnote_no']))) {
            return back()->with('error', 'Gagal membuat shipment di Mengantar. Input resi manual jika perlu.');
        }

        $trackingNumber = $result['cnote_no'] ?? $result['ORDER_ID'];

        $shipment = $order->shipment;
        if ($shipment) {
            $shipment->update([
                'tracking_number' => $trackingNumber,
                'status' => 'shipped',
                'shipped_at' => now(),
            ]);
        } else {
            $order->shipment()->create([
                'courier_name' => $order->shipping_courier,
                'tracking_number' => $trackingNumber,
                'status' => 'shipped',
                'shipped_at' => now(),
            ]);
        }

        $order->update([
            'order_status' => 'shipped'
        ]);

        $whatsapp->sendTrackingNumber($order->fresh());

        return back()->with('success', 'Shipment berhasil dibuat di Mengantar dan notifikasi WA dikirim.');
    }

    public function markSupplierOrdered(Order $order)
    {
        $order->update([
            'supplier_order_status' => 'ordered',
            'supplier_ordered_at' => now(),
            'order_status' => 'processing',
        ]);

        return back()->with('success', 'Order ditandai sudah diteruskan ke supplier.');
    }

    public function supplierQueue()
    {
        $orders = Order::with(['product', 'landingPage'])
            ->where('payment_status', 'paid')
            ->where('order_status', 'paid')
            ->where(function ($q) {
                $q->whereNull('supplier_order_status')
                  ->orWhere('supplier_order_status', 'pending');
            })
            ->latest()
            ->paginate(25);

        return view('admin.orders.supplier-queue', compact('orders'));
    }

    public function export(Request $request)
    {
        $query = Order::with(['product', 'landingPage'])->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders_export_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Order ID', 'Order Number', 'Tanggal', 'Produk', 'Customer',
                'WhatsApp', 'Alamat', 'Kota', 'Provinsi', 'Kode Pos',
                'Kurir', 'Ongkir', 'Total', 'Metode Bayar',
                'Status Bayar', 'Status Order', 'LP Slug',
                'UTM Source', 'UTM Medium', 'UTM Campaign',
            ]);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->product->name ?? '-',
                    $order->customer_name,
                    $order->customer_phone,
                    $order->customer_address,
                    $order->customer_city,
                    $order->customer_province,
                    $order->customer_postal_code,
                    $order->shipping_courier ?? '-',
                    $order->shipping_cost,
                    $order->total_amount,
                    $order->payment_method ?? '-',
                    $order->payment_status,
                    $order->order_status,
                    $order->landingPage->slug ?? '-',
                    $order->utm_source ?? '',
                    $order->utm_medium ?? '',
                    $order->utm_campaign ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function copySupplierData(Order $order)
    {
        $data = "Nama: {$order->customer_name}\n"
            . "WA: {$order->customer_phone}\n"
            . "Alamat: {$order->customer_address}\n"
            . "Kota: {$order->customer_city}\n"
            . "Provinsi: {$order->customer_province}\n"
            . "Kode Pos: {$order->customer_postal_code}\n"
            . "Produk: {$order->product->name}\n"
            . "SKU Supplier: {$order->product->sku_supplier}\n"
            . "Jumlah: {$order->qty}\n"
            . "Kurir: {$order->shipping_courier}\n"
            . "Catatan: {$order->notes}";

        return response()->json(['data' => $data]);
    }
}
