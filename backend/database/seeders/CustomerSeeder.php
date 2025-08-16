<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'code' => 'CUST001',
                'name' => 'Home Decor Plus',
                'email' => 'orders@homedecorplus.com',
                'phone' => '(555) 111-2222',
                'billing_address' => '123 Main Street, Downtown, CA 90210',
                'shipping_address' => '123 Main Street, Downtown, CA 90210',
                'payment_terms' => 'Net 30',
                'credit_limit' => 50000.00,
                'is_active' => true,
            ],
            [
                'code' => 'CUST002',
                'name' => 'Office Furniture Solutions',
                'email' => 'purchasing@officefurniture.com',
                'phone' => '(555) 222-3333',
                'billing_address' => '456 Business Ave, Corporate Plaza, CA 90211',
                'shipping_address' => '456 Business Ave, Corporate Plaza, CA 90211',
                'payment_terms' => 'Net 30',
                'credit_limit' => 75000.00,
                'is_active' => true,
            ],
            [
                'code' => 'CUST003',
                'name' => 'Luxury Furniture Gallery',
                'email' => 'sales@luxuryfurniture.com',
                'phone' => '(555) 333-4444',
                'billing_address' => '789 Luxury Blvd, Upscale District, CA 90212',
                'shipping_address' => '789 Luxury Blvd, Upscale District, CA 90212',
                'payment_terms' => 'Net 30',
                'credit_limit' => 100000.00,
                'is_active' => true,
            ],
            [
                'code' => 'CUST004',
                'name' => 'Restaurant Supply Co.',
                'email' => 'orders@restaurantsupply.com',
                'phone' => '(555) 444-5555',
                'billing_address' => '321 Restaurant Row, Food District, CA 90213',
                'shipping_address' => '321 Restaurant Row, Food District, CA 90213',
                'payment_terms' => 'Net 30',
                'credit_limit' => 25000.00,
                'is_active' => true,
            ],
            [
                'code' => 'CUST005',
                'name' => 'Hotel Furniture Direct',
                'email' => 'procurement@hotelfurniture.com',
                'phone' => '(555) 555-6666',
                'billing_address' => '654 Hotel Circle, Hospitality Zone, CA 90214',
                'shipping_address' => '654 Hotel Circle, Hospitality Zone, CA 90214',
                'payment_terms' => 'Net 30',
                'credit_limit' => 150000.00,
                'is_active' => true,
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }
    }
}