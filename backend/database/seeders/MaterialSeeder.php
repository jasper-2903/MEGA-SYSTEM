<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = Supplier::all();

        $materials = [
            // Wood materials
            [
                'sku' => 'MAT-001',
                'name' => 'Oak Wood Planks',
                'type' => 'wood',
                'unit_of_measure' => 'PCS',
                'standard_cost' => 25.00,
                'safety_stock' => 50,
                'supplier_id' => $suppliers->where('code', 'SUP001')->first()->id,
                'is_active' => true,
            ],
            [
                'sku' => 'MAT-002',
                'name' => 'Maple Wood Sheets',
                'type' => 'wood',
                'unit_of_measure' => 'PCS',
                'standard_cost' => 30.00,
                'safety_stock' => 40,
                'supplier_id' => $suppliers->where('code', 'SUP001')->first()->id,
                'is_active' => true,
            ],
            [
                'sku' => 'MAT-003',
                'name' => 'Cherry Wood Boards',
                'type' => 'wood',
                'unit_of_measure' => 'PCS',
                'standard_cost' => 35.00,
                'safety_stock' => 30,
                'supplier_id' => $suppliers->where('code', 'SUP005')->first()->id,
                'is_active' => true,
            ],
            [
                'sku' => 'MAT-004',
                'name' => 'Pine Wood Strips',
                'type' => 'wood',
                'unit_of_measure' => 'PCS',
                'standard_cost' => 15.00,
                'safety_stock' => 60,
                'supplier_id' => $suppliers->where('code', 'SUP001')->first()->id,
                'is_active' => true,
            ],
            [
                'sku' => 'MAT-005',
                'name' => 'Walnut Wood Panels',
                'type' => 'wood',
                'unit_of_measure' => 'PCS',
                'standard_cost' => 40.00,
                'safety_stock' => 25,
                'supplier_id' => $suppliers->where('code', 'SUP005')->first()->id,
                'is_active' => true,
            ],

            // Hardware materials
            [
                'sku' => 'MAT-006',
                'name' => 'Wood Screws 2"',
                'type' => 'hardware',
                'unit_of_measure' => 'PCS',
                'standard_cost' => 0.25,
                'safety_stock' => 500,
                'supplier_id' => $suppliers->where('code', 'SUP002')->first()->id,
                'is_active' => true,
            ],
            [
                'sku' => 'MAT-007',
                'name' => 'Wood Screws 3"',
                'type' => 'hardware',
                'unit_of_measure' => 'PCS',
                'standard_cost' => 0.35,
                'safety_stock' => 400,
                'supplier_id' => $suppliers->where('code', 'SUP002')->first()->id,
                'is_active' => true,
            ],
            [
                'sku' => 'MAT-008',
                'name' => 'Hinges Brass',
                'type' => 'hardware',
                'unit_of_measure' => 'PCS',
                'standard_cost' => 2.50,
                'safety_stock' => 100,
                'supplier_id' => $suppliers->where('code', 'SUP002')->first()->id,
                'is_active' => true,
            ],
            [
                'sku' => 'MAT-009',
                'name' => 'Drawer Slides',
                'type' => 'hardware',
                'unit_of_measure' => 'PCS',
                'standard_cost' => 8.00,
                'safety_stock' => 80,
                'supplier_id' => $suppliers->where('code', 'SUP002')->first()->id,
                'is_active' => true,
            ],
            [
                'sku' => 'MAT-010',
                'name' => 'Cabinet Knobs',
                'type' => 'hardware',
                'unit_of_measure' => 'PCS',
                'standard_cost' => 1.50,
                'safety_stock' => 200,
                'supplier_id' => $suppliers->where('code', 'SUP002')->first()->id,
                'is_active' => true,
            ],

            // Finish materials
            [
                'sku' => 'MAT-011',
                'name' => 'Clear Varnish',
                'type' => 'finish',
                'unit_of_measure' => 'L',
                'standard_cost' => 15.00,
                'safety_stock' => 20,
                'supplier_id' => $suppliers->where('code', 'SUP003')->first()->id,
                'is_active' => true,
            ],
            [
                'sku' => 'MAT-012',
                'name' => 'Stain Dark Oak',
                'type' => 'finish',
                'unit_of_measure' => 'L',
                'standard_cost' => 12.00,
                'safety_stock' => 15,
                'supplier_id' => $suppliers->where('code', 'SUP003')->first()->id,
                'is_active' => true,
            ],
            [
                'sku' => 'MAT-013',
                'name' => 'Stain Cherry',
                'type' => 'finish',
                'unit_of_measure' => 'L',
                'standard_cost' => 14.00,
                'safety_stock' => 12,
                'supplier_id' => $suppliers->where('code', 'SUP003')->first()->id,
                'is_active' => true,
            ],
            [
                'sku' => 'MAT-014',
                'name' => 'Polyurethane',
                'type' => 'finish',
                'unit_of_measure' => 'L',
                'standard_cost' => 18.00,
                'safety_stock' => 18,
                'supplier_id' => $suppliers->where('code', 'SUP003')->first()->id,
                'is_active' => true,
            ],
            [
                'sku' => 'MAT-015',
                'name' => 'Wood Filler',
                'type' => 'finish',
                'unit_of_measure' => 'KG',
                'standard_cost' => 8.00,
                'safety_stock' => 25,
                'supplier_id' => $suppliers->where('code', 'SUP003')->first()->id,
                'is_active' => true,
            ],

            // Packaging materials
            [
                'sku' => 'MAT-016',
                'name' => 'Bubble Wrap',
                'type' => 'packaging',
                'unit_of_measure' => 'M',
                'standard_cost' => 2.00,
                'safety_stock' => 100,
                'supplier_id' => $suppliers->where('code', 'SUP004')->first()->id,
                'is_active' => true,
            ],
            [
                'sku' => 'MAT-017',
                'name' => 'Cardboard Boxes',
                'type' => 'packaging',
                'unit_of_measure' => 'PCS',
                'standard_cost' => 3.50,
                'safety_stock' => 50,
                'supplier_id' => $suppliers->where('code', 'SUP004')->first()->id,
                'is_active' => true,
            ],
            [
                'sku' => 'MAT-018',
                'name' => 'Packing Tape',
                'type' => 'packaging',
                'unit_of_measure' => 'PCS',
                'standard_cost' => 1.00,
                'safety_stock' => 200,
                'supplier_id' => $suppliers->where('code', 'SUP004')->first()->id,
                'is_active' => true,
            ],
            [
                'sku' => 'MAT-019',
                'name' => 'Corner Protectors',
                'type' => 'packaging',
                'unit_of_measure' => 'PCS',
                'standard_cost' => 0.75,
                'safety_stock' => 300,
                'supplier_id' => $suppliers->where('code', 'SUP004')->first()->id,
                'is_active' => true,
            ],
            [
                'sku' => 'MAT-020',
                'name' => 'Furniture Blankets',
                'type' => 'packaging',
                'unit_of_measure' => 'PCS',
                'standard_cost' => 5.00,
                'safety_stock' => 40,
                'supplier_id' => $suppliers->where('code', 'SUP004')->first()->id,
                'is_active' => true,
            ],
        ];

        foreach ($materials as $materialData) {
            Material::create($materialData);
        }
    }
}