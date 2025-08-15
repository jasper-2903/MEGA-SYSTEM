<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'code' => 'SUP001',
                'name' => 'Oakwood Lumber Co.',
                'contact_name' => 'John Smith',
                'email' => 'john@oakwood.com',
                'phone' => '(555) 123-4567',
                'address' => '123 Timber Lane, Woodville, CA 90210',
                'payment_terms' => 'Net 30',
                'lead_time_days' => 7,
                'is_active' => true,
            ],
            [
                'code' => 'SUP002',
                'name' => 'Hardware Plus Inc.',
                'contact_name' => 'Sarah Johnson',
                'email' => 'sarah@hardwareplus.com',
                'phone' => '(555) 234-5678',
                'address' => '456 Hardware Ave, Tooltown, CA 90211',
                'payment_terms' => 'Net 30',
                'lead_time_days' => 5,
                'is_active' => true,
            ],
            [
                'code' => 'SUP003',
                'name' => 'Finish Masters',
                'contact_name' => 'Mike Davis',
                'email' => 'mike@finishmasters.com',
                'phone' => '(555) 345-6789',
                'address' => '789 Finish Blvd, Coating City, CA 90212',
                'payment_terms' => 'Net 30',
                'lead_time_days' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'SUP004',
                'name' => 'Packaging Solutions',
                'contact_name' => 'Lisa Wilson',
                'email' => 'lisa@packaging.com',
                'phone' => '(555) 456-7890',
                'address' => '321 Package St, Boxville, CA 90213',
                'payment_terms' => 'Net 30',
                'lead_time_days' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'SUP005',
                'name' => 'Premium Wood Supply',
                'contact_name' => 'Robert Brown',
                'email' => 'robert@premiumwood.com',
                'phone' => '(555) 567-8901',
                'address' => '654 Premium Rd, Luxury Wood, CA 90214',
                'payment_terms' => 'Net 30',
                'lead_time_days' => 10,
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::create($supplierData);
        }
    }
}