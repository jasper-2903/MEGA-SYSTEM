<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumptionHistory extends Model
{
    use HasFactory;

    protected $table = 'consumption_history';

    protected $fillable = [
        'sku',
        'item_type',
        'date',
        'qty_issued',
        'qty_sold',
    ];

    protected $casts = [
        'date' => 'date',
        'qty_issued' => 'integer',
        'qty_sold' => 'integer',
    ];

    public function scopeBySku($query, $sku)
    {
        return $query->where('sku', $sku);
    }

    public function scopeByItemType($query, $itemType)
    {
        return $query->where('item_type', $itemType);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeByPeriod($query, $period)
    {
        switch ($period) {
            case 'last_30_days':
                return $query->where('date', '>=', now()->subDays(30));
            case 'last_90_days':
                return $query->where('date', '>=', now()->subDays(90));
            case 'last_6_months':
                return $query->where('date', '>=', now()->subMonths(6));
            case 'last_year':
                return $query->where('date', '>=', now()->subYear());
            default:
                return $query;
        }
    }

    public function getTotalConsumptionAttribute()
    {
        return $this->qty_issued + $this->qty_sold;
    }

    public function getDailyAverageAttribute()
    {
        // This would typically be calculated across multiple records
        return $this->total_consumption;
    }
}