@extends('account.layout', ['title' => 'Detail order'])

@section('content')
@php
    $statusLabels = [
        'pending_payment' => 'Menunggu pembayaran',
        'paid' => 'Sudah dibayar',
        'processing' => 'Sedang diproses',
        'shipped' => 'Dikirim',
        'delivered' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];
@endphp

<a href="{{ route('account.dashboard') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-900">← Kembali ke akun</a>
<div class="mt-4 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <p class="text-sm text-slate-500">Order #{{ $order->order_number }}</p>
        <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-950">Detail order</h1>
    </div>
    <span class="inline-flex w-fit rounded-lg bg-blue-50 px-3 py-1.5 text-sm font-semibold text-blue-700">{{ $statusLabels[$order->order_status] ?? ucfirst(str_replace('_', ' ', $order->order_status)) }}</span>
</div>

<div class="mt-8 grid gap-6 lg:grid-cols-[1.2fr_.8fr]">
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-bold text-slate-950">Produk</h2>
        <div class="mt-5 flex gap-4">
            <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-center text-xs font-semibold text-slate-400">Produk</div>
            <div>
                <h3 class="font-bold text-slate-900">{{ $order->product->name ?? 'Produk' }}</h3>
                <p class="mt-1 text-sm text-slate-500">{{ $order->qty }} item · Rp {{ number_format($order->unit_price, 0, ',', '.') }} per item</p>
                @if($order->productVariant)
                    <p class="mt-1 text-sm text-slate-500">Varian: {{ $order->productVariant->optionValues->pluck('value')->join(', ') }}</p>
                @endif
            </div>
        </div>
        <dl class="mt-6 divide-y divide-slate-100 border-t border-slate-100 text-sm">
            <div class="flex justify-between gap-4 py-3"><dt class="text-slate-500">Subtotal</dt><dd class="font-semibold text-slate-900">Rp {{ number_format($order->unit_price * $order->qty, 0, ',', '.') }}</dd></div>
            <div class="flex justify-between gap-4 py-3"><dt class="text-slate-500">Pengiriman</dt><dd class="font-semibold text-slate-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</dd></div>
            <div class="flex justify-between gap-4 py-3"><dt class="font-bold text-slate-900">Total</dt><dd class="font-black text-slate-950">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</dd></div>
        </dl>
    </section>

    <aside class="space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-950">Pengiriman</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div><dt class="text-slate-500">Penerima</dt><dd class="mt-1 font-semibold text-slate-900">{{ $order->customer_name }}</dd></div>
                <div><dt class="text-slate-500">Alamat</dt><dd class="mt-1 leading-6 text-slate-700">{{ $order->customer_address }}, {{ $order->customer_city }}, {{ $order->customer_province }} {{ $order->customer_postal_code }}</dd></div>
                @if($order->shipment?->tracking_number)
                    <div><dt class="text-slate-500">Resi</dt><dd class="mt-1 font-bold text-blue-700">{{ $order->shipment->tracking_number }}</dd></div>
                @endif
            </dl>
        </section>
        <a href="{{ route('tracking.show', $order->public_token) }}" class="flex items-center justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">Lihat status pengiriman</a>
    </aside>
</div>
@endsection
