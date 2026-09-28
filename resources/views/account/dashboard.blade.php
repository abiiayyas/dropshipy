@extends('account.layout', ['title' => 'Akun saya'])

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

<div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
    <div>
        <p class="text-sm font-medium text-blue-700">Akun pembeli</p>
        <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-950">Halo, {{ auth()->user()->name }}</h1>
        <p class="mt-2 text-sm text-slate-500">Lihat order dan status pengirimanmu di sini.</p>
    </div>
    <a href="{{ route('account.orders.claim.form') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:border-blue-300 hover:text-blue-700">Tambahkan order guest</a>
</div>

<section class="mt-8">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-950">Order saya</h2>
        <span class="text-sm text-slate-500">{{ $orders->total() }} order</span>
    </div>

    @if($orders->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
            <h3 class="text-lg font-bold text-slate-900">Belum ada order di akun ini</h3>
            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">Mulai belanja atau tambahkan order guest menggunakan nomor order dan nomor WhatsApp.</p>
            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <a href="{{ route('storefront.index') }}#produk" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Belanja sekarang</a>
                <a href="{{ route('account.orders.claim.form') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:border-blue-300 hover:text-blue-700">Tambahkan order</a>
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                <span>Order #{{ $order->order_number }}</span>
                                <span aria-hidden="true">•</span>
                                <time datetime="{{ $order->created_at->toIso8601String() }}">{{ $order->created_at->format('d M Y, H:i') }}</time>
                            </div>
                            <h3 class="mt-2 truncate font-bold text-slate-950">{{ $order->product->name ?? 'Produk' }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $order->qty }} item · Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        </div>
                        <span class="inline-flex w-fit rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">{{ $statusLabels[$order->order_status] ?? ucfirst(str_replace('_', ' ', $order->order_status)) }}</span>
                    </div>
                    @if($order->shipment?->tracking_number)
                        <p class="mt-4 rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-600">Resi: <strong class="text-slate-900">{{ $order->shipment->tracking_number }}</strong></p>
                    @endif
                    <div class="mt-4 flex flex-wrap gap-3 border-t border-slate-100 pt-4">
                        <a href="{{ route('account.orders.show', $order->id) }}" class="text-sm font-semibold text-blue-700 hover:text-blue-900">Lihat detail</a>
                        <a href="{{ route('tracking.show', $order->public_token) }}" class="text-sm font-semibold text-slate-600 hover:text-blue-700">Lacak status</a>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</section>
@endsection
