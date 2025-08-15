<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@unick.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // MRP Planner
        User::create([
            'name' => 'Production Planner',
            'email' => 'planner@unick.test',
            'password' => Hash::make('password'),
            'role' => 'planner',
            'is_active' => true,
        ]);

        // Warehouse Manager
        User::create([
            'name' => 'Warehouse Manager',
            'email' => 'warehouse@unick.test',
            'password' => Hash::make('password'),
            'role' => 'warehouse',
            'is_active' => true,
        ]);

        // Production Supervisor
        User::create([
            'name' => 'Production Supervisor',
            'email' => 'prod@unick.test',
            'password' => Hash::make('password'),
            'role' => 'production',
            'is_active' => true,
        ]);

        // Customer user
        $customer = Customer::create([
            'code' => 'CUST001',
            'name' => 'Demo Customer',
            'email' => 'customer@unick.test',
            'phone' => '555-0123',
            'billing_address' => '123 Main St, Anytown, ST 12345',
            'shipping_address' => '123 Main St, Anytown, ST 12345',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Demo Customer',
            'email' => 'customer@unick.test',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'customer_id' => $customer->id,
            'is_active' => true,
        ]);
    }
}
