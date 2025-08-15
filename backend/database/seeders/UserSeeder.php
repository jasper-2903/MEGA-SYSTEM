<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo users
        $users = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@unick.test',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'name' => 'Production Planner',
                'email' => 'planner@unick.test',
                'password' => Hash::make('password'),
                'role' => 'planner',
                'is_active' => true,
            ],
            [
                'name' => 'Warehouse Manager',
                'email' => 'warehouse@unick.test',
                'password' => Hash::make('password'),
                'role' => 'warehouse',
                'is_active' => true,
            ],
            [
                'name' => 'Production Supervisor',
                'email' => 'prod@unick.test',
                'password' => Hash::make('password'),
                'role' => 'production',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Create customer user
        $customer = Customer::first();
        if ($customer) {
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
}