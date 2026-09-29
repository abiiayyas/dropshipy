@extends('layouts.admin')

@section('title', 'Edit Produk')

@section('content')
<div class="max-w-4xl" x-data="variantManager()">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100 mb-6">Edit Produk</h1>
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-xl shadow-sm p-6" @submit="onSubmit">
        @csrf @method('PUT')
        <div class="mb-4"><label class="block text-sm font-medium mb-2">Nama</label><input type="text" name="name" value="{{ old('name',$product->name) }}" required class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"></div>
        <div class="mb-4"><label class="block text-sm font-medium mb-2">SKU Supplier</label><input type="text" name="sku_supplier" value="{{ old('sku_supplier',$product->sku_supplier) }}" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"></div>
        <div class="mb-4"><label class="block text-sm font-medium mb-2">Deskripsi</label><textarea name="description" rows="3" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description',$product->description) }}</textarea></div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Gudang Pengiriman <span class="text-red-500">*</span></label>
            <select name="warehouse_id" required class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">-- Pilih Gudang --</option>
                @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $product->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                        {{ $warehouse->name }} ({{ $warehouse->city }})
                    </option>
                @endforeach
            </select>
            @error('warehouse_id')<p class="text-red-500 text-xs mt-1">{{$message}}</p>@enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Kurir Mengantar yang Diizinkan</label>
            <input type="text" name="allowed_couriers"
                value="{{ old('allowed_couriers', implode(',', $product->allowed_couriers ?? [])) }}"
                placeholder="Contoh: jne,jnt"
                class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
            <p class="text-xs text-gray-500 dark:text-slate-400 mt-2">
                Isi dengan kode kurir yang dikembalikan Mengantar, pisahkan dengan koma. Kosongkan untuk semua kurir.
            </p>
            @error('allowed_couriers')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div><label class="block text-sm font-medium mb-2">Harga Jual Dasar (Rp)</label><input type="number" name="sell_price" value="{{ old('sell_price',$product->sell_price) }}" required class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"></div>
            <div><label class="block text-sm font-medium mb-2">Harga Modal Dasar (Rp)</label><input type="number" name="cost_price" value="{{ old('cost_price',$product->cost_price) }}" required class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"></div>
        </div>
        <div class="mb-4"><label class="block text-sm font-medium mb-2">Foto</label><input type="file" name="images[]" multiple accept="image/*" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"></div>
        
        <div class="mb-4">
            <label class="flex items-center gap-x-2">
                <input type="checkbox" name="has_variants" value="1" x-model="hasVariants" class="rounded border-gray-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500">
                <span class="text-sm font-bold text-gray-900 dark:text-slate-100">Produk ini memiliki variasi (Warna, Ukuran, dll)</span>
            </label>
        </div>

        <!-- Variants Section -->
        <div x-show="hasVariants" x-cloak class="mt-6 mb-6 border border-gray-200 dark:border-slate-700 rounded-lg p-5 bg-gray-50 dark:bg-slate-800/50">
            <h3 class="text-lg font-semibold mb-4">Pengaturan Opsi Varian</h3>
            
            <template x-for="(opt, index) in options" :key="index">
                <div class="flex gap-3 mb-3 items-start">
                    <div class="w-1/3">
                        <label class="block text-xs font-medium mb-1">Nama Opsi (Cth: Warna)</label>
                        <input type="text" x-model="opt.name" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ukuran">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium mb-1">Nilai (Pisahkan dengan koma: S, M, L)</label>
                        <input type="text" x-model="opt.values" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="S, M, L, XL">
                    </div>
                    <div class="pt-6">
                        <button type="button" @click="removeOption(index)" class="py-2 px-3 inline-flex items-center gap-x-2 rounded-lg text-sm font-medium bg-red-100 text-red-600 hover:bg-red-200">Hapus</button>
                    </div>
                </div>
            </template>

            <div class="flex items-center gap-3 mt-4">
                <button type="button" @click="addOption" class="py-2 px-3 inline-flex items-center gap-x-2 rounded-lg text-sm font-medium bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-800/50 dark:bg-slate-800/50">+ Tambah Opsi</button>
                <button type="button" @click="generateVariants" class="py-2 px-3 inline-flex items-center gap-x-2 rounded-lg text-sm font-bold bg-blue-100 text-blue-700 hover:bg-blue-200">Buat/Perbarui Kombinasi</button>
            </div>

            <!-- Table Kombinasi -->
            <div class="mt-4 flex gap-4 items-end bg-gray-50 dark:bg-slate-800/50 p-4 rounded-lg" x-show="variants.length > 0">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Set Hrg Jual</label>
                    <input type="number" x-model="bulkSellPrice" class="py-1 px-2 border-gray-200 dark:border-slate-700 rounded text-sm focus:border-blue-500 focus:ring-blue-500 w-32">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Set Hrg Modal</label>
                    <input type="number" x-model="bulkCostPrice" class="py-1 px-2 border-gray-200 dark:border-slate-700 rounded text-sm focus:border-blue-500 focus:ring-blue-500 w-32">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Set Stok</label>
                    <input type="number" x-model="bulkStock" class="py-1 px-2 border-gray-200 dark:border-slate-700 rounded text-sm focus:border-blue-500 focus:ring-blue-500 w-24">
                </div>
                <button type="button" @click="applyBulk" class="py-1.5 px-3 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700">Terapkan ke Semua</button>
            </div>

            <div class="mt-4 overflow-x-auto" x-show="variants.length > 0">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700 bg-white dark:bg-slate-900 rounded-lg overflow-hidden border border-gray-200 dark:border-slate-700">
                    <thead class="bg-gray-100 dark:bg-slate-800">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">Kombinasi</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase">SKU</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase w-32">Hrg Jual</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase w-32">Hrg Modal</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase w-24">Stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                        <template x-for="(variant, idx) in variants" :key="idx">
                            <tr>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-800 dark:text-slate-200" x-text="Object.values(variant.combination).join(' - ')"></td>
                                <td class="px-4 py-2"><input type="text" x-model="variant.sku" class="py-1 px-2 w-full border-gray-200 dark:border-slate-700 rounded text-sm focus:border-blue-500 focus:ring-blue-500"></td>
                                <td class="px-4 py-2"><input type="number" x-model="variant.sell_price" class="py-1 px-2 w-full border-gray-200 dark:border-slate-700 rounded text-sm focus:border-blue-500 focus:ring-blue-500"></td>
                                <td class="px-4 py-2"><input type="number" x-model="variant.cost_price" class="py-1 px-2 w-full border-gray-200 dark:border-slate-700 rounded text-sm focus:border-blue-500 focus:ring-blue-500"></td>
                                <td class="px-4 py-2"><input type="number" x-model="variant.stock" class="py-1 px-2 w-full border-gray-200 dark:border-slate-700 rounded text-sm focus:border-blue-500 focus:ring-blue-500"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <input type="hidden" name="variants_data" :value="JSON.stringify(getVariantData())">
        </div>
        <!-- End Variants Section -->

        <div class="mb-4"><label class="flex items-center gap-x-2"><input type="checkbox" name="is_active" value="1" {{ $product->is_active?'checked':'' }} class="rounded border-gray-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500"><span class="text-sm">Aktif</span></label></div>
        <div class="flex gap-3"><button class="py-2 px-4 inline-flex items-center gap-x-2 rounded-lg text-sm font-medium bg-blue-600 text-white hover:bg-blue-700">Update</button><a href="{{ route('admin.products.index') }}" class="py-2 px-4 inline-flex items-center gap-x-2 rounded-lg text-sm font-medium bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:bg-gray-200">Batal</a></div>
    </form>
</div>

<script>
function variantManager() {
    return {
        hasVariants: {{ $product->has_variants ? 'true' : 'false' }},
        options: [],
        variants: [],
        bulkSellPrice: '',
        bulkCostPrice: '',
        bulkStock: '',

        init() {
            const existingOptions = @json($product->options);
            const existingVariants = @json($product->variants);

            if (existingOptions && existingOptions.length > 0) {
                this.options = existingOptions.map(opt => ({
                    id: opt.id,
                    name: opt.name,
                    values: (opt.option_values || []).map(ov => ov.value).join(', ')
                }));
            } else {
                this.options = [{ id: null, name: 'Warna', values: '' }];
            }

            if (existingVariants && existingVariants.length > 0) {
                this.variants = existingVariants.map(v => {
                    let combo = {};
                    (v.option_values || []).forEach(ov => {
                        const opt = existingOptions.find(o => o.id === ov.product_option_id);
                        if (opt) combo[opt.name] = ov.value;
                    });
                    return {
                        id: v.id,
                        sku: v.sku || '',
                        sell_price: v.sell_price,
                        cost_price: v.cost_price || 0,
                        stock: v.stock || 0,
                        combination: combo
                    };
                });
            }
        },

        addOption() {
            this.options.push({ id: null, name: '', values: '' });
        },
        removeOption(index) {
            this.options.splice(index, 1);
        },

        onSubmit(e) {
            if (this.hasVariants && this.variants.length === 0) {
                this.generateVariants();
            }
            
            // Auto generate empty SKUs
            this.variants.forEach((v, idx) => {
                if (!v.sku || v.sku.trim() === '') {
                    v.sku = 'VAR-' + Date.now() + '-' + idx;
                }
            });
        },

        applyBulk() {
            this.variants.forEach(v => {
                if (this.bulkSellPrice !== '') v.sell_price = this.bulkSellPrice;
                if (this.bulkCostPrice !== '') v.cost_price = this.bulkCostPrice;
                if (this.bulkStock !== '') v.stock = this.bulkStock;
            });
        },

        generateVariants() {
            const parsedOptions = this.options.map(o => ({
                name: o.name.trim(),
                values: o.values.split(',').map(v => v.trim()).filter(v => v !== '')
            })).filter(o => o.name !== '' && o.values.length > 0);

            if (parsedOptions.length === 0) return;

            let combinations = [{}];
            parsedOptions.forEach(opt => {
                let temp = [];
                combinations.forEach(combo => {
                    opt.values.forEach(val => {
                        let newCombo = { ...combo };
                        newCombo[opt.name] = val;
                        temp.push(newCombo);
                    });
                });
                combinations = temp;
            });

            this.variants = combinations.map(combo => {
                const existing = this.variants.find(v => JSON.stringify(v.combination) === JSON.stringify(combo));
                if (existing) return existing;
                return {
                    id: null,
                    sku: '',
                    sell_price: document.querySelector('input[name="sell_price"]').value || 0,
                    cost_price: document.querySelector('input[name="cost_price"]').value || 0,
                    stock: 0,
                    combination: combo
                };
            });
        },

        getVariantData() {
            return {
                options: this.options.map(o => ({
                    name: o.name.trim(),
                    values: o.values.split(',').map(v => v.trim()).filter(v => v !== '')
                })).filter(o => o.name !== '' && o.values.length > 0),
                variants: this.variants
            };
        }
    }
}
</script>
@endsection

