<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class LandingPageController extends Controller
{
    public function index()
    {
        $landingPages = LandingPage::with(['product'])
            ->withCount('orders')
            ->latest()
            ->paginate(20);

        return view('admin.landing-pages.index', compact('landingPages'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        return view('admin.landing-pages.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'slug' => 'required|string|max:255|unique:landing_pages,slug',
            'headline' => 'nullable|string|max:255',
            'subheadline' => 'nullable|string|max:255',
            'body_content' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'list_items' => 'nullable|string',
            'faq_items' => 'nullable|string',
            'testimonials' => 'nullable|string',
            'slider_images' => 'nullable|array',
            'slider_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'embed_code' => 'nullable|string',
            'cta_text' => 'nullable|string|max:100',
            'cta_color' => 'nullable|string|max:20',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:500',
            'button_color' => 'nullable|string|max:20',
            'scroll_target' => 'nullable|string|max:100',
            'pixel_id' => 'nullable|string|max:100',
            'domain' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'variant_name' => 'nullable|string|max:255',
            'template' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('cover_image')) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('cover_image')->getRealPath());
            $image->scaleDown(width: 1200);
            $filename = 'landing-pages/' . Str::random(40) . '.webp';
            Storage::disk('public')->put($filename, (string) $image->toWebp(80));
            $validated['cover_image'] = $filename;
        }

        if ($request->hasFile('slider_images')) {
            $paths = [];
            $manager = new ImageManager(new Driver());
            foreach ($request->file('slider_images') as $file) {
                $image = $manager->read($file->getRealPath());
                $image->scaleDown(width: 1000);
                $filename = 'landing-pages/slider/' . Str::random(40) . '.webp';
                Storage::disk('public')->put($filename, (string) $image->toWebp(80));
                $paths[] = $filename;
            }
            $validated['image_slider'] = json_encode($paths);
        } else {
            $validated['image_slider'] = null;
        }

        $validated['faq_items'] = $this->buildFaqJson($request);
        $validated['animation_config'] = $this->buildAnimationJson($request);

        $validated['cta_text'] = $validated['cta_text'] ?? 'Pesan Sekarang';
        $validated['cta_color'] = $validated['cta_color'] ?? '#2563eb';

        LandingPage::create($validated);

        return redirect()->route('admin.landing-pages.index')
            ->with('success', 'Landing page berhasil dibuat.');
    }

    public function edit(LandingPage $landingPage)
    {
        $products = Product::where('is_active', true)->get();

        $existingFaqs = [];
        if ($landingPage->faq_items) {
            $decoded = json_decode($landingPage->faq_items, true);
            if (is_array($decoded)) {
                $existingFaqs = $decoded;
            } else {
                foreach (array_filter(explode("\n", $landingPage->faq_items)) as $line) {
                    $parts = explode('|', $line, 2);
                    if (count($parts) === 2) {
                        $existingFaqs[] = ['q' => trim($parts[0]), 'a' => trim($parts[1])];
                    }
                }
            }
        }

        $existingSliderImages = [];
        if ($landingPage->image_slider) {
            $decoded = json_decode($landingPage->image_slider, true);
            if (is_array($decoded)) {
                $existingSliderImages = $decoded;
            } else {
                $existingSliderImages = array_filter(array_map('trim', explode("\n", $landingPage->image_slider)));
            }
        }

        $animConfig = [];
        if ($landingPage->animation_config) {
            $animConfig = json_decode($landingPage->animation_config, true) ?: [];
        }

        return view('admin.landing-pages.edit', compact(
            'landingPage', 'products', 'existingFaqs', 'existingSliderImages', 'animConfig'
        ));
    }

    public function update(Request $request, LandingPage $landingPage)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'slug' => 'required|string|max:255|unique:landing_pages,slug,' . $landingPage->id,
            'headline' => 'nullable|string|max:255',
            'subheadline' => 'nullable|string|max:255',
            'body_content' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'list_items' => 'nullable|string',
            'faq_items' => 'nullable|string',
            'testimonials' => 'nullable|string',
            'slider_images' => 'nullable|array',
            'slider_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'embed_code' => 'nullable|string',
            'cta_text' => 'nullable|string|max:100',
            'cta_color' => 'nullable|string|max:20',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:500',
            'button_color' => 'nullable|string|max:20',
            'scroll_target' => 'nullable|string|max:100',
            'pixel_id' => 'nullable|string|max:100',
            'domain' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'variant_name' => 'nullable|string|max:255',
            'template' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($landingPage->cover_image) {
                Storage::disk('public')->delete($landingPage->cover_image);
            }
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('cover_image')->getRealPath());
            $image->scaleDown(width: 1200);
            $filename = 'landing-pages/' . Str::random(40) . '.webp';
            Storage::disk('public')->put($filename, (string) $image->toWebp(80));
            $validated['cover_image'] = $filename;
        }

        if ($request->hasFile('slider_images')) {
            if ($landingPage->image_slider) {
                $old = json_decode($landingPage->image_slider, true);
                if (is_array($old)) {
                    foreach ($old as $oldPath) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
            }
            $paths = [];
            $manager = new ImageManager(new Driver());
            foreach ($request->file('slider_images') as $file) {
                $image = $manager->read($file->getRealPath());
                $image->scaleDown(width: 1000);
                $filename = 'landing-pages/slider/' . Str::random(40) . '.webp';
                Storage::disk('public')->put($filename, (string) $image->toWebp(80));
                $paths[] = $filename;
            }
            $validated['image_slider'] = json_encode($paths);
        } elseif ($request->has('keep_slider_images')) {
            $validated['image_slider'] = $landingPage->image_slider;
        } else {
            if ($landingPage->image_slider) {
                $old = json_decode($landingPage->image_slider, true);
                if (is_array($old)) {
                    foreach ($old as $oldPath) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
            }
            $validated['image_slider'] = null;
        }

        $validated['faq_items'] = $this->buildFaqJson($request);
        $validated['animation_config'] = $this->buildAnimationJson($request);

        $validated['cta_text'] = $validated['cta_text'] ?? 'Pesan Sekarang';
        $validated['cta_color'] = $validated['cta_color'] ?? '#2563eb';

        $landingPage->update($validated);

        return redirect()->route('admin.landing-pages.index')
            ->with('success', 'Landing page berhasil diperbarui.');
    }

    public function toggle(LandingPage $landingPage)
    {
        $landingPage->update(['is_active' => !$landingPage->is_active]);

        return back()->with('success', 'Status landing page berhasil diubah.');
    }

    public function destroy(LandingPage $landingPage)
    {
        if ($landingPage->cover_image) {
            Storage::disk('public')->delete($landingPage->cover_image);
        }

        if ($landingPage->image_slider) {
            $old = json_decode($landingPage->image_slider, true);
            if (is_array($old)) {
                foreach ($old as $oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        }

        $landingPage->delete();

        return redirect()->route('admin.landing-pages.index')
            ->with('success', 'Landing page berhasil dihapus.');
    }

    public function stats(LandingPage $landingPage)
    {
        $stats = [
            'total_orders' => $landingPage->orders()->count(),
            'total_revenue' => $landingPage->orders()->where('payment_status', 'paid')->sum('total_amount'),
            'conversion_rate' => $landingPage->visits > 0
                ? round(($landingPage->orders()->count() / $landingPage->visits) * 100, 1)
                : 0,
            'orders_today' => $landingPage->orders()->whereDate('created_at', today())->count(),
            'revenue_today' => $landingPage->orders()->whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
        ];

        return response()->json($stats);
    }

    private function buildFaqJson(Request $request): ?string
    {
        if ($request->has('faq_items') && !empty($request->input('faq_items'))) {
            $decoded = json_decode($request->input('faq_items'), true);
            if (is_array($decoded) && !empty($decoded)) {
                $faqs = [];
                foreach ($decoded as $item) {
                    if (!empty($item['q']) && !empty($item['a'])) {
                        $faqs[] = ['q' => $item['q'], 'a' => $item['a']];
                    }
                }
                return !empty($faqs) ? json_encode($faqs) : null;
            }
        }
        return null;
    }

    private function buildAnimationJson(Request $request): ?string
    {
        $animations = [];

        if ($request->boolean('anim_arrow_cta')) {
            $animations[] = [
                'type' => 'arrow',
                'target' => '#cta-btn',
                'direction' => 'down',
                'delay' => (int) ($request->input('anim_arrow_cta_delay', 2000)),
            ];
        }
        if ($request->boolean('anim_pulse_cta')) {
            $animations[] = [
                'type' => 'pulse',
                'target' => '#cta-btn',
                'delay' => (int) ($request->input('anim_pulse_cta_delay', 1500)),
            ];
        }
        if ($request->boolean('anim_bounce_cta')) {
            $animations[] = [
                'type' => 'bounce',
                'target' => '#cta-btn',
                'delay' => (int) ($request->input('anim_bounce_cta_delay', 2000)),
            ];
        }
        if ($request->boolean('anim_shake_cta')) {
            $animations[] = [
                'type' => 'shake',
                'target' => '#cta-btn',
                'delay' => (int) ($request->input('anim_shake_cta_delay', 3000)),
            ];
        }
        if ($request->boolean('anim_fade_sections')) {
            $animations[] = [
                'type' => 'fadein',
                'target' => '.anim-fadein',
                'delay' => 0,
            ];
        }

        return !empty($animations) ? json_encode($animations) : null;
    }
}
