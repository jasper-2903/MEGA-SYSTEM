<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'item_type',
        'horizon_start',
        'horizon_end',
        'method',
        'forecast_qty',
        'mad',
        'is_active',
    ];

    protected $casts = [
        'horizon_start' => 'date',
        'horizon_end' => 'date',
        'forecast_qty' => 'integer',
        'mad' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySku($query, $sku)
    {
        return $query->where('sku', $sku);
    }

    public function scopeByItemType($query, $itemType)
    {
        return $query->where('item_type', $itemType);
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('method', $method);
    }

    public function scopeByHorizon($query, $startDate, $endDate)
    {
        return $query->where('horizon_start', '>=', $startDate)
                    ->where('horizon_end', '<=', $endDate);
    }

    public function getConfidenceLevelAttribute()
    {
        if (!$this->mad || $this->forecast_qty == 0) {
            return 'Low';
        }

        $coefficient = $this->mad / $this->forecast_qty;

        if ($coefficient <= 0.1) {
            return 'High';
        } elseif ($coefficient <= 0.25) {
            return 'Medium';
        } else {
            return 'Low';
        }
    }

    public function getFormattedHorizonAttribute()
    {
        return $this->horizon_start->format('M d') . ' - ' . $this->horizon_end->format('M d, Y');
    }
}