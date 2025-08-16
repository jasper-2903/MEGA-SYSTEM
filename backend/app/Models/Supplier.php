<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'contact_name',
        'email',
        'phone',
        'address',
        'payment_terms',
        'lead_time_days',
        'is_active',
    ];

    protected $casts = [
        'lead_time_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }
}