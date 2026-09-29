@extends('layouts.admin')

@section('title', 'Buat LP')

@section('content')
@php
$lpData = [
    'product_id' => old('product_id', ''),
    'headline' => old('headline'),
    'subheadline' => old('subheadline'),
    'body_content' => old('body_content'),
    'list_items' => old('list_items'),
    'embed_code' => old('embed_code'),
    'testimonials' => old('testimonials'),
    'cta_text' => old('cta_text', 'Pesan Sekarang'),
    'cta_color' => old('cta_color', '#2563eb'),
    'button_text' => old('button_text'),
    'button_url' => old('button_url'),
    'button_color' => old('button_color', '#6b7280'),
    'scroll_target' => old('scroll_target'),
    'price' => old('product_id') ? \App\Models\Product::find(old('product_id'))?->sell_price ?? 0 : 0,
    'cover_image_url' => null,
    'sliderPreviews' => [],
    'existingFaqs' => [],
    'template' => 'shopee',
];
@endphp
<div x-data='lpEditor(@json($lpData))' class="h-[calc(100vh-140px)]">

    {{-- STEP 1: Form Editor --}}
    <div x-show="step === 1" class="h-full">
        <form id="lpForm" action="{{ route('admin.landing-pages.store') }}" method="POST" enctype="multipart/form-data" class="h-full flex flex-col relative pb-24">
            @csrf
            
            <div class="flex-1 overflow-y-auto">
                <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-xl shadow-sm p-6 lg:p-8">
                    @include('admin.landing-pages.partials._form')
                </div>
            </div>

            {{-- Sticky Footer --}}
            <div class="fixed bottom-0 left-0 right-0 lg:left-64 bg-white dark:bg-slate-900 border-t px-6 py-4 flex justify-between items-center z-50">
                <a href="{{ route('admin.landing-pages.index') }}" class="py-2.5 px-4 rounded-lg text-sm font-medium bg-white dark:bg-slate-900 text-gray-600 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-800/50 dark:bg-slate-800/50 transition-colors">Batal</a>
                <div class="flex gap-3">
                    <button type="submit" name="is_active" value="0" class="py-2.5 px-4 rounded-lg text-sm font-medium text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-slate-100 transition-colors">Save as draft</button>
                    <button type="button" @click="step = 2; window.scrollTo(0,0);" class="py-2.5 px-6 rounded-lg text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 transition-colors">Next: Preview</button>
                </div>
            </div>
        </form>
    </div>

    {{-- STEP 2: Preview --}}
    <div x-show="step === 2" style="display: none;" class="h-full flex flex-col pb-24">
        <div class="flex-1 overflow-y-auto flex justify-center pb-8">
            <div class="w-full max-w-sm sm:max-w-md md:max-w-lg lg:max-w-2xl xl:max-w-4xl bg-white dark:bg-slate-900 shadow-xl rounded-xl overflow-hidden shrink-0 mt-6 border">
                @include('admin.landing-pages.partials._preview')
            </div>
        </div>

        {{-- Preview Footer --}}
        <div class="fixed bottom-0 left-0 right-0 lg:left-64 bg-white dark:bg-slate-900 border-t px-6 py-4 flex justify-end gap-3 z-50">
            <button type="button" @click="step = 1" class="py-2.5 px-4 rounded-lg text-sm font-medium bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800/50 dark:bg-slate-800/50 transition-colors">Kembali Edit</button>
            <button type="button" @click="document.getElementById('lpForm').requestSubmit()" class="py-2.5 px-6 rounded-lg text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 transition-colors">Simpan & Terbitkan</button>
        </div>
    </div>
</div>
@endsection
