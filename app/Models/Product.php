<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku_supplier',
        'description',
        'sell_price',
        'cost_price',
        'images',
        'allowed_couriers',
        'is_active',
        'has_variants',
        'warehouse_id',
    ];

    protected $casts = [
        'images' => 'array',
        'allowed_couriers' => 'array',
        'sell_price' => 'integer',
        'cost_price' => 'integer',
        'is_active' => 'boolean',
        'has_variants' => 'boolean',
    ];

    public function landingPages()
    {
        return $this->hasMany(LandingPage::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
