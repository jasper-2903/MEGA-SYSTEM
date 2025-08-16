<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Tables
            [
                'sku' => 'PROD-001',
                'name' => 'Oak Dining Table',
                'category' => 'Tables',
                'dimensions' => '180x90x75',
                'finish' => 'Natural Oak',
                'unit_of_measure' => 'PCS',
                'unit_weight' => 45.0,
                'price' => 899.99,
                'lead_time_days' => 7,
                'description' => 'Solid oak dining table with natural finish. Seats 6-8 people comfortably.',
                'image_url' => 'https://images.unsplash.com/photo-1615066390971-03e4e1c36ddf?w=400',
                'is_active' => true,
            ],
            [
                'sku' => 'PROD-002',
                'name' => 'Maple Coffee Table',
                'category' => 'Tables',
                'dimensions' => '120x60x45',
                'finish' => 'Cherry Stain',
                'unit_of_measure' => 'PCS',
                'unit_weight' => 25.0,
                'price' => 349.99,
                'lead_time_days' => 5,
                'description' => 'Elegant maple coffee table with cherry stain finish.',
                'image_url' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400',
                'is_active' => true,
            ],
            [
                'sku' => 'PROD-003',
                'name' => 'Walnut Side Table',
                'category' => 'Tables',
                'dimensions' => '50x50x60',
                'finish' => 'Natural Walnut',
                'unit_of_measure' => 'PCS',
                'unit_weight' => 12.0,
                'price' => 199.99,
                'lead_time_days' => 4,
                'description' => 'Compact walnut side table perfect for living rooms.',
                'image_url' => 'https://images.unsplash.com/photo-1592078615290-033ee584e267?w=400',
                'is_active' => true,
            ],

            // Chairs
            [
                'sku' => 'PROD-004',
                'name' => 'Oak Dining Chair',
                'category' => 'Chairs',
                'dimensions' => '45x50x90',
                'finish' => 'Natural Oak',
                'unit_of_measure' => 'PCS',
                'unit_weight' => 8.5,
                'price' => 149.99,
                'lead_time_days' => 3,
                'description' => 'Comfortable oak dining chair with padded seat.',
                'image_url' => 'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=400',
                'is_active' => true,
            ],
            [
                'sku' => 'PROD-005',
                'name' => 'Cherry Armchair',
                'category' => 'Chairs',
                'dimensions' => '65x75x85',
                'finish' => 'Cherry Stain',
                'unit_of_measure' => 'PCS',
                'unit_weight' => 15.0,
                'price' => 299.99,
                'lead_time_days' => 6,
                'description' => 'Elegant cherry armchair with upholstered seat and back.',
                'image_url' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400',
                'is_active' => true,
            ],
            [
                'sku' => 'PROD-006',
                'name' => 'Maple Office Chair',
                'category' => 'Chairs',
                'dimensions' => '60x60x120',
                'finish' => 'Natural Maple',
                'unit_of_measure' => 'PCS',
                'unit_weight' => 18.0,
                'price' => 399.99,
                'lead_time_days' => 5,
                'description' => 'Ergonomic maple office chair with adjustable height.',
                'image_url' => 'https://images.unsplash.com/photo-1592078615290-033ee584e267?w=400',
                'is_active' => true,
            ],

            // Cabinets
            [
                'sku' => 'PROD-007',
                'name' => 'Oak Bookcase',
                'category' => 'Cabinets',
                'dimensions' => '120x30x180',
                'finish' => 'Natural Oak',
                'unit_of_measure' => 'PCS',
                'unit_weight' => 35.0,
                'price' => 599.99,
                'lead_time_days' => 8,
                'description' => 'Tall oak bookcase with 5 adjustable shelves.',
                'image_url' => 'https://images.unsplash.com/photo-1615066390971-03e4e1c36ddf?w=400',
                'is_active' => true,
            ],
            [
                'sku' => 'PROD-008',
                'name' => 'Cherry Filing Cabinet',
                'category' => 'Cabinets',
                'dimensions' => '45x60x120',
                'finish' => 'Cherry Stain',
                'unit_of_measure' => 'PCS',
                'unit_weight' => 28.0,
                'price' => 449.99,
                'lead_time_days' => 7,
                'description' => 'Two-drawer cherry filing cabinet with lock.',
                'image_url' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400',
                'is_active' => true,
            ],
            [
                'sku' => 'PROD-009',
                'name' => 'Walnut Display Cabinet',
                'category' => 'Cabinets',
                'dimensions' => '80x40x180',
                'finish' => 'Natural Walnut',
                'unit_of_measure' => 'PCS',
                'unit_weight' => 42.0,
                'price' => 799.99,
                'lead_time_days' => 10,
                'description' => 'Elegant walnut display cabinet with glass doors.',
                'image_url' => 'https://images.unsplash.com/photo-1592078615290-033ee584e267?w=400',
                'is_active' => true,
            ],
            [
                'sku' => 'PROD-010',
                'name' => 'Maple Kitchen Cabinet',
                'category' => 'Cabinets',
                'dimensions' => '60x60x90',
                'finish' => 'Natural Maple',
                'unit_of_measure' => 'PCS',
                'unit_weight' => 22.0,
                'price' => 349.99,
                'lead_time_days' => 6,
                'description' => 'Base kitchen cabinet with single door and shelf.',
                'image_url' => 'https://images.unsplash.com/photo-1615066390971-03e4e1c36ddf?w=400',
                'is_active' => true,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}