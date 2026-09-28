<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>{{ $landingPage->headline ?: $landingPage->product->name }}</title>
    <meta name="description" content="{{ $landingPage->subheadline ?: $landingPage->product->description }}">

    @php
        $globalPixel = \App\Models\Setting::get('global_pixel_id');
        $hasPixel = $landingPage->pixel_id || $globalPixel;
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
    @if($landingPage->pixel_id)
    fbq('init', '{{ $landingPage->pixel_id }}');
    @endif
    fbq('track', 'PageView');
    </script>
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #121212; color: #e5e7eb; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        #top-header { transition: background-color 0.3s ease; }
        #top-header.scrolled { background-color: rgba(18, 18, 18, 0.95); backdrop-filter: blur(10px); }
        @keyframes pulse { 0%,100% { transform: scale(1); } 50% { transform: scale(1.05); } }
        @keyframes bounce { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        @keyframes shake { 0%,100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
        @keyframes arrowBounceDown { 0%,100% { transform: translateY(0); opacity: 1; } 50% { transform: translateY(12px); opacity: 0.6; } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .anim-arrow { position: fixed; z-index: 9999; pointer-events: none; font-size: 2rem; display: none; }
        .anim-arrow.down { animation: arrowBounceDown 1s ease infinite; }
        .anim-pulse { animation: pulse 1.5s ease infinite; }
        .anim-bounce { animation: bounce 1s ease infinite; }
        .anim-shake { animation: shake 0.5s ease infinite; }
        .anim-fadein { animation: fadeInUp 0.6s ease forwards; opacity: 0; }
        .embed-wrapper { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; }
        .embed-wrapper iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; }
        [x-cloak] { display: none !important; }
        .collapsible-section .content { max-height: 0; overflow: hidden; transition: max-height 0.35s ease; }
        .collapsible-section.open .content { max-height: 800px; }
        .recommend-scroll::-webkit-scrollbar { height: 4px; }
        .recommend-scroll::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
    </style>
</head>
<body class="text-gray-200 antialiased">
    <div class="max-w-md mx-auto bg-[#121212] min-h-screen relative overflow-x-hidden pb-[100px]">

        {{-- TikTok-style Top Bar --}}
        <header id="top-header" class="fixed top-0 max-w-md w-full z-50 flex items-center justify-between px-3 py-2.5">
            <button onclick="history.back()" class="w-8 h-8 flex items-center justify-center text-white">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5m7-7l-7 7 7 7"></path></svg>
            </button>
            <div class="flex items-center gap-3">
                <button class="w-8 h-8 flex items-center justify-center text-white" onclick="alert('Link disalin!'); navigator.clipboard.writeText(window.location.href);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M3.092 19.78a1 1 0 0 0 .78-.57c2.36-3.81 6.57-4.82 9.95-5v2.45a1.33 1.33 0 0 0 .8 1.22 1.25 1.25 0 0 0 1.37-.28l5.1-5a2.25 2.25 0 0 0 0-3.18l-5.05-5a1.25 1.25 0 0 0-1.38-.28 1.29 1.29 0 0 0-.79 1.2v2.43c-6.78.32-11.53 4.94-11.53 11.23a.8.8 0 0 0 .55.75l.2.03Z"></path></svg>
                </button>
                <button class="w-8 h-8 flex items-center justify-center text-white">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M21.68 7.56a1.908 1.908 0 0 0-.35-.66 1.71 1.71 0 0 0-.58-.46 1.85 1.85 0 0 0-.75-.19H6.17a1.82 1.82 0 0 0-.57.13l-.06-.3a1.91 1.91 0 0 0-2-1.83h-1a.75.75 0 0 0 0 1.5h1c.42 0 .49.07.57.59l1.09 5.54.54 2.78A3.86 3.86 0 0 0 7 16.89a3.76 3.76 0 0 0 1.54.75A2 2 0 0 0 8 19a2 2 0 0 0 4 0 2 2 0 0 0-.46-1.25h2.88a2 2 0 1 0 3.06-.12 3.8 3.8 0 0 0 1.46-.7 3.71 3.71 0 0 0 1.32-2.1l1.47-6.46V8.3a1.68 1.68 0 0 0-.05-.74Z"></path></svg>
                </button>
            </div>
        </header>

        {{-- Immersive Full-Width Gallery --}}
        @php
            $images = count($landingPage->parsed_slider_images) ? $landingPage->parsed_slider_images : ($landingPage->cover_image ? [asset('storage/' . $landingPage->cover_image)] : []);
        @endphp
        <div class="relative w-full bg-black" id="hero-slider">
            @if(count($images))
            <div class="flex w-full transition-transform duration-300" id="hero-slider-track" style="aspect-ratio: 9/14; max-height: 80vh;">
                @foreach($images as $img)
                <div class="w-full flex-shrink-0 bg-black flex items-center justify-center">
                    <img src="{{ $img }}" class="w-full h-full object-cover" alt="Product Image">
                </div>
                @endforeach
            </div>
            @if(count($images) > 1)
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2" id="slider-dots">
                @foreach($images as $i => $_)
                <div class="w-2 h-2 rounded-full {{ $i === 0 ? 'bg-[#FF2C55]' : 'bg-white/40' }} transition-all"></div>
                @endforeach
            </div>
            <div class="absolute bottom-4 right-4 bg-black/50 text-white/80 text-[10px] px-2 py-1 rounded-full backdrop-blur-sm" id="slider-counter">
                1/{{ count($images) }}
            </div>
            @endif
            @else
            <div class="w-full bg-gray-800 flex items-center justify-center text-gray-500" style="aspect-ratio: 9/14; max-height: 80vh;">Tanpa Gambar</div>
            @endif
        </div>

        @php
            $price = $landingPage->product->sell_price;
            $fakeOriginalPrice = $price * 1.4;
            $discountPercent = 29;
            $rating = 4.8;
            $soldCount = 2340 + ($landingPage->orders_count ?? 0);
        @endphp

        {{-- Product Info Compact --}}
        <div class="px-4 pt-3 pb-2 bg-[#1a1a1a] -mt-5 rounded-t-2xl relative z-10">
            <h1 class="text-[15px] font-bold text-white leading-snug mb-1.5">
                {{ $landingPage->headline ?: $landingPage->product->name }}
            </h1>
            @if($landingPage->subheadline)
            <p class="text-xs text-gray-400 mb-2">{{ $landingPage->subheadline }}</p>
            @endif

            {{-- Price Row --}}
            <div class="flex items-end gap-2 mb-2">
                <span class="text-2xl font-black text-[#FF2C55]" id="price-display">Rp{{ number_format($price, 0, ',', '.') }}</span>
                <span class="text-xs text-gray-500 line-through mb-1">Rp{{ number_format($fakeOriginalPrice, 0, ',', '.') }}</span>
                <span class="text-[10px] bg-[#FF2C55]/20 text-[#FF2C55] px-2 py-0.5 rounded-full font-bold mb-1">{{ $discountPercent }}%</span>
            </div>

            {{-- Rating + Sold --}}
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-3">
                <div class="flex text-yellow-400 gap-0.5">
                    @for($i=0; $i<5; $i++)
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24l5.46 4.73L5.82 21z"></path></svg>
                    @endfor
                </div>
                <span class="text-white font-medium">{{ $rating }}</span>
                <span class="text-gray-600">|</span>
                <span>{{ number_format($soldCount, 0, ',', '.') }} Terjual</span>
            </div>
        </div>

        <div class="h-1 bg-[#222]"></div>

        {{-- Shop Info Compact Row --}}
        <div class="px-4 py-3 bg-[#1a1a1a]">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 bg-gradient-to-br from-[#FF2C55] to-pink-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr(\App\Models\Setting::get('store_name', 'S'), 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-xs font-bold text-white">{{ \App\Models\Setting::get("store_name", config("app.name", "Toko Resmi")) }}</div>
                        <div class="text-[10px] text-gray-400 flex items-center gap-1">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="#22c55e"><path d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5zm-2 14l-4-4 1.41-1.41L10 13.17l6.59-6.59L18 8l-8 8z"></path></svg>
                            Verified Seller
                        </div>
                    </div>
                </div>
                <button class="bg-[#FF2C55] text-white text-[10px] font-bold px-3 py-1.5 rounded-full hover:bg-pink-600 transition-colors">
                    + Follow
                </button>
            </div>
        </div>

        <div class="h-1 bg-[#222]"></div>

        {{-- Variants --}}
        @if($landingPage->product->has_variants && count($landingPage->product->options))
        <div class="px-4 py-3 bg-[#1a1a1a] anim-fadein">
            <h2 class="font-bold text-sm text-white mb-3">Pilih Variasi</h2>
            <div id="variants-container" class="space-y-3">
                @foreach($landingPage->product->options as $option)
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">{{ $option->name }}</label>
                    <select class="variant-option-select w-full bg-[#222] border border-[#333] rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-[#FF2C55]" data-option-id="{{ $option->id }}">
                        <option value="" class="bg-[#222]">Pilih {{ $option->name }}</option>
                        @foreach($option->optionValues as $val)
                        <option value="{{ $val->id }}" class="bg-[#222]">{{ $val->value }}</option>
                        @endforeach
                    </select>
                </div>
                @endforeach
            </div>
            <div id="variant-error" class="text-xs text-red-500 mt-2 hidden">Kombinasi variasi tidak tersedia.</div>
        </div>
        <div class="h-1 bg-[#222]"></div>
        @endif

        {{-- Collapsible Sections with Alpine.js --}}
        <div x-data="{ openSection: 'detail' }">

            {{-- Product Detail --}}
            <div class="bg-[#1a1a1a] collapsible-section open">
                <button @click="openSection = openSection === 'detail' ? '' : 'detail'" class="w-full px-4 py-3 flex justify-between items-center text-left">
                    <span class="font-bold text-sm text-white">Detail Produk</span>
                    <svg :class="openSection === 'detail' ? 'rotate-180' : ''" class="transition-transform text-gray-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"></path></svg>
                </button>
                <div class="content" :class="openSection === 'detail' ? '!max-h-[2000px]' : ''">
                    <div class="px-4 pb-3">
                        @if($landingPage->body_content)
                        <div class="text-sm text-gray-300 leading-relaxed whitespace-pre-line">{{ $landingPage->body_content }}</div>
                        @endif
                        @if(count($landingPage->parsed_list_items))
                        <div class="mt-3 space-y-2">
                            @foreach($landingPage->parsed_list_items as $feature)
                            <div class="flex items-start gap-2">
                                <span class="text-[#FF2C55] text-xs mt-0.5">✦</span>
                                <span class="text-sm text-gray-300">{{ $feature }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="h-px bg-[#222]"></div>

            {{-- FAQ Section --}}
            @if(count($landingPage->parsed_faqs))
            <div class="bg-[#1a1a1a] collapsible-section">
                <button @click="openSection = openSection === 'faq' ? '' : 'faq'" class="w-full px-4 py-3 flex justify-between items-center text-left">
                    <span class="font-bold text-sm text-white">FAQ</span>
                    <svg :class="openSection === 'faq' ? 'rotate-180' : ''" class="transition-transform text-gray-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"></path></svg>
                </button>
                <div class="content" :class="openSection === 'faq' ? '!max-h-[2000px]' : ''">
                    <div class="px-4 pb-3 space-y-2">
                        @foreach($landingPage->parsed_faqs as $faq)
                        <div class="bg-[#222] rounded-lg p-3">
                            <div class="text-xs font-semibold text-white mb-1">{{ $faq['q'] }}</div>
                            <div class="text-xs text-gray-400">{{ $faq['a'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="h-px bg-[#222]"></div>
            @endif

            {{-- Video Section --}}
            @if($landingPage->youtube_embed_url)
            <div class="bg-[#1a1a1a] collapsible-section">
                <button @click="openSection = openSection === 'video' ? '' : 'video'" class="w-full px-4 py-3 flex justify-between items-center text-left">
                    <span class="font-bold text-sm text-white">Video Produk</span>
                    <svg :class="openSection === 'video' ? 'rotate-180' : ''" class="transition-transform text-gray-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"></path></svg>
                </button>
                <div class="content" :class="openSection === 'video' ? '!max-h-[2000px]' : ''">
                    <div class="px-4 pb-3">
                        <div class="embed-wrapper rounded-lg overflow-hidden">
                            <iframe src="{{ $landingPage->youtube_embed_url }}" title="Video Produk" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" referrerpolicy="strict-origin-when-cross-origin" sandbox="allow-scripts allow-same-origin allow-presentation" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
            <div class="h-px bg-[#222]"></div>
            @endif

            {{-- Reviews Section --}}
            @if(count($landingPage->parsed_testimonials))
            <div class="bg-[#1a1a1a] collapsible-section">
                <button @click="openSection = openSection === 'review' ? '' : 'review'" class="w-full px-4 py-3 flex justify-between items-center text-left">
                    <span class="font-bold text-sm text-white">Ulasan Pembeli</span>
                    <svg :class="openSection === 'review' ? 'rotate-180' : ''" class="transition-transform text-gray-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"></path></svg>
                </button>
                <div class="content" :class="openSection === 'review' ? '!max-h-[2000px]' : ''">
                    <div class="px-4 pb-3 space-y-2">
                        @foreach($landingPage->parsed_testimonials as $t)
                        <div class="bg-[#222] rounded-lg p-3">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-7 h-7 bg-[#FF2C55]/20 rounded-full flex items-center justify-center text-[#FF2C55] text-[10px] font-bold">
                                    {{ substr($t['name'], 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-white">{{ $t['name'] }}</div>
                                    <div class="flex text-yellow-400">
                                        @for($i=0; $i<5; $i++) <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24l5.46 4.73L5.82 21z"></path></svg> @endfor
                                    </div>
                                </div>
                                <span class="ml-auto text-[9px] text-gray-500">{{ $t['role'] }}</span>
                            </div>
                            <p class="text-[11px] text-gray-400 leading-relaxed">"{{ $t['quote'] }}"</p>
                            {{-- Photo thumbnail placeholder --}}
                            <div class="flex gap-1 mt-2">
                                <div class="w-12 h-12 bg-[#333] rounded flex items-center justify-center text-gray-600 text-[8px]">📷</div>
                                <div class="w-12 h-12 bg-[#333] rounded flex items-center justify-center text-gray-600 text-[8px]">📷</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Recommended Products Horizontal Scroll --}}
        <div class="bg-[#1a1a1a] px-4 py-3">
            <h3 class="font-bold text-sm text-white mb-3">Rekomendasi</h3>
            <div class="flex gap-3 overflow-x-auto no-scrollbar recommend-scroll pb-2">
                @php $recImages = count($images) ? $images : ($landingPage->cover_image ? [asset('storage/'.$landingPage->cover_image)] : []); @endphp
                @foreach(array_slice($recImages, 0, 4) as $recImg)
                <div class="flex-shrink-0 w-[110px]">
                    <div class="bg-[#222] rounded-lg overflow-hidden mb-1.5">
                        <img src="{{ $recImg }}" class="w-full h-[110px] object-cover" alt="Product">
                    </div>
                    <div class="text-[10px] text-gray-300 font-medium line-clamp-2 mb-1">{{ $landingPage->headline ?: $landingPage->product->name }}</div>
                    <div class="text-xs font-bold text-[#FF2C55]">Rp{{ number_format($price, 0, ',', '.') }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Footer --}}
        <div class="bg-[#121212] px-4 py-6 text-center text-[10px] text-gray-600">
            &copy; {{ date('Y') }} shop.diginiaga.com
        </div>

        {{-- Sticky Bottom Dual Buttons --}}
        <div class="fixed bottom-0 max-w-md w-full bg-[#1a1a1a] border-t border-[#222] px-3 py-2.5 flex items-center gap-2 z-50">
            <button class="flex-1 flex justify-center items-center rounded-lg border border-[#FF2C55] text-[#FF2C55] font-bold text-sm h-11 hover:bg-[#FF2C55]/10 transition-colors">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-1"><path d="M21.68 7.56a1.908 1.908 0 0 0-.35-.66 1.71 1.71 0 0 0-.58-.46 1.85 1.85 0 0 0-.75-.19H6.17a1.82 1.82 0 0 0-.57.13l-.06-.3a1.91 1.91 0 0 0-2-1.83h-1a.75.75 0 0 0 0 1.5h1c.42 0 .49.07.57.59l1.09 5.54.54 2.78A3.86 3.86 0 0 0 7 16.89a3.76 3.76 0 0 0 1.54.75A2 2 0 0 0 8 19a2 2 0 0 0 4 0 2 2 0 0 0-.46-1.25h2.88a2 2 0 1 0 3.06-.12 3.8 3.8 0 0 0 1.46-.7 3.71 3.71 0 0 0 1.32-2.1l1.47-6.46V8.3a1.68 1.68 0 0 0-.05-.74Z"></path></svg>
                Keranjang
            </button>

            <a id="cta-btn-bottom" href="{{ route('checkout.form', ['slug' => $landingPage->slug]) }}@if(isset($utmQuery) && $utmQuery)?{{ $utmQuery }}@endif"
               class="flex-1 flex justify-center items-center rounded-lg text-white font-bold text-sm h-11 hover:opacity-90 transition-opacity"
               style="background-color: #FF2C55;">
                {{ $landingPage->cta_text ?: 'Beli Sekarang' }}
            </a>
        </div>
    </div>

    @if($globalPixel)
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $globalPixel }}&ev=PageView&noscript=1"/></noscript>
    @endif
    @if($landingPage->pixel_id)
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $landingPage->pixel_id }}&ev=PageView&noscript=1"/></noscript>
    @endif

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const header = document.getElementById('top-header');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) header.classList.add('scrolled');
            else header.classList.remove('scrolled');
        });

        const hasVariants = {{ $landingPage->product->has_variants ? 'true' : 'false' }};
        const productVariants = @json($landingPage->product->variants);
        let selectedVariantId = null;
        const ctaBtn = document.getElementById('cta-btn-bottom');
        const priceDisplay = document.getElementById('price-display');
        const variantError = document.getElementById('variant-error');
        let baseUrl = ctaBtn ? ctaBtn.href : '';
        if (baseUrl.includes('variant_id=')) baseUrl = baseUrl.replace(/([?&])variant_id=[^&]+(&|$)/, '$1').replace(/[?&]$/, '');

        if (hasVariants && ctaBtn) {
            ctaBtn.addEventListener('click', function(e) {
                if (!selectedVariantId) {
                    e.preventDefault();
                    alert('Silakan pilih variasi produk terlebih dahulu!');
                }
            });
            const selects = document.querySelectorAll('.variant-option-select');
            selects.forEach(select => { select.addEventListener('change', checkVariantCombination); });
            function checkVariantCombination() {
                let selectedOptionValueIds = [], allSelected = true;
                selects.forEach(sel => { if(sel.value) selectedOptionValueIds.push(parseInt(sel.value)); else allSelected = false; });
                if (!allSelected) { selectedVariantId = null; if(variantError) variantError.classList.add('hidden'); updateCtaUrl(); return; }
                const matchedVariant = productVariants.find(v => {
                    if(!v.option_values) return false;
                    const ids = v.option_values.map(ov => ov.id);
                    return selectedOptionValueIds.every(id => ids.includes(id)) && selectedOptionValueIds.length === ids.length;
                });
                if (matchedVariant) {
                    selectedVariantId = matchedVariant.id;
                    if(variantError) variantError.classList.add('hidden');
                    if(priceDisplay) priceDisplay.innerText = 'Rp' + new Intl.NumberFormat('id-ID').format(matchedVariant.sell_price);
                    updateCtaUrl();
                } else { selectedVariantId = null; if(variantError) variantError.classList.remove('hidden'); updateCtaUrl(); }
            }
            function updateCtaUrl() {
                if(selectedVariantId) { ctaBtn.href = baseUrl + (baseUrl.includes('?') ? '&' : '?') + 'variant_id=' + selectedVariantId; }
                else { ctaBtn.href = baseUrl; }
            }
        }

        const track = document.getElementById('hero-slider-track'), dots = document.querySelectorAll('#slider-dots div');
        const counter = document.getElementById('slider-counter');
        if (track && dots.length > 1) {
            let idx = 0;
            setInterval(() => {
                idx = (idx + 1) % dots.length;
                track.style.transform = `translateX(-${idx * 100}%)`;
                dots.forEach((d,i) => d.className = 'w-2 h-2 rounded-full ' + (i===idx ? 'bg-[#FF2C55]' : 'bg-white/40') + ' transition-all');
                if(counter) counter.innerText = `${idx+1}/${dots.length}`;
            }, 3000);
        }

        const configs = @json($landingPage->parsed_animations ?: []);
        configs.forEach(c => {
            setTimeout(() => {
                const el = document.querySelector(c.target);
                if(!el) return;
                if(c.type === 'arrow'){
                    const arr = document.createElement('div');
                    arr.className = 'anim-arrow ' + (c.direction || 'down');
                    arr.innerText = '⬇️';
                    const rect = el.getBoundingClientRect();
                    arr.style.top = (rect.top - 40) + 'px';
                    arr.style.left = (rect.left + rect.width/2 - 16) + 'px';
                    arr.style.display = 'block';
                    document.body.appendChild(arr);
                    setTimeout(() => arr.remove(), 5000);
                } else if(c.type === 'fadein'){
                    document.querySelectorAll('.anim-fadein').forEach((s, i) => { s.style.animationDelay = (i * 0.1) + 's'; });
                } else {
                    el.classList.add('anim-' + c.type);
                    setTimeout(() => el.classList.remove('anim-' + c.type), 5000);
                }
            }, c.delay || 0);
        });
    });
    </script>
</body>
</html>
