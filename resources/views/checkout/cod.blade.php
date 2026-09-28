<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order COD - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f3f4f6; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: white; border-radius: 16px; padding: 48px 32px; text-align: center; max-width: 480px; width: 90%; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .icon { font-size: 4rem; margin-bottom: 16px; }
        h2 { font-size: 1.5rem; margin-bottom: 8px; }
        p { color: #6b7280; margin-bottom: 16px; }
        .detail { background: #f9fafb; border-radius: 12px; padding: 16px; text-align: left; margin-bottom: 24px; }
        .detail-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 0.9rem; }
        .detail-row.total { border-top: 2px solid #e5e7eb; margin-top: 6px; padding-top: 10px; font-weight: 700; }
        .badge { display: inline-block; padding: 4px 12px; background: #fef3c7; color: #92400e; border-radius: 20px; font-size: 0.8rem; font-weight: 600; margin-bottom: 16px; }
        .btn { display: inline-block; padding: 12px 32px; background: #2563eb; color: white; border-radius: 10px; text-decoration: none; font-weight: 600; }
        .note { margin-top: 24px; font-size: 0.8rem; color: #6b7280; }
        .note { margin-top: 24px; font-size: 0.8rem; color: #6b7280; }
    </style>

    @php
        $globalPixel = \App\Models\Setting::get('global_pixel_id');
        $hasPixel = ($order && $order->landingPage) ? ($order->landingPage->pixel_id || $globalPixel) : $globalPixel;
    @endphp

    @if($hasPixel)
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    @if($globalPixel)
    fbq('init', '{{ $globalPixel }}');
    @endif
    @if($order && $order->landingPage && $order->landingPage->pixel_id)
    fbq('init', '{{ $order->landingPage->pixel_id }}');
    @endif
    fbq('track', 'PageView');
    fbq('track', 'Purchase', {value: {{ $order ? $order->total_amount : 0 }}, currency: 'IDR'});
    </script>
    @endif
</head>
<body>
    <div class="card">
        <div class="icon">📦</div>
        <span class="badge">COD · Bayar di Tempat</span>
        <h2>Order Berhasil Dibuat!</h2>
        <p>Order #{{ $order->order_number }} akan diproses. Anda akan membayar saat paket diterima.</p>

        <div class="detail">
            <div class="detail-row"><span>Produk</span><span>{{ $order->product->name }}</span></div>
            <div class="detail-row"><span>Kurir</span><span>{{ $order->shipping_courier }}</span></div>
            <div class="detail-row"><span>Pengiriman</span><span>{{ $order->customer_city }}, {{ $order->customer_province }}</span></div>
            <div class="detail-row total"><span>Total</span><span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span></div>
        </div>

        <a href="{{ route('tracking.show', $order->public_token) }}" class="btn">Cek Status Order</a>

        <div class="note">
            Tim kami akan segera memproses order Anda. Notifikasi akan dikirim via WhatsApp ke {{ $order->customer_phone }}.
        </div>
    </div>
</body>
</html>
