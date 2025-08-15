<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'so_number',
        'customer_id',
        'status',
        'requested_date',
        'promised_date',
        'subtotal',
        'tax',
        'total',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'promised_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function lines()
    {
        return $this->hasMany(SalesOrderLine::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('promised_date', [$startDate, $endDate]);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['draft', 'confirmed', 'planned', 'in_production']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('promised_date', '<', now())
                    ->whereIn('status', ['confirmed', 'planned', 'in_production']);
    }

    public function getIsOverdueAttribute()
    {
        return $this->promised_date < now() && in_array($this->status, ['confirmed', 'planned', 'in_production']);
    }

    public function getShippedQtyAttribute()
    {
        return $this->lines()->sum('qty');
    }

    public function getOrderedQtyAttribute()
    {
        return $this->lines()->sum('qty');
    }

    public function getCompletionPercentageAttribute()
    {
        $totalOrdered = $this->lines()->sum('qty');
        $totalShipped = $this->lines()->where('status', 'shipped')->sum('qty');

        if ($totalOrdered == 0) {
            return 0;
        }

        return round(($totalShipped / $totalOrdered) * 100, 2);
    }

    public function isFullyShipped()
    {
        return $this->lines()->where('status', '!=', 'shipped')->count() === 0;
    }

    public function calculateTotals()
    {
        $subtotal = $this->lines()->sum(\DB::raw('qty * unit_price'));
        $tax = $subtotal * 0.1; // 10% tax rate
        $total = $subtotal + $tax;

        $this->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);
    }
}