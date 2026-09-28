<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $storeName }}. Temukan produk pilihan dan checkout dengan pembayaran aman serta pengiriman ke seluruh Indonesia.">
    <meta property="og:title" content="{{ $storeName }} | Belanja produk pilihan">
    <meta property="og:description" content="Produk pilihan, checkout mudah, dan pengiriman yang bisa dilacak.">
    <title>{{ $storeName }} | Belanja produk pilihan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { color-scheme: light; }
        html { scroll-behavior: smooth; }
        body { background: #f7f9fc; }
        .store-grid { grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); }
        .product-card { transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
        .product-card:hover { transform: translateY(-3px); box-shadow: 0 18px 40px rgba(25, 71, 128, .11); border-color: #cbdcf4; }
        .product-card:active { transform: translateY(-1px); }
        .hero-glow { background: radial-gradient(circle at 80% 10%, rgba(59, 130, 246, .15), transparent 34%), #eef5ff; }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .product-card { transition: none; }
            .product-card:hover { transform: none; }
        }
    </style>
</head>
<body class="text-slate-900 antialiased">
    <a href="#produk" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:bg-slate-950 focus:px-4 focus:py-2 focus:text-sm focus:text-white">Lewati ke produk</a>

    <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl items-center gap-5 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('storefront.index') }}" class="flex shrink-0 items-center gap-2.5" aria-label="{{ $storeName }} beranda">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-base font-black text-white">{{ strtoupper(substr($storeName, 0, 1)) }}</span>
                <span class="text-lg font-bold tracking-tight text-slate-950">{{ $storeName }}</span>
            </a>

            <form action="{{ route('storefront.index') }}" method="GET" class="order-3 flex w-full items-center md:order-2 md:max-w-xl" role="search">
                <label for="store-search" class="sr-only">Cari produk</label>
                <div class="relative w-full">
                    <input id="store-search" type="search" name="q" value="{{ $search }}" placeholder="Cari produk yang kamu butuhkan" class="h-10 w-full rounded-xl border-slate-200 bg-slate-50 px-4 text-sm placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-blue-500">
                </div>
            </form>

            <nav class="order-2 ml-auto hidden items-center gap-5 text-sm font-medium text-slate-600 md:order-3 md:flex" aria-label="Navigasi utama">
                <a href="#produk" class="transition hover:text-blue-600">Produk</a>
                <a href="#bantuan" class="transition hover:text-blue-600">Bantuan</a>
                <a href="{{ route('tracking.index') }}" class="transition hover:text-blue-600">Lacak pesanan</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero-glow border-b border-blue-100">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 sm:py-16 lg:grid-cols-[1.05fr_.95fr] lg:items-center lg:px-8 lg:py-20">
                <div class="max-w-xl">
                    <p class="mb-4 text-sm font-semibold text-blue-700">Belanja lebih mudah dari satu tempat</p>
                    <h1 class="text-4xl font-black tracking-tight text-slate-950 sm:text-5xl lg:text-[3.6rem] lg:leading-[1.05]">Produk pilihan untuk kebutuhan sehari-hari.</h1>
                    <p class="mt-5 max-w-lg text-base leading-7 text-slate-600 sm:text-lg">Pilih produk, cek detailnya, lalu selesaikan checkout dengan pembayaran aman. Pesanan bisa dikirim dari gudang kami atau partner dropship tepercaya.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="#produk" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:scale-[.98]">Lihat produk</a>
                        <a href="#cara-belanja" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:scale-[.98]">Cara belanja</a>
                    </div>
                </div>

                @if($products->isNotEmpty())
                    @php
                        $featuredProduct = $products->first();
                        $featuredLandingPage = $featuredProduct->landingPages->first();
                        $featuredImage = is_array($featuredProduct->images) ? ($featuredProduct->images[0] ?? null) : null;
                        $featuredImageUrl = $featuredImage ? (filter_var($featuredImage, FILTER_VALIDATE_URL) ? $featuredImage : asset('storage/' . $featuredImage)) : null;
                    @endphp
                    <a href="{{ route('lp.show', $featuredLandingPage->slug) }}" class="group relative overflow-hidden rounded-[2rem] border border-white/80 bg-white p-3 shadow-2xl shadow-blue-900/10">
                        <div class="relative aspect-[4/3] overflow-hidden rounded-[1.5rem] bg-slate-100">
                            @if($featuredImageUrl)
                                <img src="{{ $featuredImageUrl }}" alt="{{ $featuredProduct->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            @else
                                <div class="flex h-full items-center justify-center bg-gradient-to-br from-slate-100 to-blue-100 px-8 text-center text-2xl font-bold text-slate-400">{{ $featuredProduct->name }}</div>
                            @endif
                        </div>
                        <div class="flex items-end justify-between gap-4 px-3 pb-2 pt-4">
                            <div>
                                <p class="text-xs font-medium text-slate-500">Produk terbaru</p>
                                <h2 class="mt-1 text-xl font-bold tracking-tight text-slate-950">{{ $featuredProduct->name }}</h2>
                            </div>
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white transition group-hover:translate-x-1" aria-hidden="true">→</span>
                        </div>
                    </a>
                @else
                    <div class="flex min-h-72 items-center justify-center rounded-[2rem] border border-dashed border-blue-200 bg-white p-8 text-center shadow-sm">
                        <div><p class="font-semibold text-slate-800">Katalog sedang disiapkan</p><p class="mt-2 text-sm text-slate-500">Produk baru akan muncul di sini setelah admin mengaktifkan produk dan landing page.</p></div>
                    </div>
                @endif
            </div>
        </section>

        <section id="produk" class="mx-auto max-w-7xl scroll-mt-24 px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
            <div class="flex flex-col justify-between gap-5 border-b border-slate-200 pb-6 sm:flex-row sm:items-end">
                <div>
                    <p class="text-sm font-semibold text-blue-700">Katalog toko</p>
                    <h2 class="mt-1 text-3xl font-black tracking-tight text-slate-950">Temukan produk pilihan</h2>
                    <p class="mt-2 text-sm text-slate-500">{{ $products->total() }} produk tersedia untuk dibeli.</p>
                </div>
                <form action="{{ route('storefront.index') }}#produk" method="GET" class="flex items-center gap-2">
                    @if($search !== '')<input type="hidden" name="q" value="{{ $search }}">@endif
                    <label for="sort" class="sr-only">Urutkan produk</label>
                    <select id="sort" name="sort" onchange="this.form.submit()" class="rounded-xl border-slate-200 bg-white py-2.5 pl-3 pr-9 text-sm font-medium text-slate-700 focus:border-blue-500 focus:ring-blue-500">
                        <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Harga terendah</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Harga tertinggi</option>
                    </select>
                </form>
            </div>

            @if($products->isNotEmpty())
                <div class="store-grid mt-8 grid gap-5 sm:gap-6">
                    @foreach($products as $product)
                        @php
                            $landingPage = $product->landingPages->first();
                            $image = is_array($product->images) ? ($product->images[0] ?? null) : null;
                            $imageUrl = $image ? (filter_var($image, FILTER_VALIDATE_URL) ? $image : asset('storage/' . $image)) : null;
                            $price = $product->has_variants && $product->variants->isNotEmpty() ? $product->variants->min('sell_price') : $product->sell_price;
                            $hasVariants = $product->has_variants && $product->variants->isNotEmpty();
                        @endphp
                        <article class="product-card group overflow-hidden rounded-2xl border border-slate-200 bg-white">
                            <a href="{{ route('lp.show', $landingPage->slug) }}" class="block focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" aria-label="Lihat {{ $product->name }}">
                                <div class="relative aspect-square overflow-hidden bg-slate-100">
                                    @if($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                    @else
                                        <div class="flex h-full items-center justify-center bg-gradient-to-br from-slate-100 to-blue-50 px-5 text-center text-lg font-bold text-slate-400">{{ $product->name }}</div>
                                    @endif
                                </div>
                            </a>
                            <div class="p-4">
                                <a href="{{ route('lp.show', $landingPage->slug) }}" class="block focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <h3 class="line-clamp-2 min-h-11 text-[15px] font-semibold leading-5 text-slate-900 transition group-hover:text-blue-700">{{ $landingPage->headline ?: $product->name }}</h3>
                                </a>
                                <p class="mt-3 text-lg font-black tabular-nums text-slate-950">{{ $hasVariants ? 'Mulai ' : '' }}Rp {{ number_format($price, 0, ',', '.') }}</p>
                                <a href="{{ route('lp.show', $landingPage->slug) }}" class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-blue-50 px-4 py-2.5 text-sm font-semibold text-blue-700 transition hover:bg-blue-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:scale-[.98]">{{ $hasVariants ? 'Pilih varian' : 'Lihat produk' }}</a>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mt-10">{{ $products->links() }}</div>
            @else
                <div class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-2xl text-slate-400">⌕</div>
                    <h3 class="mt-5 text-lg font-bold text-slate-900">Produk tidak ditemukan</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">Coba gunakan kata kunci yang lebih umum atau hapus pencarian untuk melihat semua produk yang tersedia.</p>
                    <a href="{{ route('storefront.index') }}#produk" class="mt-6 inline-flex rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Lihat semua produk</a>
                </div>
            @endif
        </section>

        <section id="cara-belanja" class="border-y border-slate-200 bg-white scroll-mt-24">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:px-6 md:grid-cols-3 lg:px-8 lg:py-14">
                <div><p class="text-sm font-semibold text-blue-700">Cara belanja</p><h2 class="mt-2 text-2xl font-black tracking-tight text-slate-950">Dari pilih produk sampai paket tiba.</h2></div>
                <div class="md:col-span-2 grid gap-6 sm:grid-cols-3">
                    <div><span class="text-2xl font-black text-blue-600">01</span><h3 class="mt-3 font-bold text-slate-900">Pilih produk</h3><p class="mt-2 text-sm leading-6 text-slate-500">Buka detail produk dan pilih varian jika tersedia.</p></div>
                    <div><span class="text-2xl font-black text-blue-600">02</span><h3 class="mt-3 font-bold text-slate-900">Isi checkout</h3><p class="mt-2 text-sm leading-6 text-slate-500">Masukkan alamat, pilih kurir, lalu pilih metode pembayaran.</p></div>
                    <div><span class="text-2xl font-black text-blue-600">03</span><h3 class="mt-3 font-bold text-slate-900">Lacak pesanan</h3><p class="mt-2 text-sm leading-6 text-slate-500">Gunakan nomor order dan WhatsApp untuk melihat status kiriman.</p></div>
                </div>
            </div>
        </section>

        <section id="bantuan" class="mx-auto max-w-7xl scroll-mt-24 px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
            <div class="flex flex-col justify-between gap-6 rounded-3xl bg-blue-700 px-6 py-8 text-white shadow-xl shadow-blue-900/10 sm:flex-row sm:items-center sm:px-10">
                <div><p class="text-sm font-semibold text-blue-300">Butuh bantuan?</p><h2 class="mt-2 text-2xl font-black tracking-tight">Pesanan sudah dibuat? Cek statusnya kapan saja.</h2></div>
                <a href="{{ route('tracking.index') }}" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-bold text-blue-800 transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-700">Lacak pesanan</a>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-8 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <p>© {{ date('Y') }} {{ $storeName }}. Belanja dengan tenang.</p>
            <div class="flex gap-5"><a href="{{ route('tracking.index') }}" class="transition hover:text-blue-700">Lacak pesanan</a><a href="{{ route('storefront.index') }}#produk" class="transition hover:text-blue-700">Katalog</a></div>
        </div>
    </footer>
</body>
</html>
