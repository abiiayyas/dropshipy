<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Dropshipy - Platform dropshipping #1 Indonesia. Kelola landing page, order, warehouse, dan pembayaran semua marketplace dalam satu dashboard.">
    <title>Dropshipy - Platform Dropshipping Indonesia</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-white text-gray-900 antialiased">
    <!-- Navbar -->
    <nav class="fixed top-0 inset-x-0 bg-white/80 backdrop-blur-md border-b border-gray-100 z-50">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center h-16">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-violet-600 to-indigo-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm">D</span>
                </div>
                <span class="font-bold text-xl">Dropshipy</span>
            </div>
            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                <a href="#features" class="hover:text-gray-900 transition-colors">Fitur</a>
                <a href="#how-it-works" class="hover:text-gray-900 transition-colors">Cara Kerja</a>
                <a href="#pricing" class="hover:text-gray-900 transition-colors">Harga</a>
                <a href="#faq" class="hover:text-gray-900 transition-colors">FAQ</a>
            </div>
            <div class="flex items-center gap-3">
                <a href="/login" class="text-sm font-medium text-gray-700 hover:text-gray-900">Masuk</a>
                <a href="/register" class="text-sm font-medium bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors">Daftar Gratis</a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="pt-32 pb-20 px-4 overflow-hidden">
        <div class="max-w-7xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 bg-violet-50 text-violet-700 text-sm font-medium px-4 py-1.5 rounded-full mb-6">
                <span class="w-2 h-2 bg-violet-500 rounded-full animate-pulse"></span>
                Platform Dropshipping #1 Indonesia
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight mb-6 max-w-4xl mx-auto">
                Jualan Online Lebih <span class="bg-gradient-to-r from-violet-600 to-indigo-600 bg-clip-text text-transparent">Mudah & Cepat</span>
            </h1>
            <p class="text-lg sm:text-xl text-gray-600 mb-10 max-w-2xl mx-auto">
                Kelola landing page, order, stok multi-gudang, sampai pembayaran Midtrans & COD — semuanya dalam satu dashboard.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/register" class="inline-flex items-center justify-center bg-gray-900 text-white px-8 py-3.5 rounded-xl font-medium hover:bg-gray-800 shadow-lg transition-colors">Mulai Gratis Sekarang →</a>
                <a href="#features" class="inline-flex items-center justify-center bg-white text-gray-700 px-8 py-3.5 rounded-xl font-medium border border-gray-200 hover:border-gray-300 transition-colors">Lihat Fitur</a>
            </div>

            <!-- Dashboard mockup -->
            <div class="mt-16 max-w-5xl mx-auto rounded-2xl border border-gray-200 shadow-2xl shadow-violet-100/60 overflow-hidden text-left">
                <div class="bg-gray-50 border-b border-gray-100 px-4 py-3 flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-400"></span>
                    <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                    <span class="w-3 h-3 rounded-full bg-green-400"></span>
                    <span class="ml-4 text-xs text-gray-400 bg-white border border-gray-100 rounded-md px-3 py-1">app.dropshipy.id/admin/dashboard</span>
                </div>
                <div class="grid grid-cols-12 bg-white">
                    <div class="hidden md:block col-span-3 border-r border-gray-100 p-4 space-y-3">
                        @foreach ([['Dashboard', 'bg-violet-100 text-violet-700'], ['Produk', ''], ['Landing Pages', ''], ['Orders', ''], ['Warehouses', ''], ['Analytics', '']] as [$menu, $active])
                            <div class="flex items-center gap-2 text-xs font-medium rounded-lg px-3 py-2 {{ $active ?: 'text-gray-400' }}">
                                <span class="w-2 h-2 rounded {{ $active ? 'bg-violet-500' : 'bg-gray-300' }}"></span>{{ $menu }}
                            </div>
                        @endforeach
                    </div>
                    <div class="col-span-12 md:col-span-9 p-6">
                        <div class="grid grid-cols-3 gap-4 mb-6">
                            @foreach ([['Order Hari Ini', '128', '+24%'], ['Pendapatan', 'Rp 18,4jt', '+31%'], ['Konversi LP', '7,8%', '+0,9%']] as [$label, $value, $delta])
                                <div class="rounded-xl border border-gray-100 p-4">
                                    <div class="text-[11px] text-gray-400">{{ $label }}</div>
                                    <div class="text-lg sm:text-xl font-bold mt-1">{{ $value }}</div>
                                    <div class="text-[11px] text-emerald-500 font-medium mt-1">↑ {{ $delta }}</div>
                                </div>
                            @endforeach
                        </div>
                        <div class="rounded-xl border border-gray-100 p-4">
                            <div class="flex items-end justify-between gap-2 h-28">
                                @foreach ([40, 55, 35, 70, 62, 85, 95] as $h)
                                    <div class="flex-1 rounded-t-md bg-gradient-to-t from-violet-200 to-violet-500" style="height: {{ $h }}%"></div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="py-12 border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div><div class="text-3xl font-bold">10K+</div><div class="text-sm text-gray-500 mt-1">Active Sellers</div></div>
            <div><div class="text-3xl font-bold">500K+</div><div class="text-sm text-gray-500 mt-1">Order Diproses</div></div>
            <div><div class="text-3xl font-bold">4</div><div class="text-sm text-gray-500 mt-1">Marketplace</div></div>
            <div><div class="text-3xl font-bold">99.9%</div><div class="text-sm text-gray-500 mt-1">Uptime Server</div></div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-20 px-4 scroll-mt-20">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold mb-4">Semua yang Anda Butuhkan</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Fitur lengkap untuk bisnis dropshipping yang profitable — dari landing page sampai pengiriman.</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="p-6 rounded-2xl border border-gray-100 hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5"/></svg>
                    </div>
                    <h3 class="font-semibold mb-2">Dashboard All-in-One</h3>
                    <p class="text-sm text-gray-600">Kelola produk, order, campaign, dan gudang dari satu tempat. Performa bisnis terlihat jelas dalam satu view.</p>
                </div>
                <div class="p-6 rounded-2xl border border-gray-100 hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <h3 class="font-semibold mb-2">Landing Page Builder</h3>
                    <p class="text-sm text-gray-600">Template profesional siap pakai bergaya Shopee, Tokopedia, TikTok Shop & Blibli. Lengkap dengan varian produk dan tracking kunjungan.</p>
                </div>
                <div class="p-6 rounded-2xl border border-gray-100 hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-semibold mb-2">Order Management + Supplier Queue</h3>
                    <p class="text-sm text-gray-600">Proses order otomatis. Antrian order supplier satu klik, copy data supplier instan, update status & resi tanpa ribet.</p>
                </div>
                <div class="p-6 rounded-2xl border border-gray-100 hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                    </div>
                    <h3 class="font-semibold mb-2">Multi Warehouse</h3>
                    <p class="text-sm text-gray-600">Kelola beberapa gudang sekaligus. Pencarian area otomatis untuk ongkir akurat dari lokasi terdekat.</p>
                </div>
                <div class="p-6 rounded-2xl border border-gray-100 hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-rose-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                    </div>
                    <h3 class="font-semibold mb-2">Payment Midtrans & COD</h3>
                    <p class="text-sm text-gray-600">Terima transfer virtual account, e-wallet, QRIS via Midtrans — atau COD. Webhook otomatis update status pembayaran.</p>
                </div>
                <div class="p-6 rounded-2xl border border-gray-100 hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-cyan-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                    </div>
                    <h3 class="font-semibold mb-2">Analytics & UTM Tracking</h3>
                    <p class="text-sm text-gray-600">Lacak kunjungan per landing page, performa campaign dengan parameter UTM, dan detail konversi tiap iklan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section id="how-it-works" class="py-20 px-4 bg-gray-50 scroll-mt-20">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold mb-4">Cara Kerjanya Sederhana</h2>
                <p class="text-gray-600 text-lg">Tiga langkah dari iklan sampai paket terkirim.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6 relative">
                <div class="hidden md:block absolute top-10 left-[16%] right-[16%] h-px bg-gradient-to-r from-violet-200 via-indigo-200 to-violet-200"></div>
                <div class="relative text-center p-6">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-violet-600 to-indigo-600 text-white flex items-center justify-center text-2xl font-bold shadow-lg shadow-violet-200">1</div>
                    <h3 class="font-semibold text-lg mb-2">Buat Landing Page</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Pilih template marketplace favorit, atur produk & harga, share link ke iklan Anda. Setiap kunjungan tercatat otomatis.</p>
                </div>
                <div class="relative text-center p-6">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-violet-600 to-indigo-600 text-white flex items-center justify-center text-2xl font-bold shadow-lg shadow-violet-200">2</div>
                    <h3 class="font-semibold text-lg mb-2">Order Masuk Otomatis</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Customer isi form checkout, pilih kurir & bayar via Midtrans atau COD. Notifikasi WhatsApp langsung terkirim.</p>
                </div>
                <div class="relative text-center p-6">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-violet-600 to-indigo-600 text-white flex items-center justify-center text-2xl font-bold shadow-lg shadow-violet-200">3</div>
                    <h3 class="font-semibold text-lg mb-2">Kirim ke Supplier & Lacak</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Forward order ke supplier lewat supplier queue, input resi, customer bisa lacak paket real-time lewat halaman tracking.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Marketplaces -->
    <section id="marketplaces" class="py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold mb-4">Semua Marketplace, Satu Dashboard</h2>
                <p class="text-gray-600 text-lg">Template landing page dengan gaya visual sesuai platform target Anda</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 text-center hover:shadow-lg transition-all">
                    <div class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-orange-500">S</span>
                    </div>
                    <h3 class="font-semibold">Shopee</h3>
                    <p class="text-xs text-gray-500 mt-1">Template Siap Pakai</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 text-center hover:shadow-lg transition-all">
                    <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-green-600">T</span>
                    </div>
                    <h3 class="font-semibold">Tokopedia</h3>
                    <p class="text-xs text-gray-500 mt-1">Template Siap Pakai</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 text-center hover:shadow-lg transition-all">
                    <div class="w-16 h-16 bg-gray-900 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-white">T</span>
                    </div>
                    <h3 class="font-semibold">TikTok Shop</h3>
                    <p class="text-xs text-gray-500 mt-1">Template Siap Pakai</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 text-center hover:shadow-lg transition-all">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-blue-600">B</span>
                    </div>
                    <h3 class="font-semibold">Blibli</h3>
                    <p class="text-xs text-gray-500 mt-1">Template Siap Pakai</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="py-20 px-4 bg-gray-50 scroll-mt-20">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold mb-4">Dipercaya Ribuan Seller</h2>
                <p class="text-gray-600 text-lg">Cerita mereka yang sudah tumbuh bersama Dropshipy.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex gap-1 text-amber-400 mb-4">
                        @foreach(range(1,5) as $i)<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>@endforeach
                    </div>
                    <p class="text-gray-700 text-sm leading-relaxed mb-6">"Dulu kelola order dari 4 marketplace manual pakai spreadsheet. Sekarang semua masuk satu dashboard, supplier queue-nya bikin proses kirim 3x lebih cepat."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-violet-100 text-violet-700 flex items-center justify-center font-semibold">RA</div>
                        <div>
                            <div class="text-sm font-semibold">Rizky Ananda</div>
                            <div class="text-xs text-gray-500">Seller Skincare, Jakarta</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex gap-1 text-amber-400 mb-4">
                        @foreach(range(1,5) as $i)<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>@endforeach
                    </div>
                    <p class="text-gray-700 text-sm leading-relaxed mb-6">"Fitur UTM tracking-nya juara. Saya bisa lihat iklan mana yang beneran closing, bukan cuma ramai klik. ROAS naik signifikan dalam sebulan."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-semibold">SP</div>
                        <div>
                            <div class="text-sm font-semibold">Sari Puspita</div>
                            <div class="text-xs text-gray-500">Media Buyer, Bandung</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex gap-1 text-amber-400 mb-4">
                        @foreach(range(1,5) as $i)<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>@endforeach
                    </div>
                    <p class="text-gray-700 text-sm leading-relaxed mb-6">"COD + notifikasi WhatsApp otomatis itu game changer. Tinggal follow up, tidak perlu konfirmasi manual satu-satu lagi."</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-semibold">BH</div>
                        <div>
                            <div class="text-sm font-semibold">Budi Hartono</div>
                            <div class="text-xs text-gray-500">Dropshipper Fashion, Surabaya</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="py-20 px-4 scroll-mt-20">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold mb-4">Harga Transparan</h2>
                <p class="text-gray-600 text-lg">Mulai gratis, upgrade saat bisnis Anda tumbuh.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto items-stretch">
                <div class="p-8 rounded-2xl border border-gray-200 flex flex-col">
                    <h3 class="font-semibold text-lg">Starter</h3>
                    <p class="text-sm text-gray-500 mt-1">Untuk mulai coba-coba</p>
                    <div class="mt-6 mb-6"><span class="text-4xl font-bold">Gratis</span><span class="text-sm text-gray-500"> /selamanya</span></div>
                    <ul class="space-y-3 text-sm text-gray-600 flex-1">
                        @foreach (['3 Landing Page aktif', '1 Warehouse', 'Checkout COD', 'Tracking order dasar'] as $f)
                            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>{{ $f }}</li>
                        @endforeach
                    </ul>
                    <a href="/register" class="mt-8 inline-flex justify-center border border-gray-200 text-gray-700 px-6 py-3 rounded-xl font-medium hover:border-gray-300 transition-colors">Daftar Gratis</a>
                </div>
                <div class="p-8 rounded-2xl border-2 border-violet-600 relative bg-white shadow-xl shadow-violet-100 flex flex-col">
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-violet-600 text-white text-xs font-medium px-4 py-1 rounded-full">Paling Populer</span>
                    <h3 class="font-semibold text-lg">Pro</h3>
                    <p class="text-sm text-gray-500 mt-1">Untuk seller serius scale</p>
                    <div class="mt-6 mb-6"><span class="text-4xl font-bold">Rp 149rb</span><span class="text-sm text-gray-500"> /bulan</span></div>
                    <ul class="space-y-3 text-sm text-gray-600 flex-1">
                        @foreach (['Landing Page tanpa batas', 'Unlimited warehouse', 'Midtrans Payment Gateway', 'Supplier Queue + export', 'UTM campaign analytics', 'Notifikasi WhatsApp otomatis'] as $f)
                            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>{{ $f }}</li>
                        @endforeach
                    </ul>
                    <a href="/register" class="mt-8 inline-flex justify-center bg-gradient-to-r from-violet-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-medium hover:opacity-90 transition-opacity">Mulai 14 Hari Gratis</a>
                </div>
                <div class="p-8 rounded-2xl border border-gray-200 flex flex-col">
                    <h3 class="font-semibold text-lg">Business</h3>
                    <p class="text-sm text-gray-500 mt-1">Untuk tim & brand besar</p>
                    <div class="mt-6 mb-6"><span class="text-4xl font-bold">Rp 399rb</span><span class="text-sm text-gray-500"> /bulan</span></div>
                    <ul class="space-y-3 text-sm text-gray-600 flex-1">
                        @foreach (['Semua fitur Pro', 'Multi user + role admin/operator', 'Prioritas support 24/7', 'Custom domain landing page', 'Export data lengkap'] as $f)
                            <li class="flex items-start gap-2"><svg class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>{{ $f }}</li>
                        @endforeach
                    </ul>
                    <a href="/register" class="mt-8 inline-flex justify-center border border-gray-200 text-gray-700 px-6 py-3 rounded-xl font-medium hover:border-gray-300 transition-colors">Hubungi Kami</a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="py-20 px-4 bg-gray-50 scroll-mt-20">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold mb-4">Pertanyaan Umum</h2>
                <p class="text-gray-600 text-lg">Masih ragu? Mungkin jawabannya ada di sini.</p>
            </div>
            <div class="space-y-4">
                @foreach ([
                    ['Apakah Dropshipy cocok untuk pemula?', 'Sangat cocok. Mulai dari paket gratis, buat landing page pertama dalam hitungan menit dengan template siap pakai. Tidak perlu coding, tidak perlu stok barang.'],
                    ['Bagaimana cara pembayarannya?', 'Customer bisa bayar via transfer virtual account, e-wallet, QRIS melalui Midtrans, atau COD. Status pembayaran ter-update otomatis lewat webhook Midtrans.'],
                    ['Bagaimana proses pengiriman ke customer?', 'Setelah order masuk, forward ke supplier lewat Supplier Queue. Ongkir dihitung otomatis via integrasi kurir (JNE, J&T, SiCepat), dan customer bisa lacak paket lewat nomor order.'],
                    ['Apakah bisa lihat iklan mana yang menghasilkan?', 'Bisa. Tambahkan parameter UTM di link iklan Anda, lalu pantau performa per campaign di menu Analytics — termasuk kunjungan, order, dan konversi per landing page.'],
                    ['Apakah data order bisa diekspor?', 'Ya. Semua order bisa diekspor ke file untuk kebutuhan pembukuan atau laporan supplier, kapan saja dari dashboard admin.'],
                ] as [$q, $a])
                    <details class="group bg-white rounded-2xl border border-gray-100 open:shadow-md transition-shadow">
                        <summary class="flex justify-between items-center cursor-pointer list-none p-6 font-medium select-none">
                            {{ $q }}
                            <svg class="w-5 h-5 text-gray-400 shrink-0 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </summary>
                        <p class="px-6 pb-6 text-sm text-gray-600 leading-relaxed">{{ $a }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section id="cta" class="py-20 px-4">
        <div class="max-w-5xl mx-auto">
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-violet-600 to-indigo-700 px-8 py-16 sm:px-16 text-center">
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>
                <h2 class="relative text-3xl sm:text-4xl font-bold text-white mb-4">Siap Memulai Bisnis Dropshipping?</h2>
                <p class="relative text-violet-100 text-lg mb-8 max-w-2xl mx-auto">Daftar sekarang, buat landing page pertama Anda dalam hitungan menit — gratis, tanpa kartu kredit.</p>
                <div class="relative flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/register" class="inline-flex items-center justify-center bg-white text-violet-700 px-8 py-4 rounded-xl font-semibold hover:bg-violet-50 shadow-lg transition-colors">Daftar Gratis Sekarang →</a>
                    <a href="#features" class="inline-flex items-center justify-center border border-white/30 text-white px-8 py-4 rounded-xl font-medium hover:bg-white/10 transition-colors">Pelajari Lebih Lanjut</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-gray-100 py-12 px-4">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-10 mb-10">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-gradient-to-br from-violet-600 to-indigo-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">D</span>
                    </div>
                    <span class="font-bold text-lg">Dropshipy</span>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed">Platform dropshipping all-in-one untuk seller Indonesia. Buat landing page, proses order, dan scale bisnis Anda.</p>
            </div>
            <div>
                <h4 class="font-semibold text-sm mb-4">Produk</h4>
                <ul class="space-y-2.5 text-sm text-gray-500">
                    <li><a href="#features" class="hover:text-gray-900 transition-colors">Fitur</a></li>
                    <li><a href="#marketplaces" class="hover:text-gray-900 transition-colors">Marketplace</a></li>
                    <li><a href="#pricing" class="hover:text-gray-900 transition-colors">Harga</a></li>
                    <li><a href="/track" class="hover:text-gray-900 transition-colors">Lacak Order</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-sm mb-4">Perusahaan</h4>
                <ul class="space-y-2.5 text-sm text-gray-500">
                    <li><a href="#" class="hover:text-gray-900 transition-colors">Tentang Kami</a></li>
                    <li><a href="#" class="hover:text-gray-900 transition-colors">Blog</a></li>
                    <li><a href="#" class="hover:text-gray-900 transition-colors">Karir</a></li>
                    <li><a href="#" class="hover:text-gray-900 transition-colors">Kontak</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-sm mb-4">Legal</h4>
                <ul class="space-y-2.5 text-sm text-gray-500">
                    <li><a href="#" class="hover:text-gray-900 transition-colors">Syarat & Ketentuan</a></li>
                    <li><a href="#" class="hover:text-gray-900 transition-colors">Kebijakan Privasi</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm text-gray-500">© {{ date('Y') }} Dropshipy. All rights reserved.</p>
            <p class="text-sm text-gray-400">Dibuat dengan ♥ untuk seller Indonesia</p>
        </div>
    </footer>
</body>
</html>
