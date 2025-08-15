<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'routing_step_id',
        'date',
        'qty_started',
        'qty_completed',
        'qty_scrap',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'qty_started' => 'integer',
        'qty_completed' => 'integer',
        'qty_scrap' => 'integer',
    ];

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function routingStep()
    {
        return $this->belongsTo(RoutingStep::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeByProductionOrder($query, $productionOrderId)
    {
        return $query->where('production_order_id', $productionOrderId);
    }

    public function scopeByRoutingStep($query, $routingStepId)
    {
        return $query->where('routing_step_id', $routingStepId);
    }

    public function getEfficiencyAttribute()
    {
        if ($this->qty_started == 0) {
            return 0;
        }

        return round(($this->qty_completed / $this->qty_started) * 100, 2);
    }

    public function getScrapRateAttribute()
    {
        if ($this->qty_started == 0) {
            return 0;
        }

        return round(($this->qty_scrap / $this->qty_started) * 100, 2);
    }

    public function getTotalTimeAttribute()
    {
        return $this->qty_completed * $this->routingStep->std_time_minutes;
    }
}