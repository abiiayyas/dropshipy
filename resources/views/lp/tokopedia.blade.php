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
        body { font-family: 'Inter', sans-serif; background-color: #f5f5f5; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        #top-header { transition: background-color 0.3s ease, box-shadow 0.3s ease; }
        #top-header.scrolled { background-color: rgba(255, 255, 255, 0.95); box-shadow: 0 1px 3px rgba(0,0,0,0.1); backdrop-filter: blur(8px); }
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
        .spesifikasi-table td { padding: 8px 12px; }
        .spesifikasi-table tr:nth-child(odd) { background: #f9fafb; }
    </style>
</head>
<body class="text-gray-800 antialiased">
    <div class="max-w-md mx-auto bg-white min-h-screen relative shadow-2xl overflow-x-hidden pb-[90px]">

        {{-- Tokopedia-style Header --}}
        <header id="top-header" class="fixed top-0 max-w-md w-full z-50 flex items-center justify-between px-3 py-3 bg-white border-b border-gray-100">
            <button onclick="history.back()" class="w-8 h-8 flex items-center justify-center text-gray-600">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"></path></svg>
            </button>
            <div class="flex items-center gap-3">
                <button class="w-8 h-8 flex items-center justify-center text-gray-600">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="M21 21l-4.35-4.35"></path></svg>
                </button>
                <button class="w-8 h-8 flex items-center justify-center text-gray-600 relative">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M21.68 7.56a1.908 1.908 0 0 0-.35-.66 1.71 1.71 0 0 0-.58-.46 1.85 1.85 0 0 0-.75-.19H6.17a1.82 1.82 0 0 0-.57.13l-.06-.3a1.91 1.91 0 0 0-2-1.83h-1a.75.75 0 0 0 0 1.5h1c.42 0 .49.07.57.59l1.09 5.54.54 2.78A3.86 3.86 0 0 0 7 16.89a3.76 3.76 0 0 0 1.54.75A2 2 0 0 0 8 19a2 2 0 0 0 4 0 2 2 0 0 0-.46-1.25h2.88a2 2 0 1 0 3.06-.12 3.8 3.8 0 0 0 1.46-.7 3.71 3.71 0 0 0 1.32-2.1l1.47-6.46V8.3a1.68 1.68 0 0 0-.05-.74Z"></path></svg>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[8px] w-4 h-4 rounded-full flex items-center justify-center">0</span>
                </button>
                <button class="w-8 h-8 flex items-center justify-center text-gray-600" onclick="alert('Link disalin!'); navigator.clipboard.writeText(window.location.href);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M3.092 19.78a1 1 0 0 0 .78-.57c2.36-3.81 6.57-4.82 9.95-5v2.45a1.33 1.33 0 0 0 .8 1.22 1.25 1.25 0 0 0 1.37-.28l5.1-5a2.25 2.25 0 0 0 0-3.18l-5.05-5a1.25 1.25 0 0 0-1.38-.28 1.29 1.29 0 0 0-.79 1.2v2.43c-6.78.32-11.53 4.94-11.53 11.23a.8.8 0 0 0 .55.75l.2.03Z"></path></svg>
                </button>
            </div>
        </header>

        {{-- Image Gallery Slider --}}
        @php
            $images = count($landingPage->parsed_slider_images) ? $landingPage->parsed_slider_images : ($landingPage->cover_image ? [asset('storage/' . $landingPage->cover_image)] : []);
        @endphp
        <div class="bg-gray-100 relative w-full aspect-square overflow-hidden mt-0" id="hero-slider">
            @if(count($images))
            <div class="flex w-full h-full transition-transform duration-400" id="hero-slider-track">
                @foreach($images as $img)
                <img src="{{ $img }}" class="w-full h-full object-contain flex-shrink-0 bg-white" alt="Product Image">
                @endforeach
            </div>
            @if(count($images) > 1)
            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5" id="slider-dots">
                @foreach($images as $i => $_)
                <div class="w-2 h-2 rounded-full {{ $i === 0 ? 'bg-green-500' : 'bg-gray-300' }} transition-colors"></div>
                @endforeach
            </div>
            @endif
            @else
            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">Tanpa Gambar</div>
            @endif
        </div>

        @php
            $price = $landingPage->product->sell_price;
            $fakeOriginalPrice = $price * 1.5;
            $discountPercent = 33;
            $rating = 4.9;
            $soldCount = 1245 + ($landingPage->orders_count ?? 0);
        @endphp

        {{-- Product Info --}}
        <div class="px-4 pt-3 pb-2">
            <h1 class="text-[15px] font-bold text-gray-900 leading-snug mb-2">
                {{ $landingPage->headline ?: $landingPage->product->name }}
            </h1>
            @if($landingPage->subheadline)
            <p class="text-xs text-gray-500 mb-2">{{ $landingPage->subheadline }}</p>
            @endif

            {{-- Price Row --}}
            <div class="flex items-end gap-2 mb-2">
                <span class="text-2xl font-black text-[#03AC0E]" id="price-display">Rp{{ number_format($price, 0, ',', '.') }}</span>
                <span class="text-xs text-gray-400 line-through mb-1">Rp{{ number_format($fakeOriginalPrice, 0, ',', '.') }}</span>
                <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-bold mb-1">{{ $discountPercent }}%</span>
            </div>

            {{-- Rating & Sold --}}
            <div class="flex items-center gap-3 text-xs text-gray-500 mb-3">
                <div class="flex items-center text-yellow-500 gap-0.5">
                    @for($i=0; $i<5; $i++)
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24l5.46 4.73L5.82 21z"></path></svg>
                    @endfor
                </div>
                <span class="font-semibold text-gray-700">{{ $rating }}</span>
                <span>|</span>
                <span>{{ number_format($soldCount, 0, ',', '.') }} Terjual</span>
                <span>|</span>
                <span>Diskusi (12)</span>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-2 mb-2">
                <a id="cta-btn" href="{{ route('checkout.form', ['slug' => $landingPage->slug]) }}@if(isset($utmQuery) && $utmQuery)?{{ $utmQuery }}@endif"
                   class="flex-1 flex justify-center items-center rounded-lg text-white font-bold text-sm h-11 hover:opacity-90 transition-opacity"
                   style="background-color: #03AC0E;">
                    Beli Sekarang
                </a>
                <button class="flex-1 flex justify-center items-center rounded-lg border-2 border-[#03AC0E] text-[#03AC0E] font-bold text-sm h-11 hover:bg-green-50 transition-colors">
                    + Keranjang
                </button>
            </div>
        </div>

        <div class="h-2 bg-gray-100"></div>

        {{-- Variants Section --}}
        @if($landingPage->product->has_variants && count($landingPage->product->options))
        <div class="px-4 py-3 anim-fadein">
            <h2 class="font-bold text-[14px] mb-3">Pilih Variasi</h2>
            <div id="variants-container" class="space-y-3">
                @foreach($landingPage->product->options as $option)
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">{{ $option->name }}</label>
                    <select class="variant-option-select w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 bg-gray-50" data-option-id="{{ $option->id }}">
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
        <div class="h-2 bg-gray-100"></div>
        @endif

        {{-- Store Info --}}
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center overflow-hidden border border-gray-200">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="#9ca3af"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 4c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm0 14c-2.03 0-4.43-.82-6.14-2.88C7.55 15.8 9.68 15 12 15s4.45.8 6.14 2.12C16.43 19.18 14.03 20 12 20z"></path></svg>
                    </div>
                    <div>
                        <div class="font-bold text-sm text-gray-900">{{ \App\Models\Setting::get("store_name", config("app.name", "Toko Resmi")) }}</div>
                        <div class="flex items-center gap-1 mt-0.5">
                            <div class="flex text-yellow-500">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24l5.46 4.73L5.82 21z"></path></svg>
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24l5.46 4.73L5.82 21z"></path></svg>
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24l5.46 4.73L5.82 21z"></path></svg>
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24l5.46 4.73L5.82 21z"></path></svg>
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24l5.46 4.73L5.82 21z"></path></svg>
                            </div>
                            <span class="text-[10px] text-gray-500">| Jakarta</span>
                        </div>
                    </div>
                </div>
                <button class="border border-[#03AC0E] text-[#03AC0E] px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1 hover:bg-green-50">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"></path></svg>
                    Chat
                </button>
            </div>
        </div>

        <div class="h-2 bg-gray-100"></div>

        {{-- Keunggulan / Fitur Unggulan --}}
        @if(count($landingPage->parsed_list_items))
        <div class="px-4 py-3 anim-fadein">
            <h2 class="font-bold text-[14px] mb-3">Fitur Unggulan</h2>
            <ul class="space-y-2.5">
                @foreach($landingPage->parsed_list_items as $feature)
                <li class="flex items-start gap-2">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="#03AC0E" class="flex-shrink-0 mt-0.5"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path></svg>
                    <span class="text-sm text-gray-700">{{ $feature }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        <div class="h-2 bg-gray-100"></div>
        @endif

        {{-- Spesifikasi --}}
        @if($landingPage->body_content)
        @php
            $specLines = array_filter(array_map('trim', explode("\n", $landingPage->body_content)));
        @endphp
        @if(count($specLines))
        <div class="px-4 py-3">
            <h2 class="font-bold text-[14px] mb-3">Spesifikasi</h2>
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <table class="w-full text-sm spesifikasi-table">
                    @foreach($specLines as $line)
                    @php $parts = explode(':', $line, 2); if(count($parts) === 2) $parts = [$parts[0], $parts[1]]; else $parts = [$line, '']; @endphp
                    <tr>
                        <td class="text-gray-500 font-medium w-2/5">{{ trim($parts[0]) }}</td>
                        <td class="text-gray-900">{{ trim($parts[1] ?? '') }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <div class="h-2 bg-gray-100"></div>
        @endif
        @endif

        {{-- Deskripsi Produk --}}
        @if($landingPage->body_content)
        <div class="px-4 py-3">
            <h2 class="font-bold text-[14px] mb-3">Deskripsi Produk</h2>
            <div class="text-sm text-gray-700 leading-relaxed">
                {!! nl2br(e($landingPage->body_content)) !!}
            </div>
        </div>
        @endif

        {{-- Embed Video --}}
        @if($landingPage->youtube_embed_url)
        <div class="px-4 py-3">
            <h2 class="font-bold text-[14px] mb-3">Video Produk</h2>
            <div class="rounded-xl overflow-hidden border border-gray-100">
                <div class="embed-wrapper">
                    <iframe src="{{ $landingPage->youtube_embed_url }}" title="Video Produk" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" referrerpolicy="strict-origin-when-cross-origin" sandbox="allow-scripts allow-same-origin allow-presentation" allowfullscreen></iframe>
                </div>
            </div>
        </div>
        <div class="h-2 bg-gray-100"></div>
        @endif

        {{-- FAQ --}}
        @if(count($landingPage->parsed_faqs))
        <div class="px-4 py-3 anim-fadein">
            <h2 class="font-bold text-[14px] mb-3">FAQ (Tanya Jawab)</h2>
            <div class="space-y-2">
                @foreach($landingPage->parsed_faqs as $i => $faq)
                <div class="border rounded-lg border-gray-200 overflow-hidden faq-item {{ $i === 0 ? 'open' : '' }}">
                    <button class="w-full text-left px-3 py-2.5 flex justify-between items-center focus:outline-none bg-gray-50 hover:bg-gray-100 transition-colors" onclick="this.parentElement.classList.toggle('open')">
                        <span class="text-sm font-semibold text-gray-800 pr-4">{{ $faq['q'] }}</span>
                        <svg class="arrow transform transition-transform text-gray-400 flex-shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"></path></svg>
                    </button>
                    <div class="faq-answer max-h-0 overflow-hidden transition-all duration-300 ease-in-out text-xs text-gray-600">
                        <div class="px-3 pb-3 pt-2">{{ $faq['a'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            <style>
                .faq-item.open .arrow { transform: rotate(180deg); }
                .faq-item.open .faq-answer { max-height: 300px; }
            </style>
        </div>
        <div class="h-2 bg-gray-100"></div>
        @endif

        {{-- Ulasan Pembeli --}}
        @if(count($landingPage->parsed_testimonials))
        <div class="px-4 py-3 anim-fadein">
            <h2 class="font-bold text-[14px] mb-3">Ulasan Pembeli</h2>
            <div class="space-y-3">
                @foreach($landingPage->parsed_testimonials as $t)
                <div class="border border-gray-200 rounded-lg p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-bold text-xs">
                            {{ substr($t['name'], 0, 1) }}
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-800">{{ $t['name'] }}</div>
                            <div class="flex text-yellow-500">
                                @for($i=0; $i<5; $i++) <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24l5.46 4.73L5.82 21z"></path></svg> @endfor
                            </div>
                        </div>
                        <span class="ml-auto text-[10px] text-gray-400">{{ $t['role'] }}</span>
                    </div>
                    <p class="text-xs text-gray-600 leading-relaxed">{{ $t['quote'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
        <div class="h-2 bg-gray-100"></div>
        @endif

        {{-- Footer --}}
        <div class="px-4 py-6 text-center text-[10px] text-gray-400">
            &copy; {{ date('Y') }} shop.diginiaga.com
        </div>

        {{-- Fixed Bottom Bar --}}
        <div class="fixed bottom-0 max-w-md w-full bg-white border-t border-gray-200 px-4 py-2.5 flex items-center gap-3 z-50">
            @if($landingPage->button_url)
            <a href="{{ $landingPage->button_url }}" target="_blank" rel="noopener" class="flex flex-col items-center justify-center w-10 flex-shrink-0 text-[#03AC0E] hover:bg-green-50 rounded-lg py-1 transition-colors">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.83 3.08 1.27 4.79 1.27 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm5.46 14.15c-.23.65-1.33 1.25-1.84 1.34-.48.09-1.12.15-3.32-.76-2.66-1.1-4.38-3.83-4.51-4.01-.14-.18-1.08-1.44-1.08-2.75 0-1.31.68-1.96.93-2.24.25-.28.54-.35.73-.35.18 0 .36 0 .52.01.17.01.39-.06.6.45.22.54.73 1.8.8 1.94.07.15.11.32.02.5-.09.18-.14.3-.27.45-.14.14-.29.31-.41.43-.14.14-.29.29-.13.56.16.27.7 1.15 1.5 1.87.97.87 1.83 1.14 2.1 1.28.27.14.43.12.59-.06.16-.18.68-.8.87-1.07.18-.27.37-.23.62-.13.25.1 1.62.77 1.9 1.05.28.27.47.43.54.67.07.24.07 1.38-.16 2.03z"></path></svg>
                <span class="text-[8px] font-medium mt-0.5">Chat</span>
            </a>
            @endif

            <a id="cta-btn" href="{{ route('checkout.form', ['slug' => $landingPage->slug]) }}@if(isset($utmQuery) && $utmQuery)?{{ $utmQuery }}@endif"
               class="flex-1 flex justify-center items-center rounded-lg text-white font-bold text-sm h-10 hover:opacity-90 transition-opacity shadow-sm"
               style="background-color: {{ $landingPage->cta_color }}"
               onclick="if(typeof fbq === 'function') fbq('track', 'AddToCart');">
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
        if (track && dots.length > 1) {
            let idx = 0;
            setInterval(() => {
                idx = (idx + 1) % dots.length;
                track.style.transform = `translateX(-${idx * 100}%)`;
                dots.forEach((d,i) => d.className = 'w-2 h-2 rounded-full ' + (i===idx ? 'bg-green-500' : 'bg-gray-300') + ' transition-colors');
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
