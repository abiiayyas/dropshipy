<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    protected $fillable = [
        'product_id',
        'slug',
        'headline',
        'subheadline',
        'body_content',
        'cover_image',
        'list_items',
        'faq_items',
        'testimonials',
        'image_slider',
        'embed_code',
        'cta_text',
        'cta_color',
        'button_text',
        'button_url',
        'button_color',
        'scroll_target',
        'animation_config',
        'pixel_id',
        'domain',
        'is_active',
        'variant_name',
        'visits',
        'template',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'visits' => 'integer',
    ];

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        $parts = parse_url($this->embed_code ?? '');
        $host = strtolower($parts['host'] ?? '');
        $path = trim($parts['path'] ?? '', '/');

        if ($host !== 'www.youtube-nocookie.com' || !str_starts_with($path, 'embed/')) {
            return null;
        }

        $videoId = substr($path, strlen('embed/'));

        return preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId)
            ? 'https://www.youtube-nocookie.com/embed/' . $videoId
            : null;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getParsedFeaturesAttribute(): array
    {
        if (!$this->body_content) return [];
        return array_filter(array_map('trim', explode("\n", $this->body_content)));
    }

    public function getParsedListItemsAttribute(): array
    {
        if (!$this->list_items) return [];
        return array_filter(array_map('trim', explode("\n", $this->list_items)));
    }

    public function getParsedSliderImagesAttribute(): array
    {
        if (!$this->image_slider) return [];
        $images = [];
        $decoded = json_decode($this->image_slider, true);
        
        $rawImages = is_array($decoded) ? $decoded : array_filter(array_map('trim', explode("\n", $this->image_slider)));
        
        foreach ($rawImages as $img) {
            $images[] = filter_var($img, FILTER_VALIDATE_URL) ? $img : asset('storage/' . $img);
        }
        
        return $images;
    }

    public function getParsedFaqsAttribute(): array
    {
        if (!$this->faq_items) return [];
        $decoded = json_decode($this->faq_items, true);
        if (is_array($decoded) && !empty($decoded) && isset($decoded[0]['q'])) {
            return $decoded;
        }
        
        $faqs = [];
        foreach (array_filter(explode("\n", $this->faq_items)) as $line) {
            $parts = explode('|', $line, 2);
            if (count($parts) === 2) {
                $faqs[] = ['q' => trim($parts[0]), 'a' => trim($parts[1])];
            }
        }
        return $faqs;
    }

    public function getParsedTestimonialsAttribute(): array
    {
        if (!$this->testimonials) return [];
        $testis = [];
        foreach (array_filter(explode("\n", $this->testimonials)) as $line) {
            $parts = explode('|', $line, 3);
            if (count($parts) >= 2) {
                $testis[] = [
                    'name' => trim($parts[0]),
                    'role' => trim($parts[1]),
                    'quote' => trim($parts[2] ?? '')
                ];
            }
        }
        return $testis;
    }

    public function getParsedAnimationsAttribute(): array
    {
        if (!$this->animation_config) return [];
        return json_decode($this->animation_config, true) ?: [];
    }
}
