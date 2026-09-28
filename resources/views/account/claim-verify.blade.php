@extends('account.layout', ['title' => 'Verifikasi order'])

@section('content')
<div class="mx-auto mt-8 max-w-md rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm sm:p-8">
    <p class="text-sm font-medium text-blue-700">Verifikasi WhatsApp</p>
    <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-950">Masukkan kode</h1>
    <p class="mt-3 text-sm leading-6 text-slate-500">Kode 6 digit sudah dikirim ke nomor WhatsApp pada order. Kode berlaku selama 10 menit.</p>

    <form method="POST" action="{{ route('account.orders.claim.verify') }}" class="mt-6 space-y-4 text-left">
        @csrf
        <label for="code" class="block text-sm font-medium text-slate-700">Kode verifikasi</label>
        <input id="code" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" required class="block w-full rounded-xl border-slate-300 text-center text-2xl tracking-[.45em] focus:border-blue-500 focus:ring-blue-500" placeholder="000000">
        <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">Verifikasi order</button>
    </form>

    <a href="{{ route('account.orders.claim.form') }}" class="mt-5 inline-block text-sm font-semibold text-slate-600 hover:text-blue-700">Minta kode baru</a>
</div>
@endsection
