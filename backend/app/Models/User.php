<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'customer_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'created_by');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'created_by');
    }

    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class, 'created_by');
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'created_by');
    }

    public function productionLogs()
    {
        return $this->hasMany(ProductionLog::class, 'recorded_by');
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPlanner()
    {
        return $this->role === 'planner';
    }

    public function isWarehouse()
    {
        return $this->role === 'warehouse';
    }

    public function isProduction()
    {
        return $this->role === 'production';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}