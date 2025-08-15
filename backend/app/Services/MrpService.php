<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Material;
use App\Models\SalesOrder;
use App\Models\ProductionOrder;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\ReorderPolicy;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\SystemJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MrpService
{
    public function runMrp()
    {
        $job = SystemJob::create([
            'job_name' => 'MRP_RUN',
            'status' => 'running',
            'started_at' => now(),
        ]);

        try {
            Log::info('Starting MRP run');

            // Step 1: Calculate gross requirements
            $grossRequirements = $this->calculateGrossRequirements();

            // Step 2: Calculate net requirements
            $netRequirements = $this->calculateNetRequirements($grossRequirements);

            // Step 3: Generate planned orders
            $plannedOrders = $this->generatePlannedOrders($netRequirements);

            // Step 4: Create purchase orders for materials below reorder point
            $this->createPurchaseOrdersForReorder();

            $job->update([
                'status' => 'completed',
                'finished_at' => now(),
                'notes' => 'MRP run completed successfully. Generated ' . count($plannedOrders) . ' planned orders.',
            ]);

            Log::info('MRP run completed successfully');

            return [
                'success' => true,
                'planned_orders' => count($plannedOrders),
                'purchase_orders_created' => $this->getPurchaseOrdersCreated(),
            ];

        } catch (\Exception $e) {
            $job->update([
                'status' => 'failed',
                'finished_at' => now(),
                'notes' => 'MRP run failed: ' . $e->getMessage(),
            ]);

            Log::error('MRP run failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function calculateGrossRequirements()
    {
        $grossRequirements = [];

        // Get all open sales orders
        $openSalesOrders = SalesOrder::whereIn('status', ['confirmed', 'planned', 'in_production'])
            ->with('lines.product')
            ->get();

        // Get all open production orders
        $openProductionOrders = ProductionOrder::whereIn('status', ['planned', 'released', 'in_process'])
            ->with('product')
            ->get();

        // Calculate demand from sales orders
        foreach ($openSalesOrders as $so) {
            foreach ($so->lines as $line) {
                $productSku = $line->product->sku;
                
                if (!isset($grossRequirements[$productSku])) {
                    $grossRequirements[$productSku] = [
                        'item_type' => 'product',
                        'demand' => 0,
                        'due_date' => $so->promised_date,
                    ];
                }
                
                $grossRequirements[$productSku]['demand'] += $line->qty;
            }
        }

        // Calculate demand from production orders
        foreach ($openProductionOrders as $po) {
            $productSku = $po->product->sku;
            $remainingQty = $po->qty_planned - $po->qty_completed;
            
            if (!isset($grossRequirements[$productSku])) {
                $grossRequirements[$productSku] = [
                    'item_type' => 'product',
                    'demand' => 0,
                    'due_date' => $po->due_date,
                ];
            }
            
            $grossRequirements[$productSku]['demand'] += $remainingQty;
        }

        // Explode BOMs to get material requirements
        $materialRequirements = $this->explodeBoms($grossRequirements);

        return array_merge($grossRequirements, $materialRequirements);
    }

    private function explodeBoms($productRequirements)
    {
        $materialRequirements = [];

        foreach ($productRequirements as $sku => $requirement) {
            if ($requirement['item_type'] !== 'product') {
                continue;
            }

            $product = Product::where('sku', $sku)->first();
            if (!$product) {
                continue;
            }

            $activeBom = $product->activeBom;
            if (!$activeBom) {
                continue;
            }

            foreach ($activeBom->items as $bomItem) {
                $materialSku = $bomItem->material->sku;
                $totalQtyRequired = $bomItem->total_qty_required * $requirement['demand'];

                if (!isset($materialRequirements[$materialSku])) {
                    $materialRequirements[$materialSku] = [
                        'item_type' => 'material',
                        'demand' => 0,
                        'due_date' => $requirement['due_date'],
                    ];
                }

                $materialRequirements[$materialSku]['demand'] += $totalQtyRequired;
            }
        }

        return $materialRequirements;
    }

    private function calculateNetRequirements($grossRequirements)
    {
        $netRequirements = [];

        foreach ($grossRequirements as $sku => $requirement) {
            // Get current inventory
            $inventory = Inventory::where('sku', $sku)
                ->where('item_type', $requirement['item_type'])
                ->sum('on_hand');

            // Get allocated inventory
            $allocated = Inventory::where('sku', $sku)
                ->where('item_type', $requirement['item_type'])
                ->sum('allocated');

            // Get on-order inventory
            $onOrder = Inventory::where('sku', $sku)
                ->where('item_type', $requirement['item_type'])
                ->sum('on_order');

            // Get safety stock
            $reorderPolicy = ReorderPolicy::where('sku', $sku)
                ->where('item_type', $requirement['item_type'])
                ->where('is_active', true)
                ->first();

            $safetyStock = $reorderPolicy ? $reorderPolicy->min_level : 0;

            // Calculate net requirement
            $availableQty = $inventory - $allocated + $onOrder;
            $netRequirement = max(0, $requirement['demand'] + $safetyStock - $availableQty);

            if ($netRequirement > 0) {
                $netRequirements[$sku] = [
                    'item_type' => $requirement['item_type'],
                    'gross_requirement' => $requirement['demand'],
                    'net_requirement' => $netRequirement,
                    'due_date' => $requirement['due_date'],
                    'current_inventory' => $inventory,
                    'allocated' => $allocated,
                    'on_order' => $onOrder,
                    'safety_stock' => $safetyStock,
                ];
            }
        }

        return $netRequirements;
    }

    private function generatePlannedOrders($netRequirements)
    {
        $plannedOrders = [];

        foreach ($netRequirements as $sku => $requirement) {
            if ($requirement['item_type'] === 'material') {
                // For materials, create purchase order suggestions
                $plannedOrders[] = [
                    'type' => 'purchase_order',
                    'sku' => $sku,
                    'qty' => $requirement['net_requirement'],
                    'due_date' => $requirement['due_date'],
                    'item_type' => 'material',
                ];
            } else {
                // For products, create production order suggestions
                $plannedOrders[] = [
                    'type' => 'production_order',
                    'sku' => $sku,
                    'qty' => $requirement['net_requirement'],
                    'due_date' => $requirement['due_date'],
                    'item_type' => 'product',
                ];
            }
        }

        return $plannedOrders;
    }

    private function createPurchaseOrdersForReorder()
    {
        $reorderPolicies = ReorderPolicy::where('is_active', true)->get();
        $purchaseOrdersCreated = 0;

        foreach ($reorderPolicies as $policy) {
            $inventory = Inventory::where('sku', $policy->sku)
                ->where('item_type', $policy->item_type)
                ->sum('on_hand');

            $allocated = Inventory::where('sku', $policy->sku)
                ->where('item_type', $policy->item_type)
                ->sum('allocated');

            $availableQty = $inventory - $allocated;

            if ($availableQty <= $policy->reorder_point) {
                $this->createPurchaseOrder($policy, $availableQty);
                $purchaseOrdersCreated++;
            }
        }

        return $purchaseOrdersCreated;
    }

    private function createPurchaseOrder($reorderPolicy, $currentQty)
    {
        $item = $reorderPolicy->item_type === 'material' 
            ? Material::where('sku', $reorderPolicy->sku)->first()
            : Product::where('sku', $reorderPolicy->sku)->first();

        if (!$item || $reorderPolicy->item_type !== 'material') {
            return null;
        }

        // Check if there's already an open PO for this material
        $existingPO = PurchaseOrder::whereHas('lines', function ($query) use ($reorderPolicy) {
            $query->where('material_id', $item->id)
                  ->whereIn('status', ['open', 'partial']);
        })->whereIn('status', ['draft', 'approved', 'sent'])->first();

        if ($existingPO) {
            return $existingPO;
        }

        // Create new purchase order
        $po = PurchaseOrder::create([
            'po_number' => 'PO-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'supplier_id' => $item->supplier_id,
            'status' => 'draft',
            'expected_date' => now()->addDays($item->supplier->lead_time_days ?? 7),
            'created_by' => 1, // System user
        ]);

        // Create PO line
        PurchaseOrderLine::create([
            'purchase_order_id' => $po->id,
            'material_id' => $item->id,
            'qty_ordered' => $reorderPolicy->reorder_qty,
            'unit_cost' => $item->standard_cost,
            'expected_date_line' => $po->expected_date,
            'status' => 'open',
        ]);

        return $po;
    }

    private function getPurchaseOrdersCreated()
    {
        return PurchaseOrder::where('created_by', 1)
            ->whereDate('created_at', today())
            ->count();
    }
}