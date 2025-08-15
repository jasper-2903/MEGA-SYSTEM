<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Material;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\SalesOrder;
use App\Models\SalesOrderLine;
use App\Models\ProductionOrder;
use App\Models\ProductionLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    public function receivePurchaseOrder($purchaseOrderId, $receiptData)
    {
        DB::beginTransaction();

        try {
            $purchaseOrder = PurchaseOrder::with('lines.material')->findOrFail($purchaseOrderId);
            
            foreach ($receiptData as $lineId => $receivedQty) {
                $line = PurchaseOrderLine::findOrFail($lineId);
                
                if ($line->purchase_order_id != $purchaseOrderId) {
                    throw new \Exception('Invalid line for this purchase order');
                }

                if ($receivedQty <= 0) {
                    continue;
                }

                // Update PO line
                $line->qty_received += $receivedQty;
                $line->status = $line->qty_received >= $line->qty_ordered ? 'received' : 'partial';
                $line->save();

                // Update inventory
                $this->updateInventory(
                    $line->material->sku,
                    'material',
                    $receivedQty,
                    'receipt',
                    'PO',
                    $purchaseOrderId,
                    'Receipt from PO ' . $purchaseOrder->po_number
                );
            }

            // Check if all lines are received
            $allReceived = $purchaseOrder->lines()->where('status', '!=', 'received')->count() === 0;
            if ($allReceived) {
                $purchaseOrder->update(['status' => 'received']);
            } else {
                $purchaseOrder->update(['status' => 'partial']);
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase order receipt failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function allocateSalesOrder($salesOrderId)
    {
        DB::beginTransaction();

        try {
            $salesOrder = SalesOrder::with('lines.product')->findOrFail($salesOrderId);
            
            foreach ($salesOrder->lines as $line) {
                $product = $line->product;
                $availableQty = $product->available_qty;
                
                if ($availableQty >= $line->qty) {
                    // Allocate inventory
                    $this->allocateInventory(
                        $product->sku,
                        'product',
                        $line->qty,
                        $salesOrderId
                    );
                    
                    $line->update(['status' => 'allocated']);
                } else {
                    // Partial allocation if possible
                    if ($availableQty > 0) {
                        $this->allocateInventory(
                            $product->sku,
                            'product',
                            $availableQty,
                            $salesOrderId
                        );
                        
                        $line->update(['status' => 'allocated']);
                    }
                }
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sales order allocation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function shipSalesOrder($salesOrderId, $shipmentData)
    {
        DB::beginTransaction();

        try {
            $salesOrder = SalesOrder::with('lines.product')->findOrFail($salesOrderId);
            
            foreach ($shipmentData as $lineId => $shippedQty) {
                $line = SalesOrderLine::findOrFail($lineId);
                
                if ($line->sales_order_id != $salesOrderId) {
                    throw new \Exception('Invalid line for this sales order');
                }

                if ($shippedQty <= 0) {
                    continue;
                }

                // Issue inventory
                $this->updateInventory(
                    $line->product->sku,
                    'product',
                    $shippedQty,
                    'issue',
                    'SO',
                    $salesOrderId,
                    'Shipment for SO ' . $salesOrder->so_number
                );

                // Deallocate inventory
                $this->deallocateInventory(
                    $line->product->sku,
                    'product',
                    $shippedQty,
                    $salesOrderId
                );

                $line->update(['status' => 'shipped']);
            }

            // Check if all lines are shipped
            $allShipped = $salesOrder->lines()->where('status', '!=', 'shipped')->count() === 0;
            if ($allShipped) {
                $salesOrder->update(['status' => 'shipped']);
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sales order shipment failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function backflushProductionOrder($productionOrderId, $routingStepId, $qtyCompleted, $qtyScrap = 0)
    {
        DB::beginTransaction();

        try {
            $productionOrder = ProductionOrder::with('product.activeBom.items.material')->findOrFail($productionOrderId);
            $routingStep = $productionOrder->product->activeRouting->steps()->findOrFail($routingStepId);

            // Consume materials based on BOM
            $bom = $productionOrder->product->activeBom;
            if ($bom) {
                foreach ($bom->items as $bomItem) {
                    $totalQtyRequired = $bomItem->total_qty_required * $qtyCompleted;
                    
                    $this->updateInventory(
                        $bomItem->material->sku,
                        'material',
                        $totalQtyRequired,
                        'consume',
                        'WO',
                        $productionOrderId,
                        'Backflush for WO ' . $productionOrder->wo_number
                    );
                }
            }

            // Produce finished goods
            $this->updateInventory(
                $productionOrder->product->sku,
                'product',
                $qtyCompleted,
                'produce',
                'WO',
                $productionOrderId,
                'Production completion for WO ' . $productionOrder->wo_number
            );

            // Update production order
            $productionOrder->qty_completed += $qtyCompleted;
            if ($productionOrder->qty_completed >= $productionOrder->qty_planned) {
                $productionOrder->status = 'completed';
            } else {
                $productionOrder->status = 'in_process';
            }
            $productionOrder->save();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Production backflush failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function adjustInventory($sku, $itemType, $warehouseId, $locationId, $qty, $adjustmentType, $notes = '')
    {
        DB::beginTransaction();

        try {
            $this->updateInventory(
                $sku,
                $itemType,
                $qty,
                $adjustmentType,
                'COUNT',
                null,
                $notes
            );

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Inventory adjustment failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function updateInventory($sku, $itemType, $qty, $txnType, $referenceType, $referenceId, $notes = '')
    {
        // Get or create inventory record
        $inventory = Inventory::firstOrCreate([
            'sku' => $sku,
            'item_type' => $itemType,
            'warehouse_id' => 1, // Default warehouse
            'location_id' => 1,  // Default location
        ], [
            'on_hand' => 0,
            'allocated' => 0,
            'on_order' => 0,
        ]);

        // Update inventory based on transaction type
        $inventory->updateOnHand($qty, $txnType);

        // Create inventory transaction
        InventoryTransaction::create([
            'sku' => $sku,
            'item_type' => $itemType,
            'warehouse_id' => $inventory->warehouse_id,
            'location_id' => $inventory->location_id,
            'txn_type' => $txnType,
            'qty' => $qty,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'created_by' => auth()->id() ?? 1,
        ]);
    }

    private function allocateInventory($sku, $itemType, $qty, $referenceId)
    {
        $inventory = Inventory::where('sku', $sku)
            ->where('item_type', $itemType)
            ->first();

        if ($inventory) {
            $inventory->updateAllocated($qty, 'allocate');
        }
    }

    private function deallocateInventory($sku, $itemType, $qty, $referenceId)
    {
        $inventory = Inventory::where('sku', $sku)
            ->where('item_type', $itemType)
            ->first();

        if ($inventory) {
            $inventory->updateAllocated($qty, 'deallocate');
        }
    }

    public function getInventoryStatus($filters = [])
    {
        $query = Inventory::with(['warehouse', 'location']);

        if (isset($filters['sku'])) {
            $query->where('sku', 'like', '%' . $filters['sku'] . '%');
        }

        if (isset($filters['item_type'])) {
            $query->where('item_type', $filters['item_type']);
        }

        if (isset($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        return $query->get()->map(function ($inventory) {
            $item = $inventory->item_type === 'material' 
                ? Material::where('sku', $inventory->sku)->first()
                : Product::where('sku', $inventory->sku)->first();

            return [
                'sku' => $inventory->sku,
                'item_type' => $inventory->item_type,
                'name' => $item ? $item->name : 'Unknown',
                'warehouse' => $inventory->warehouse->name,
                'location' => $inventory->location->name,
                'on_hand' => $inventory->on_hand,
                'allocated' => $inventory->allocated,
                'on_order' => $inventory->on_order,
                'available' => $inventory->available_qty,
                'total' => $inventory->total_qty,
                'is_low_stock' => $inventory->isLowStock(),
            ];
        });
    }

    public function getInventoryTransactions($filters = [])
    {
        $query = InventoryTransaction::with(['warehouse', 'location', 'createdBy']);

        if (isset($filters['sku'])) {
            $query->where('sku', $filters['sku']);
        }

        if (isset($filters['item_type'])) {
            $query->where('item_type', $filters['item_type']);
        }

        if (isset($filters['txn_type'])) {
            $query->where('txn_type', $filters['txn_type']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(50);
    }
}