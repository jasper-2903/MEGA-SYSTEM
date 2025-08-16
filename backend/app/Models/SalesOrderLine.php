<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'product_id',
        'qty',
        'unit_price',
        'status',
    ];

    protected $casts = [
        'qty' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'allocated']);
    }

    public function getLineTotalAttribute()
    {
        return $this->qty * $this->unit_price;
    }

    public function isShipped()
    {
        return $this->status === 'shipped';
    }

    public function isAllocated()
    {
        return $this->status === 'allocated';
    }
}