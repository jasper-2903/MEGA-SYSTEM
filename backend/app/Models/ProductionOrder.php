<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'wo_number',
        'product_id',
        'qty_planned',
        'qty_completed',
        'status',
        'start_date',
        'due_date',
        'priority',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'qty_planned' => 'integer',
        'qty_completed' => 'integer',
        'start_date' => 'date',
        'due_date' => 'date',
        'priority' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function routing()
    {
        return $this->product->activeRouting();
    }

    public function logs()
    {
        return $this->hasMany(ProductionLog::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('due_date', [$startDate, $endDate]);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['planned', 'released', 'in_process']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereIn('status', ['planned', 'released', 'in_process']);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date < now() && in_array($this->status, ['planned', 'released', 'in_process']);
    }

    public function getCompletionPercentageAttribute()
    {
        if ($this->qty_planned == 0) {
            return 0;
        }

        return round(($this->qty_completed / $this->qty_planned) * 100, 2);
    }

    public function getRemainingQtyAttribute()
    {
        return $this->qty_planned - $this->qty_completed;
    }

    public function isCompleted()
    {
        return $this->qty_completed >= $this->qty_planned;
    }

    public function isInProgress()
    {
        return in_array($this->status, ['released', 'in_process']);
    }

    public function canBeReleased()
    {
        return $this->status === 'planned';
    }

    public function canBeCompleted()
    {
        return $this->status === 'in_process' && $this->isCompleted();
    }

    public function getPriorityLabelAttribute()
    {
        $labels = [
            1 => 'Critical',
            2 => 'High',
            3 => 'Medium',
            4 => 'Low',
            5 => 'Normal',
        ];

        return $labels[$this->priority] ?? 'Normal';
    }
}