@extends('layouts.admin')

@section('title', 'Tambah Produk')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100 mb-6">Tambah Produk</h1>
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-xl shadow-sm p-6">
        @csrf
        <div class="mb-4"><label class="block text-sm font-medium mb-2">Nama Produk</label><input type="text" name="name" value="{{ old('name') }}" required class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">@error('name')<p class="text-red-500 text-xs mt-1">{{$message}}</p>@enderror</div>
        <div class="mb-4"><label class="block text-sm font-medium mb-2">SKU Supplier</label><input type="text" name="sku_supplier" value="{{ old('sku_supplier') }}" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"></div>
        <div class="mb-4"><label class="block text-sm font-medium mb-2">Deskripsi</label><textarea name="description" rows="3" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea></div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Gudang Pengiriman <span class="text-red-500">*</span></label>
            <select name="warehouse_id" required class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">-- Pilih Gudang --</option>
                @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                        {{ $warehouse->name }} ({{ $warehouse->city }})
                    </option>
                @endforeach
            </select>
            @error('warehouse_id')<p class="text-red-500 text-xs mt-1">{{$message}}</p>@enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Kurir Mengantar yang Diizinkan</label>
            <input type="text" name="allowed_couriers" value="{{ old('allowed_couriers') }}"
                placeholder="Contoh: jne,jnt"
                class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
            <p class="text-xs text-gray-500 dark:text-slate-400 mt-2">
                Isi dengan kode kurir yang dikembalikan Mengantar, pisahkan dengan koma. Kosongkan untuk semua kurir.
            </p>
            @error('allowed_couriers')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div><label class="block text-sm font-medium mb-2">Harga Jual (Rp)</label><input type="number" name="sell_price" value="{{ old('sell_price') }}" required class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"></div>
            <div><label class="block text-sm font-medium mb-2">Harga Modal (Rp)</label><input type="number" name="cost_price" value="{{ old('cost_price') }}" required class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"></div>
        </div>
        <div class="mb-4"><label class="block text-sm font-medium mb-2">Foto</label><input type="file" name="images[]" multiple accept="image/*" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"></div>
        <div class="mb-4">
            <label class="flex items-center gap-x-2">
                <input type="checkbox" name="has_variants" value="1" class="rounded border-gray-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500">
                <span class="text-sm font-medium text-gray-900 dark:text-slate-100">Produk ini memiliki variasi (Warna, Ukuran, dll)</span>
            </label>
        </div>
        <div class="mb-4"><label class="flex items-center gap-x-2"><input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500"><span class="text-sm">Aktif</span></label></div>
        <div class="flex gap-3"><button class="py-2 px-4 inline-flex items-center gap-x-2 rounded-lg text-sm font-medium bg-blue-600 text-white hover:bg-blue-700">Simpan</button><a href="{{ route('admin.products.index') }}" class="py-2 px-4 inline-flex items-center gap-x-2 rounded-lg text-sm font-medium bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:bg-gray-200">Batal</a></div>
    </form>
</div>
@endsection

