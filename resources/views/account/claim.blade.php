@extends('account.layout', ['title' => 'Tambahkan order guest'])

@section('content')
<a href="{{ route('account.dashboard') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-900">← Kembali ke akun</a>
<div class="mx-auto mt-6 max-w-xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <p class="text-sm font-medium text-blue-700">Order guest</p>
    <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-950">Tambahkan order ke akun</h1>
    <p class="mt-3 text-sm leading-6 text-slate-500">Masukkan nomor order atau resi dan nomor WhatsApp yang digunakan saat checkout. Kode verifikasi akan dikirim ke WhatsApp pada order.</p>

    <form method="POST" action="{{ route('account.orders.claim.store') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label for="order_reference" class="block text-sm font-medium text-slate-700">Nomor order atau resi</label>
            <input id="order_reference" name="order_reference" value="{{ old('order_reference') }}" required class="mt-1 block w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="ORD-... atau nomor resi">
        </div>
        <div>
            <label for="phone" class="block text-sm font-medium text-slate-700">Nomor WhatsApp saat checkout</label>
            <input id="phone" name="phone" type="tel" value="{{ old('phone', auth()->user()->phone) }}" required class="mt-1 block w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="0812...">
        </div>
        <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">Kirim kode verifikasi</button>
    </form>
</div>
@endsection
