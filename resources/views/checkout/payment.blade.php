<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f3f4f6; min-height: 100vh; }
        .container { max-width: 480px; margin: 0 auto; padding: 40px 20px; }
        .card { background: white; border-radius: 16px; padding: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card h2 { font-size: 1.25rem; margin-bottom: 24px; text-align: center; }
        .detail { margin-bottom: 24px; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 0.9rem; }
        .detail-row.total { border-top: 2px solid #e5e7eb; margin-top: 8px; padding-top: 12px; font-weight: 700; font-size: 1.1rem; }
        .btn-primary { width: 100%; padding: 16px; background: #2563eb; color: white; border: none; border-radius: 12px; font-size: 1rem; font-weight: 600; cursor: pointer; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-outline { display: block; text-align: center; margin-top: 12px; color: #6b7280; text-decoration: none; font-size: 0.9rem; }
        .note { margin-top: 24px; padding: 12px; background: #fef3c7; border-radius: 8px; font-size: 0.85rem; color: #92400e; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Selesaikan Pembayaran</h2>
            <div class="detail">
                <div class="detail-row"><span>Order</span><span>#{{ $order->order_number }}</span></div>
                <div class="detail-row"><span>Produk</span><span>{{ $order->product->name }}</span></div>
                <div class="detail-row"><span>Qty</span><span>{{ $order->qty }}</span></div>
                <div class="detail-row"><span>Ongkir</span><span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
                <div class="detail-row total"><span>Total</span><span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span></div>
            </div>

            @if($snapToken)
            <button id="pay-button" class="btn-primary">Bayar Sekarang</button>
            @else
            <p class="text-center text-red-600 text-sm">Gagal membuat sesi pembayaran. Silakan coba lagi.</p>
            @endif

            <a href="{{ route('checkout.pending') }}" class="btn-outline">Nanti saja</a>
        </div>

        <div class="note">
            Pembayaran akan diproses oleh Midtrans. Pilih metode transfer bank (BCA, Mandiri, BNI) atau QRIS.
        </div>
    </div>

    @if($snapToken)
    <script>
    var payButton = document.getElementById('pay-button');
    payButton.addEventListener('click', function () {
        snap.pay('{{ $snapToken }}', {
            onSuccess: function(result) {
                window.location.href = '{{ route('checkout.finish') }}?order={{ $order->public_token }}';
            },
            onPending: function(result) {
                window.location.href = '{{ route('checkout.pending') }}';
            },
            onError: function(result) {
                window.location.href = '{{ route('checkout.error') }}';
            },
            onClose: function() {
                window.location.href = '{{ route('tracking.show', $order->public_token) }}';
            }
        });
    });
    </script>
    @endif
</body>
</html>
