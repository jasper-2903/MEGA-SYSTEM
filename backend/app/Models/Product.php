<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'category',
        'length',
        'width',
        'height',
        'finish',
        'unit_of_measure',
        'unit_weight',
        'price',
        'lead_time_days',
        'is_active',
        'description',
        'image_url',
    ];

    protected $casts = [
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'unit_weight' => 'decimal:2',
        'price' => 'decimal:2',
        'lead_time_days' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function boms()
    {
        return $this->hasMany(Bom::class);
    }

    public function activeBom()
    {
        return $this->hasOne(Bom::class)->where('is_active', true);
    }

    public function routings()
    {
        return $this->hasMany(Routing::class);
    }

    public function activeRouting()
    {
        return $this->hasOne(Routing::class)->where('is_active', true);
    }

    public function salesOrderLines()
    {
        return $this->hasMany(SalesOrderLine::class);
    }

    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'sku', 'sku')
            ->where('item_type', 'product');
    }

    public function forecasts()
    {
        return $this->hasMany(Forecast::class, 'sku', 'sku')
            ->where('item_type', 'product');
    }

    public function consumptionHistory()
    {
        return $this->hasMany(ConsumptionHistory::class, 'sku', 'sku')
            ->where('item_type', 'product');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Attributes
    public function getDimensionsAttribute()
    {
        return $this->length . ' x ' . $this->width . ' x ' . $this->height . ' cm';
    }

    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }
}
