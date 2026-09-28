<div class="mb-8">
    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Lengkapi informasi dasar di sebelah kiri, dan tambahkan media atau konten ekstra di sebelah kanan.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    {{-- LEFT COLUMN: Basic Text Info --}}
    <div class="col-span-1 lg:col-span-5 space-y-6">
        
        {{-- Info Dasar --}}
        <div>
            <label class="block text-sm font-medium mb-1">Produk <span class="text-red-500">*</span></label>
            <select name="product_id" required x-model="product_id" @change="updatePrice($el)" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Pilih Produk</option>
                @foreach($products as $p)
                <option value="{{ $p->id }}" data-price="{{ $p->sell_price }}" {{ old('product_id', $landingPage->product_id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Slug <span class="text-red-500">*</span></label>
            <div class="flex items-center">
                <span class="text-gray-400 dark:text-slate-500 text-sm mr-2">{{config('app.url')}}/p/</span>
                <input type="text" name="slug" value="{{old('slug', $landingPage->slug ?? '')}}" required class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="nama-produk">
            </div>
        </div>

        {{-- Template Selection --}}
        <div class="bg-gray-50 dark:bg-slate-800/50 p-4 rounded-lg border border-gray-200 dark:border-slate-700">
            <label class="block text-sm font-medium text-gray-800 dark:text-slate-200 mb-3">Template Tampilan</label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <label class="flex flex-col items-center gap-1 p-3 bg-white dark:bg-slate-900 rounded-lg border cursor-pointer hover:border-blue-400 transition-colors" :class="template === 'shopee' ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-200 dark:border-slate-700'">
                    <input type="radio" name="template" value="shopee" x-model="template" class="sr-only" checked>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-xs" style="background: #ee4d2d">S</div>
                    <span class="text-xs font-medium text-gray-700 dark:text-slate-300">Shopee</span>
                </label>
                <label class="flex flex-col items-center gap-1 p-3 bg-white dark:bg-slate-900 rounded-lg border cursor-pointer hover:border-blue-400 transition-colors" :class="template === 'tokopedia' ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-200 dark:border-slate-700'">
                    <input type="radio" name="template" value="tokopedia" x-model="template" class="sr-only">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-xs" style="background: #03AC0E">T</div>
                    <span class="text-xs font-medium text-gray-700 dark:text-slate-300">Tokopedia</span>
                </label>
                <label class="flex flex-col items-center gap-1 p-3 bg-white dark:bg-slate-900 rounded-lg border cursor-pointer hover:border-blue-400 transition-colors" :class="template === 'blibli' ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-200 dark:border-slate-700'">
                    <input type="radio" name="template" value="blibli" x-model="template" class="sr-only">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-xs" style="background: #0095DA">B</div>
                    <span class="text-xs font-medium text-gray-700 dark:text-slate-300">Blibli</span>
                </label>
                <label class="flex flex-col items-center gap-1 p-3 bg-white dark:bg-slate-900 rounded-lg border cursor-pointer hover:border-blue-400 transition-colors" :class="template === 'tiktokshop' ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-200 dark:border-slate-700'">
                    <input type="radio" name="template" value="tiktokshop" x-model="template" class="sr-only">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-xs" style="background: #FF2C55">T</div>
                    <span class="text-xs font-medium text-gray-700 dark:text-slate-300">TikTok Shop</span>
                </label>
            </div>
        </div>

        {{-- Hero --}}
        <div>
            <label class="block text-sm font-medium text-gray-800 dark:text-slate-200 mb-2">Headline</label>
            <input type="text" name="headline" maxlength="100" x-model="headline" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Masukkan headline utama yang menarik">
            <div class="flex justify-end mt-1">
                <span class="text-[10px] font-medium text-gray-400 dark:text-slate-500 tracking-wide" x-text="Math.max(0, 100 - (headline ? headline.length : 0)) + ' characters left'"></span>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-800 dark:text-slate-200 mb-2">Subheadline</label>
            <textarea name="subheadline" rows="2" maxlength="200" x-model="subheadline" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Penjelasan singkat mendukung headline..."></textarea>
            <div class="flex justify-end mt-1">
                <span class="text-[10px] font-medium text-gray-400 dark:text-slate-500 tracking-wide" x-text="Math.max(0, 200 - (subheadline ? subheadline.length : 0)) + ' characters left'"></span>
            </div>
        </div>

        {{-- Content --}}
        <div>
            <label class="block text-sm font-medium text-gray-800 dark:text-slate-200 mb-2">Deskripsi Produk <span class="text-red-500">*</span></label>
            <textarea name="body_content" rows="6" maxlength="600" x-model="body_content" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ceritakan detail produk, masalah yang diselesaikan, dll..."></textarea>
            <div class="flex justify-end mt-1">
                <span class="text-[10px] font-medium text-gray-400 dark:text-slate-500 tracking-wide" x-text="Math.max(0, 600 - (body_content ? body_content.length : 0)) + ' characters left'"></span>
            </div>
        </div>

        {{-- List --}}
        <div>
            <label class="block text-sm font-medium text-gray-800 dark:text-slate-200 mb-2">Keunggulan / List (1 per baris)</label>
            <textarea name="list_items" rows="4" x-model="list_items" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Type to add skills relevant to this project (e.g. Bahan Premium)"></textarea>
            <div class="flex justify-end mt-1">
                <span class="text-[10px] font-medium text-gray-400 dark:text-slate-500 tracking-wide" x-text="Math.max(0, 10 - (list_items ? list_items.split('\n').filter(Boolean).length : 0)) + ' items left'"></span>
            </div>
        </div>
        
        <div class="border-t pt-4 mt-8">
            <h3 class="text-sm font-medium mb-3">Pengaturan Lainnya</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm text-gray-700 dark:text-slate-300 mb-1">Meta Pixel ID</label>
                    <input type="text" name="pixel_id" value="{{old('pixel_id', $landingPage->pixel_id ?? '')}}" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm text-gray-700 dark:text-slate-300 mb-1">Varian Nama</label>
                    <input type="text" name="variant_name" value="{{old('variant_name', $landingPage->variant_name ?? '')}}" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="pt-2">
                    <label class="flex items-center gap-x-2">
                        <input type="checkbox" name="is_active" value="1" {{ (isset($landingPage) && $landingPage->is_active) || !isset($landingPage) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700 dark:text-slate-300">Aktifkan Landing Page</span>
                    </label>
                </div>
            </div>
        </div>

    </div>

    {{-- RIGHT COLUMN: Media & Extra Content --}}
    <div class="col-span-1 lg:col-span-7">
        
        {{-- Dashed Box to Add Content --}}
        <div class="border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-xl p-8 text-center hover:bg-gray-50 dark:hover:bg-slate-800/50 dark:bg-slate-800/50 transition-colors mb-6">
            <span class="text-sm text-gray-500 dark:text-slate-400 block mb-6">Tambahkan Media & Konten Ekstra</span>
            <div class="flex flex-wrap justify-center gap-6">
                
                <button type="button" @click="toggleSection('cover')" class="flex flex-col items-center gap-2 group">
                    <div class="w-12 h-12 rounded-full border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm flex items-center justify-center text-gray-500 dark:text-slate-400 group-hover:text-blue-600 group-hover:border-blue-200 group-hover:bg-blue-50 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <span class="text-[11px] font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide">Cover</span>
                </button>
                
                <button type="button" @click="toggleSection('slider')" class="flex flex-col items-center gap-2 group">
                    <div class="w-12 h-12 rounded-full border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm flex items-center justify-center text-gray-500 dark:text-slate-400 group-hover:text-blue-600 group-hover:border-blue-200 group-hover:bg-blue-50 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </div>
                    <span class="text-[11px] font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide">Slider</span>
                </button>
                
                <button type="button" @click="toggleSection('embed')" class="flex flex-col items-center gap-2 group">
                    <div class="w-12 h-12 rounded-full border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm flex items-center justify-center text-gray-500 dark:text-slate-400 group-hover:text-blue-600 group-hover:border-blue-200 group-hover:bg-blue-50 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </div>
                    <span class="text-[11px] font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide">Video</span>
                </button>

                <button type="button" @click="toggleSection('faq')" class="flex flex-col items-center gap-2 group">
                    <div class="w-12 h-12 rounded-full border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm flex items-center justify-center text-gray-500 dark:text-slate-400 group-hover:text-blue-600 group-hover:border-blue-200 group-hover:bg-blue-50 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-[11px] font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide">FAQ</span>
                </button>

                <button type="button" @click="toggleSection('testi')" class="flex flex-col items-center gap-2 group">
                    <div class="w-12 h-12 rounded-full border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm flex items-center justify-center text-gray-500 dark:text-slate-400 group-hover:text-blue-600 group-hover:border-blue-200 group-hover:bg-blue-50 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    </div>
                    <span class="text-[11px] font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide">Testimoni</span>
                </button>

                <button type="button" @click="toggleSection('cta')" class="flex flex-col items-center gap-2 group">
                    <div class="w-12 h-12 rounded-full border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm flex items-center justify-center text-gray-500 dark:text-slate-400 group-hover:text-blue-600 group-hover:border-blue-200 group-hover:bg-blue-50 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                    </div>
                    <span class="text-[11px] font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide">CTA</span>
                </button>
            </div>
        </div>

        {{-- Expanded Sections --}}
        <div class="space-y-4">
            
            {{-- Cover Image --}}
            <div x-show="activeSections.cover" class="p-6 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-xl relative shadow-sm" style="display: none;">
                <button type="button" @click="activeSections.cover = false" class="absolute top-4 right-4 text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-400">✕</button>
                <h3 class="font-semibold text-gray-800 dark:text-slate-200 mb-4">Cover Image</h3>
                @if(isset($landingPage) && $landingPage->cover_image)
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $landingPage->cover_image) }}" class="w-32 h-20 object-cover rounded-lg border">
                </div>
                @endif
                <input type="file" name="cover_image" accept="image/*" @change="cover_image_url = $el.files[0] ? URL.createObjectURL($el.files[0]) : cover_image_url" class="py-2 px-3 block w-full border border-gray-200 dark:border-slate-700 rounded-lg text-sm">
                <p class="text-xs text-gray-400 dark:text-slate-500 mt-2">Rekomendasi ukuran: 1200x630px. Max 4MB.</p>
            </div>

            {{-- Slider Images --}}
            <div x-show="activeSections.slider" class="p-6 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-xl relative shadow-sm" style="display: none;">
                <button type="button" @click="activeSections.slider = false" class="absolute top-4 right-4 text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-400">✕</button>
                <h3 class="font-semibold text-gray-800 dark:text-slate-200 mb-4">Gambar Slider</h3>
                @if(!empty($existingSliderImages))
                <div class="mb-4 p-3 bg-gray-50 dark:bg-slate-800/50 rounded-lg border">
                    <p class="text-sm font-medium mb-2">Gambar Saat Ini:</p>
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                        @foreach($existingSliderImages as $img)
                        <img src="{{ filter_var($img, FILTER_VALIDATE_URL) ? $img : asset('storage/'.$img) }}" class="w-full h-16 object-cover rounded border">
                        @endforeach
                    </div>
                    <label class="flex items-center gap-x-2 mt-3 text-sm">
                        <input type="checkbox" name="keep_slider_images" value="1" checked class="rounded border-gray-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500">
                        Pertahankan gambar yang ada
                    </label>
                </div>
                @endif
                <input type="file" name="slider_images[]" accept="image/*" multiple @change="handleSliderUpload($el)" class="py-2 px-3 block w-full border border-gray-200 dark:border-slate-700 rounded-lg text-sm">
                <p class="text-xs text-gray-400 dark:text-slate-500 mt-2">Pilih beberapa gambar sekaligus. Upload baru akan mengganti jika checkbox di atas tidak dicentang.</p>
            </div>

            {{-- Embed / YouTube --}}
            <div x-show="activeSections.embed" class="p-6 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-xl relative shadow-sm" style="display: none;">
                <button type="button" @click="activeSections.embed = false" class="absolute top-4 right-4 text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-400">✕</button>
                <h3 class="font-semibold text-gray-800 dark:text-slate-200 mb-4">Video YouTube</h3>
                <textarea name="embed_code" rows="3" x-model="embed_code" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="https://www.youtube.com/watch?v=..."></textarea>
                <p class="text-xs text-gray-400 dark:text-slate-500 mt-2">Masukkan URL YouTube. Kode HTML dan provider lain tidak didukung.</p>
            </div>

            {{-- FAQ --}}
            <div x-show="activeSections.faq" class="p-6 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-xl relative shadow-sm" style="display: none;">
                <button type="button" @click="activeSections.faq = false" class="absolute top-4 right-4 text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-400">✕</button>
                <h3 class="font-semibold text-gray-800 dark:text-slate-200 mb-4">FAQ (Tanya Jawab)</h3>
                <div class="space-y-3 mb-4">
                    <template x-for="(faq, i) in faqs" :key="i">
                        <div class="flex gap-3 items-start bg-gray-50 dark:bg-slate-800/50 rounded-lg p-3 border">
                            <div class="flex-1 space-y-2">
                                <input type="text" x-model="faq.q" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Pertanyaan">
                                <textarea x-model="faq.a" rows="2" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Jawaban"></textarea>
                            </div>
                            <button type="button" @click="faqs.splice(i,1)" class="flex-shrink-0 py-1.5 px-2.5 text-gray-400 dark:text-slate-500 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">✕</button>
                        </div>
                    </template>
                </div>
                <button type="button" @click="faqs.push({q:'',a:''})" class="py-2 px-4 text-sm font-medium rounded-lg border border-dashed border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-800/50 dark:bg-slate-800/50 hover:border-gray-400 w-full transition-colors">
                    + Tambah Pertanyaan
                </button>
                <input type="hidden" name="faq_items" :value="JSON.stringify(faqs.filter(f => f.q && f.a))">
            </div>

            {{-- Testimoni --}}
            <div x-show="activeSections.testi" class="p-6 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-xl relative shadow-sm" style="display: none;">
                <button type="button" @click="activeSections.testi = false" class="absolute top-4 right-4 text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-400">✕</button>
                <h3 class="font-semibold text-gray-800 dark:text-slate-200 mb-4">Testimoni</h3>
                <textarea name="testimonials" rows="4" x-model="testimonials" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Budi | Pembeli | Produknya keren!&#10;Ani | Pelanggan | Puas"></textarea>
                <p class="text-xs text-gray-400 dark:text-slate-500 mt-2">Format: Nama | Role | Quote (1 testimoni per baris).</p>
            </div>

            {{-- CTA & Buttons --}}
            <div x-show="activeSections.cta" class="p-6 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-xl relative shadow-sm" style="display: none;">
                <button type="button" @click="activeSections.cta = false" class="absolute top-4 right-4 text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-400">✕</button>
                <h3 class="font-semibold text-gray-800 dark:text-slate-200 mb-4">Tombol & Aksi</h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4 bg-gray-50 dark:bg-slate-800/50 p-4 rounded-lg border border-gray-100 dark:border-slate-800">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Teks CTA Utama</label>
                            <input type="text" name="cta_text" x-model="cta_text" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Warna CTA Utama</label>
                            <div class="flex items-center gap-2">
                                <input type="color" name="cta_color" x-model="cta_color" class="h-9 w-12 border-0 rounded cursor-pointer p-0 bg-transparent">
                                <span class="text-xs text-gray-500 dark:text-slate-400" x-text="cta_color"></span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-slate-800/50 p-4 rounded-lg border border-gray-100 dark:border-slate-800">
                        <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-2">Tombol Tambahan (Opsional)</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div><input type="text" name="button_text" x-model="button_text" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm" placeholder="Teks (Cth: WA)"></div>
                            <div><input type="url" name="button_url" x-model="button_url" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm" placeholder="URL Target"></div>
                            <div class="flex items-center gap-2">
                                <input type="color" name="button_color" x-model="button_color" class="h-9 w-12 border-0 rounded cursor-pointer p-0 bg-transparent">
                                <span class="text-xs text-gray-500 dark:text-slate-400" x-text="button_color"></span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-slate-800/50 p-4 rounded-lg border border-gray-100 dark:border-slate-800">
                        <label class="block text-xs font-medium text-gray-500 dark:text-slate-400 mb-1">Scroll Target ID</label>
                        <input type="text" name="scroll_target" x-model="scroll_target" class="py-2 px-3 block w-full border-gray-200 dark:border-slate-700 rounded-lg text-sm" placeholder="cta-btn">
                        <p class="text-[10px] text-gray-400 dark:text-slate-500 mt-1">ID elemen untuk auto-scroll saat diklik.</p>
                    </div>

                    <div class="border-t pt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-3">Animasi Tampilan</label>
                        @php
                            $hasArrow = false; $hasPulse = false; $hasBounce = false; $hasShake = false; $hasFade = false;
                            $arrowDelay = 2000; $pulseDelay = 1500; $bounceDelay = 2000; $shakeDelay = 3000;
                            if (isset($animConfig)) {
                                foreach ($animConfig as $anim) {
                                    if (($anim['type'] ?? '') === 'arrow') { $hasArrow = true; $arrowDelay = $anim['delay'] ?? 2000; }
                                    if (($anim['type'] ?? '') === 'pulse') { $hasPulse = true; $pulseDelay = $anim['delay'] ?? 1500; }
                                    if (($anim['type'] ?? '') === 'bounce') { $hasBounce = true; $bounceDelay = $anim['delay'] ?? 2000; }
                                    if (($anim['type'] ?? '') === 'shake') { $hasShake = true; $shakeDelay = $anim['delay'] ?? 3000; }
                                    if (($anim['type'] ?? '') === 'fadein') { $hasFade = true; }
                                }
                            }
                        @endphp
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="flex items-start gap-2 p-2.5 bg-white dark:bg-slate-900 rounded-lg border border-gray-200 dark:border-slate-700 cursor-pointer">
                                <input type="checkbox" name="anim_arrow_cta" value="1" {{ $hasArrow ? 'checked' : '' }} class="mt-0.5 rounded border-gray-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500">
                                <div><span class="text-xs font-medium block">Panah ke CTA</span><input type="number" name="anim_arrow_cta_delay" value="{{ $arrowDelay }}" class="mt-1 py-1 px-1.5 w-16 border-gray-200 dark:border-slate-700 rounded text-[10px]"> <span class="text-[10px] text-gray-400 dark:text-slate-500">ms</span></div>
                            </label>
                            <label class="flex items-start gap-2 p-2.5 bg-white dark:bg-slate-900 rounded-lg border border-gray-200 dark:border-slate-700 cursor-pointer">
                                <input type="checkbox" name="anim_pulse_cta" value="1" {{ $hasPulse ? 'checked' : '' }} class="mt-0.5 rounded border-gray-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500">
                                <div><span class="text-xs font-medium block">Pulse (denyut)</span><input type="number" name="anim_pulse_cta_delay" value="{{ $pulseDelay }}" class="mt-1 py-1 px-1.5 w-16 border-gray-200 dark:border-slate-700 rounded text-[10px]"> <span class="text-[10px] text-gray-400 dark:text-slate-500">ms</span></div>
                            </label>
                            <label class="flex items-start gap-2 p-2.5 bg-white dark:bg-slate-900 rounded-lg border border-gray-200 dark:border-slate-700 cursor-pointer">
                                <input type="checkbox" name="anim_bounce_cta" value="1" {{ $hasBounce ? 'checked' : '' }} class="mt-0.5 rounded border-gray-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500">
                                <div><span class="text-xs font-medium block">Bounce (pantul)</span><input type="number" name="anim_bounce_cta_delay" value="{{ $bounceDelay }}" class="mt-1 py-1 px-1.5 w-16 border-gray-200 dark:border-slate-700 rounded text-[10px]"> <span class="text-[10px] text-gray-400 dark:text-slate-500">ms</span></div>
                            </label>
                            <label class="flex items-start gap-2 p-2.5 bg-white dark:bg-slate-900 rounded-lg border border-gray-200 dark:border-slate-700 cursor-pointer">
                                <input type="checkbox" name="anim_shake_cta" value="1" {{ $hasShake ? 'checked' : '' }} class="mt-0.5 rounded border-gray-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500">
                                <div><span class="text-xs font-medium block">Shake (getar)</span><input type="number" name="anim_shake_cta_delay" value="{{ $shakeDelay }}" class="mt-1 py-1 px-1.5 w-16 border-gray-200 dark:border-slate-700 rounded text-[10px]"> <span class="text-[10px] text-gray-400 dark:text-slate-500">ms</span></div>
                            </label>
                            <label class="flex items-start gap-2 p-2.5 bg-white dark:bg-slate-900 rounded-lg border border-gray-200 dark:border-slate-700 cursor-pointer sm:col-span-2">
                                <input type="checkbox" name="anim_fade_sections" value="1" {{ $hasFade ? 'checked' : '' }} class="mt-0.5 rounded border-gray-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500">
                                <span class="text-xs font-medium">Fade-in semua section</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
function lpEditor(initial) {
    return {
        step: 1,
        activeSections: {
            cover: !!initial.cover_image_url,
            slider: initial.sliderPreviews && initial.sliderPreviews.length > 0,
            embed: !!initial.embed_code,
            faq: initial.existingFaqs && initial.existingFaqs.length > 0,
            testi: !!initial.testimonials,
            cta: !!initial.cta_text || !!initial.button_text
        },
        toggleSection(section) {
            this.activeSections[section] = true; 
        },
        
        headline: initial.headline || '',
        subheadline: initial.subheadline || '',
        body_content: initial.body_content || '',
        list_items: initial.list_items || '',
        embed_code: initial.embed_code || '',
        testimonials: initial.testimonials || '',
        cta_text: initial.cta_text || 'Pesan Sekarang',
        cta_color: initial.cta_color || '#2563eb',
        button_text: initial.button_text || '',
        button_url: initial.button_url || '',
        button_color: initial.button_color || '#6b7280',
        scroll_target: initial.scroll_target || '',
        price: initial.price || 0,
        product_id: initial.product_id || '',
        template: initial.template || 'shopee',
        cover_image_url: initial.cover_image_url,
        sliderPreviews: initial.sliderPreviews || [],
        sliderIdx: 0,
        faqs: (initial.existingFaqs && initial.existingFaqs.length)
            ? initial.existingFaqs
            : [{q: '', a: ''}],

        benefits() {
            return (this.body_content || '').split('\n').map(s => s.trim()).filter(Boolean);
        },

        listItems() {
            return (this.list_items || '').split('\n').map(s => s.trim()).filter(Boolean);
        },

        testiItems() {
            return (this.testimonials || '').split('\n').map(s => s.trim()).filter(Boolean).map(line => {
                const p = line.split('|');
                return {name: (p[0]||'').trim(), role: (p[1]||'').trim(), quote: (p[2]||'').trim()};
            }).filter(t => t.name);
        },

        formatPrice(n) {
            return new Intl.NumberFormat('id-ID').format(n || 0);
        },

        updatePrice(el) {
            const opt = el.options[el.selectedIndex];
            if (opt && opt.dataset.price) {
                this.price = parseInt(opt.dataset.price) || 0;
            } else {
                this.price = 0;
            }
        },

        handleSliderUpload(el) {
            this.sliderPreviews = [];
            for (let f of el.files) {
                this.sliderPreviews.push(URL.createObjectURL(f));
            }
            this.sliderIdx = 0;
        },

        toggleFaq(i) {
            this._openFaq = this._openFaq === i ? null : i;
        },
    };
}
</script>
@endpush
