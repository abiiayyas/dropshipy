<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Models\Order;
use App\Services\MidtransService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected MidtransService $midtrans;
    protected WhatsAppService $whatsapp;

    public function __construct(MidtransService $midtrans, WhatsAppService $whatsapp)
    {
        $this->midtrans = $midtrans;
        $this->whatsapp = $whatsapp;
    }

    public function showForm(Request $request, string $slug)
    {
        $landingPage = LandingPage::with(['product.options.optionValues', 'product.variants.optionValues'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->whereHas('product', fn ($query) => $query->where('is_active', true))
            ->firstOrFail();

        $variant = null;
        if ($landingPage->product->has_variants) {
            $variantId = $request->query('variant_id');
            if (!$variantId) {
                return redirect()->route('lp.show', $slug)->with('error', 'Silakan pilih varian produk terlebih dahulu.');
            }
            $variant = $landingPage->product->variants()
                ->whereKey($variantId)
                ->where('is_active', true)
                ->firstOrFail();
        }

        $utmParams = [
            'utm_source' => $request->query('utm_source'),
            'utm_medium' => $request->query('utm_medium'),
            'utm_campaign' => $request->query('utm_campaign'),
            'utm_content' => $request->query('utm_content'),
        ];

        $utmQuery = http_build_query(array_filter($utmParams));

        return view('checkout.form', compact('landingPage', 'utmQuery', 'variant'));
    }

    public function payment(string $publicToken)
    {
        $order = Order::with(['product', 'landingPage', 'productVariant.optionValues'])
            ->where('public_token', $publicToken)
            ->firstOrFail();

        if ($order->is_cod) {
            return redirect()->route('checkout.cod', ['publicToken' => $order->public_token]);
        }

        if ($order->order_status === 'cancelled') {
            abort(410, 'Order sudah dibatalkan.');
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.finish')
                ->with('order_token', $order->public_token);
        }

        if (in_array($order->order_status, ['processing', 'shipped', 'delivered'])) {
            return redirect()->route('checkout.finish')
                ->with('order_token', $order->public_token);
        }

        try {
            $snapData = $this->midtrans->createTransaction($order);
            $snapToken = $snapData['token'] ?? null;
        } catch (\Exception $e) {
            return view('checkout.error', [
                'message' => 'Gagal membuat transaksi pembayaran. Silakan coba lagi.',
            ]);
        }

        return view('checkout.payment', compact('order', 'snapToken'));
    }

    public function finish(Request $request)
    {
        $publicToken = $request->session()->get('order_token')
            ?? $request->query('order');

        $order = null;
        if ($publicToken) {
            $order = Order::with(['product', 'landingPage', 'productVariant.optionValues'])
                ->where('public_token', $publicToken)
                ->first();
        }

        return view('checkout.finish', compact('order'));
    }

    public function error()
    {
        return view('checkout.error', [
            'message' => 'Terjadi kesalahan saat memproses pembayaran.',
        ]);
    }

    public function pending()
    {
        return view('checkout.pending');
    }

    public function cod(string $publicToken)
    {
        $order = Order::with(['product', 'landingPage', 'productVariant.optionValues'])
            ->where('public_token', $publicToken)
            ->firstOrFail();

        return view('checkout.cod', compact('order'));
    }
}
