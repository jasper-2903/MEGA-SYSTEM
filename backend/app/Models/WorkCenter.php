<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class WorkCenter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'capacity_per_day',
        'calendar_notes',
        'is_active',
    ];

    protected $casts = [
        'capacity_per_day' => 'integer',
        'is_active' => 'boolean',
    ];

    public function routingSteps()
    {
        return $this->hasMany(RoutingStep::class);
    }

    public function productionLogs()
    {
        return $this->hasManyThrough(ProductionLog::class, RoutingStep::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    public function getUtilizationPercentageAttribute()
    {
        $totalCapacity = $this->capacity_per_day * 30; // Monthly capacity
        $totalUsed = $this->productionLogs()
            ->whereMonth('date', now()->month)
            ->sum(\DB::raw('qty_completed * routing_steps.std_time_minutes'));

        if ($totalCapacity == 0) {
            return 0;
        }

        return round(($totalUsed / $totalCapacity) * 100, 2);
    }

    public function getAvailableCapacityAttribute()
    {
        $usedCapacity = $this->productionLogs()
            ->whereDate('date', now())
            ->sum(\DB::raw('qty_completed * routing_steps.std_time_minutes'));

        return max(0, $this->capacity_per_day - $usedCapacity);
    }

    public function isOverloaded()
    {
        return $this->utilization_percentage > 100;
    }
}