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
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Floating Header Transition */
        #top-header { transition: background-color 0.3s ease, box-shadow 0.3s ease; }
        #top-header.scrolled { background-color: rgba(255, 255, 255, 0.95); box-shadow: 0 1px 3px rgba(0,0,0,0.1); backdrop-filter: blur(8px); }
        #top-header.scrolled .header-icon { background-color: transparent; color: #374151; }

        /* Animations */
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
        
        /* Embed Wrapper */
        .embed-wrapper { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; }
        .embed-wrapper iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; }

        /* Hide Alpine Cloak */
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="text-gray-800 antialiased">
    <div class="max-w-md mx-auto bg-gray-100 min-h-screen relative shadow-2xl overflow-x-hidden pb-[80px]">

        {{-- Floating Header --}}
        <header id="top-header" class="fixed top-0 max-w-md w-full z-50 flex items-center justify-end px-4 py-3">
            <div class="flex items-center gap-3">
                <button class="header-icon w-8 h-8 flex items-center justify-center rounded-full bg-black/40 text-white transition-colors" onclick="alert('Bagikan link disalin!'); navigator.clipboard.writeText(window.location.href);">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M3.092 19.78a1 1 0 0 0 .78-.57c2.36-3.81 6.57-4.82 9.95-5v2.45a1.33 1.33 0 0 0 .8 1.22 1.25 1.25 0 0 0 1.37-.28l5.1-5a2.25 2.25 0 0 0 0-3.18l-5.05-5a1.25 1.25 0 0 0-1.38-.28 1.29 1.29 0 0 0-.79 1.2v2.43c-6.78.32-11.53 4.94-11.53 11.23a.8.8 0 0 0 .55.75l.2.03Zm11.5-7.03c-3.24 0-7.44.69-10.42 3.7 1.14-4.29 5.18-7.2 10.42-7.2a.76.76 0 0 0 .75-.75V5.82l4.66 4.66a.75.75 0 0 1 0 1.06l-4.66 4.66v-2.7a.76.76 0 0 0-.75-.75Z"></path></svg>
                </button>
                <button class="header-icon w-8 h-8 flex items-center justify-center rounded-full bg-black/40 text-white transition-colors">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M21.68 7.56a1.908 1.908 0 0 0-.35-.66 1.71 1.71 0 0 0-.58-.46 1.85 1.85 0 0 0-.75-.19H6.17a1.82 1.82 0 0 0-.57.13l-.06-.3a1.91 1.91 0 0 0-2-1.83h-1a.75.75 0 0 0 0 1.5h1c.42 0 .49.07.57.59l1.09 5.54.54 2.78A3.86 3.86 0 0 0 7 16.89a3.76 3.76 0 0 0 1.54.75A2 2 0 0 0 8 19a2 2 0 0 0 4 0 2 2 0 0 0-.46-1.25h2.88a2 2 0 1 0 3.06-.12 3.8 3.8 0 0 0 1.46-.7 3.71 3.71 0 0 0 1.32-2.1l1.47-6.46V8.3a1.68 1.68 0 0 0-.05-.74Z"></path></svg>
                </button>
            </div>
        </header>

        {{-- Hero Gallery Slider --}}
        @php
            $images = count($landingPage->parsed_slider_images) ? $landingPage->parsed_slider_images : ($landingPage->cover_image ? [asset('storage/' . $landingPage->cover_image)] : []);
        @endphp
        <div class="bg-white relative w-full aspect-square overflow-hidden" id="hero-slider">
            @if(count($images))
            <div class="flex w-full h-full transition-transform duration-300" id="hero-slider-track">
                @foreach($images as $img)
                <img src="{{ $img }}" class="w-full h-full object-cover flex-shrink-0" alt="Product Image">
                @endforeach
            </div>
            @if(count($images) > 1)
            <div class="absolute bottom-4 right-4 bg-black/40 text-white text-xs px-2 py-1 rounded-full backdrop-blur-sm" id="slider-counter">
                1 / {{ count($images) }}
            </div>
            @endif
            @else
            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">Tanpa Gambar</div>
            @endif
        </div>

        {{-- Promo / Flash Sale Banner --}}
        <div class="bg-gradient-to-r from-red-600 to-orange-500 flex justify-between items-center px-4 py-3 text-white">
            <div class="flex items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path></svg>
                <span class="font-bold text-sm italic tracking-wide">FLASH SALE</span>
            </div>
            <div class="flex flex-col items-end">
                <span class="text-[10px] font-medium opacity-90 leading-tight">Berakhir dalam</span>
                <div class="flex items-center gap-1 font-bold text-sm" id="countdown-timer">
                    <div class="bg-black/20 px-1.5 py-0.5 rounded" id="cd-h">00</div><span class="text-[10px]">:</span>
                    <div class="bg-black/20 px-1.5 py-0.5 rounded" id="cd-m">00</div><span class="text-[10px]">:</span>
                    <div class="bg-black/20 px-1.5 py-0.5 rounded" id="cd-s">00</div>
                </div>
            </div>
        </div>

        @php
            $price = $landingPage->product->sell_price;
            $fakeOriginalPrice = $price * 1.5;
            $discountPercent = 33;
            $soldCount = $landingPage->orders()->count();
            $testimonials = $landingPage->parsed_testimonials ?? [];
            $reviewCount = count($testimonials);
            $ratingValue = $reviewCount > 0 ? (collect($testimonials)->avg('rating') ?? 5.0) : 0;
            $rating = $reviewCount > 0 ? number_format($ratingValue, 1) : 0;
        @endphp

        {{-- Price & Title Section --}}
        <div class="bg-white p-4 mb-2">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <div class="text-2xl font-black text-red-600" id="price-display">Rp{{ number_format($price, 0, ',', '.') }}</div>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-gray-400 line-through">Rp{{ number_format($fakeOriginalPrice, 0, ',', '.') }}</span>
                        <span class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded font-bold">{{ $discountPercent }}% OFF</span>
                    </div>
                </div>
                <div class="text-xs text-gray-500 mt-2">{{ number_format($soldCount, 0, ',', '.') }} Terjual</div>
            </div>
            <h1 class="text-[15px] font-semibold text-gray-900 leading-snug mb-3">
                {{ $landingPage->headline ?: $landingPage->product->name }}
            </h1>
            <div class="flex items-center gap-3 text-xs text-gray-600">
                <div class="flex items-center text-yellow-500">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24l5.46 4.73L5.82 21z"></path></svg>
                    <span class="ml-1 text-gray-700 font-medium">{{ $rating }}</span>
                </div>
                <div class="w-[1px] h-3 bg-gray-300"></div>
                <span>{{ $reviewCount }} Ulasan</span>
            </div>
            @if($landingPage->subheadline)
            <p class="text-sm text-gray-500 mt-3 pt-3 border-t border-gray-100">{{ $landingPage->subheadline }}</p>
            @endif
        </div>

        {{-- Variants Section --}}
        @if($landingPage->product->has_variants && count($landingPage->product->options))
        <div class="bg-white p-4 mb-2 anim-fadein">
            <h2 class="font-bold text-[15px] mb-3">Pilih Variasi</h2>
            <div id="variants-container" class="space-y-3">
                @foreach($landingPage->product->options as $option)
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">{{ $option->name }}</label>
                    <select class="variant-option-select w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 bg-gray-50" data-option-id="{{ $option->id }}">
                        <option value="">Pilih {{ $option->name }}</option>
                        @foreach($option->optionValues as $val)
                        <option value="{{ $val->id }}">{{ $val->value }}</option>
                        @endforeach
                    </select>
                </div>
                @endforeach
            </div>
            <div id="variant-error" class="text-xs text-red-500 mt-2 hidden">Kombinasi variasi tidak tersedia.</div>
        </div>
        @endif

        {{-- Store Section --}}
        <div class="bg-white p-4 mb-2 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="#9ca3af"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 4c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm0 14c-2.03 0-4.43-.82-6.14-2.88C7.55 15.8 9.68 15 12 15s4.45.8 6.14 2.12C16.43 19.18 14.03 20 12 20z"></path></svg>
                </div>
                <div>
                    <div class="font-bold text-sm text-gray-900 flex items-center gap-1">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="#10b981"><path d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5zm-2 14l-4-4 1.41-1.41L10 13.17l6.59-6.59L18 8l-8 8z"></path></svg>
                        {{ \App\Models\Setting::get("store_name", config("app.name", "Toko Resmi")) }}
                    </div>
                    <div class="text-[11px] text-gray-500">Aktif 5 menit lalu &bull; Jakarta</div>
                </div>
            </div>
            <button class="border border-green-500 text-green-600 px-3 py-1 rounded text-xs font-semibold">Ikuti</button>
        </div>

        {{-- Keunggulan --}}
        @if(count($landingPage->parsed_list_items))
        <div class="bg-white p-4 mb-2 anim-fadein">
            <h2 class="font-bold text-[15px] mb-3">Keunggulan</h2>
            <ul class="space-y-2 pl-1">
                @foreach($landingPage->parsed_list_items as $feature)
                <li class="flex items-start gap-2">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="#10b981" class="flex-shrink-0 mt-0.5"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path></svg>
                    <span class="text-sm text-gray-700 leading-relaxed">{{ $feature }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Detail Deskripsi --}}
        <div class="bg-white p-4 mb-2">
            <h2 class="font-bold text-[15px] mb-3">Detail Produk</h2>
            
            @if($landingPage->body_content)
            <div class="text-sm text-gray-700 mb-4 leading-relaxed">
                {!! nl2br(e($landingPage->body_content)) !!}
            </div>
            @endif

            @if($landingPage->youtube_embed_url)
            <div class="rounded-xl overflow-hidden mb-4 border border-gray-100">
                <div class="embed-wrapper">
                    <iframe src="{{ $landingPage->youtube_embed_url }}" title="Video Produk" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" referrerpolicy="strict-origin-when-cross-origin" sandbox="allow-scripts allow-same-origin allow-presentation" allowfullscreen></iframe>
                </div>
            </div>
            @endif
        </div>

        {{-- Testimonials --}}
        @if(count($landingPage->parsed_testimonials))
        <div class="bg-white p-4 mb-2 anim-fadein">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-bold text-[15px]">Penilaian Pembeli</h2>
                <a href="#" class="text-xs text-red-600 flex items-center">Lihat Semua <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"></path></svg></a>
            </div>
            <div class="flex gap-3 overflow-x-auto no-scrollbar pb-2">
                @foreach($landingPage->parsed_testimonials as $t)
                <div class="bg-gray-50 border border-gray-100 rounded-lg p-3 w-[240px] flex-shrink-0">
                    <div class="flex text-yellow-500 mb-1">
                        @for($i=0; $i<5; $i++) <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24l5.46 4.73L5.82 21z"></path></svg> @endfor
                    </div>
                    <div class="text-[11px] text-gray-500 mb-2 font-medium">{{ $t['name'] }} <span class="text-gray-300 mx-1">|</span> {{ $t['role'] }}</div>
                    <div class="text-xs text-gray-700 leading-relaxed line-clamp-3">"{{ $t['quote'] }}"</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- FAQ --}}
        @if(count($landingPage->parsed_faqs))
        <div class="bg-white p-4 mb-2 anim-fadein">
            <h2 class="font-bold text-[15px] mb-3">FAQ (Tanya Jawab)</h2>
            <div class="space-y-2">
                @foreach($landingPage->parsed_faqs as $i => $faq)
                <div class="border-b border-gray-100 last:border-0 pb-2 faq-item {{ $i === 0 ? 'open' : '' }}">
                    <button class="w-full text-left py-2 flex justify-between items-center focus:outline-none" onclick="this.parentElement.classList.toggle('open')">
                        <span class="text-sm font-semibold text-gray-800 pr-4">{{ $faq['q'] }}</span>
                        <svg class="arrow transform transition-transform text-gray-400 flex-shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"></path></svg>
                    </button>
                    <div class="faq-answer max-h-0 overflow-hidden transition-all duration-300 ease-in-out text-xs text-gray-600">
                        <div class="pb-2 pt-1">{{ $faq['a'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            <style>
                .faq-item.open .arrow { transform: rotate(180deg); }
                .faq-item.open .faq-answer { max-height: 300px; }
            </style>
        </div>
        @endif

        {{-- Extra spacing at the bottom --}}
        <div class="bg-white p-4 text-center text-[10px] text-gray-400 pb-10">
            &copy; {{ date('Y') }} shop.diginiaga.com
        </div>

        {{-- Fixed Bottom Bar --}}
        <div class="fixed bottom-0 max-w-md w-full bg-white border-t border-gray-200 px-3 py-2 flex items-center gap-2 z-50">


            {{-- Buy Now CTA --}}
            <a id="cta-btn" href="{{ route('checkout.form', ['slug' => $landingPage->slug]) }}@if(isset($utmQuery) && $utmQuery)?{{ $utmQuery }}@endif"
               class="flex-1 flex justify-center items-center rounded-lg text-white font-bold text-sm h-10 hover:opacity-90 transition-opacity shadow-sm"
               style="background-color: {{ $landingPage->cta_color }}"
               onclick="if(typeof fbq === 'function') fbq('track', 'AddToCart');">
                {{ $landingPage->cta_text ?: 'Beli Sekarang' }}
            </a>
        </div>
    </div>

    @if($globalPixel)
    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id={{ $globalPixel }}&ev=PageView&noscript=1"/></noscript>
    @endif
    @if($landingPage->pixel_id)
    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id={{ $landingPage->pixel_id }}&ev=PageView&noscript=1"/></noscript>
    @endif

    {{-- Script Helpers --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Header Scroll Effect
        const header = document.getElementById('top-header');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Product Variants Handling
        const hasVariants = {{ $landingPage->product->has_variants ? 'true' : 'false' }};
        const productVariants = @json($landingPage->product->variants);
        let selectedVariantId = null;
        const ctaBtn = document.getElementById('cta-btn');
        const priceDisplay = document.getElementById('price-display');
        const variantError = document.getElementById('variant-error');
        
        let baseUrl = ctaBtn ? ctaBtn.href : '';
        // Bersihkan ?variant_id jika kebetulan sudah ada di URL dasar
        if (baseUrl.includes('variant_id=')) {
            baseUrl = baseUrl.replace(/([?&])variant_id=[^&]+(&|$)/, '$1').replace(/[?&]$/, '');
        }

        if (hasVariants && ctaBtn) {
            ctaBtn.addEventListener('click', function(e) {
                if (!selectedVariantId) {
                    e.preventDefault();
                    alert('Silakan pilih variasi produk terlebih dahulu!');
                    const variantsContainer = document.getElementById('variants-container');
                    if (variantsContainer) {
                        variantsContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });

            const selects = document.querySelectorAll('.variant-option-select');
            selects.forEach(select => {
                select.addEventListener('change', checkVariantCombination);
            });

            function checkVariantCombination() {
                let selectedOptionValueIds = [];
                let allSelected = true;

                selects.forEach(sel => {
                    if (sel.value) {
                        selectedOptionValueIds.push(parseInt(sel.value));
                    } else {
                        allSelected = false;
                    }
                });

                if (!allSelected) {
                    selectedVariantId = null;
                    if(variantError) variantError.classList.add('hidden');
                    updateCtaUrl();
                    return;
                }

                const matchedVariant = productVariants.find(variant => {
                    if (!variant.option_values) return false;
                    const variantOptionIds = variant.option_values.map(ov => ov.id);
                    return selectedOptionValueIds.every(id => variantOptionIds.includes(id)) && 
                           selectedOptionValueIds.length === variantOptionIds.length;
                });

                if (matchedVariant) {
                    selectedVariantId = matchedVariant.id;
                    if(variantError) variantError.classList.add('hidden');
                    if(priceDisplay) priceDisplay.innerText = 'Rp' + new Intl.NumberFormat('id-ID').format(matchedVariant.sell_price);
                    updateCtaUrl();
                } else {
                    selectedVariantId = null;
                    if(variantError) variantError.classList.remove('hidden');
                    updateCtaUrl();
                }
            }

            function updateCtaUrl() {
                if (selectedVariantId) {
                    const separator = baseUrl.includes('?') ? '&' : '?';
                    ctaBtn.href = baseUrl + separator + 'variant_id=' + selectedVariantId;
                } else {
                    ctaBtn.href = baseUrl;
                }
            }
        }

        // 2. Slider Logic
        const track = document.getElementById('hero-slider-track');
        if (track) {
            const count = track.children.length;
            if (count > 1) {
                let idx = 0;
                const counter = document.getElementById('slider-counter');
                setInterval(() => {
                    idx = (idx + 1) % count;
                    track.style.transform = `translateX(-${idx * 100}%)`;
                    counter.innerText = `${idx + 1} / ${count}`;
                }, 3000);
            }
        }

        // 3. Fake Countdown Timer (Ends at midnight today)
        function updateCountdown() {
            const now = new Date();
            const endOfDay = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
            const diff = endOfDay - now;
            
            if (diff <= 0) return;
            
            const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);
            
            document.getElementById('cd-h').innerText = h.toString().padStart(2, '0');
            document.getElementById('cd-m').innerText = m.toString().padStart(2, '0');
            document.getElementById('cd-s').innerText = s.toString().padStart(2, '0');
        }
        setInterval(updateCountdown, 1000);
        updateCountdown();

        // 4. Custom Animations handling
        const configs = @json($landingPage->parsed_animations ?: []);
        configs.forEach(c => {
            setTimeout(() => {
                const targetId = c.target;
                const el = document.querySelector(targetId);
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
                    document.querySelectorAll('.anim-fadein').forEach((s, i) => {
                        s.style.animationDelay = (i * 0.1) + 's';
                    });
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
