<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SupplierSeeder::class,
            MaterialSeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            WarehouseSeeder::class,
            LocationSeeder::class,
            BomSeeder::class,
            WorkCenterSeeder::class,
            RoutingSeeder::class,
            ReorderPolicySeeder::class,
            InventorySeeder::class,
            SalesOrderSeeder::class,
            ProductionOrderSeeder::class,
            PurchaseOrderSeeder::class,
            ConsumptionHistorySeeder::class,
            ForecastSeeder::class,
        ]);
    }
}