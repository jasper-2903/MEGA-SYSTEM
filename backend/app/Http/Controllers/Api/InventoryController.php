<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventory = Inventory::query()
            ->orderByDesc('updated_at')
            ->paginate(20);

        return response()->json($inventory);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:64',
            'item_type' => 'required|in:material,product',
            'warehouse_id' => 'required|exists:warehouses,id',
            'location_id' => 'required|exists:locations,id',
            'on_hand' => 'numeric|min:0',
            'allocated' => 'numeric|min:0',
            'on_order' => 'numeric|min:0',
        ]);

        $inventory = Inventory::create($validated);
        return response()->json($inventory, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $inventory = Inventory::findOrFail($id);
        return response()->json($inventory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $inventory = Inventory::findOrFail($id);

        $validated = $request->validate([
            'sku' => 'sometimes|required|string|max:64',
            'item_type' => 'sometimes|required|in:material,product',
            'warehouse_id' => 'sometimes|required|exists:warehouses,id',
            'location_id' => 'sometimes|required|exists:locations,id',
            'on_hand' => 'numeric|min:0',
            'allocated' => 'numeric|min:0',
            'on_order' => 'numeric|min:0',
        ]);

        $inventory->fill($validated)->save();
        return response()->json($inventory);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:inventory,id',
            'delta_on_hand' => 'nullable|numeric',
            'delta_allocated' => 'nullable|numeric',
            'delta_on_order' => 'nullable|numeric',
        ]);

        $inventory = Inventory::findOrFail($validated['id']);

        if (array_key_exists('delta_on_hand', $validated)) {
            $inventory->on_hand = max(0, $inventory->on_hand + (float) $validated['delta_on_hand']);
        }
        if (array_key_exists('delta_allocated', $validated)) {
            $inventory->allocated = max(0, $inventory->allocated + (float) $validated['delta_allocated']);
        }
        if (array_key_exists('delta_on_order', $validated)) {
            $inventory->on_order = max(0, $inventory->on_order + (float) $validated['delta_on_order']);
        }

        $inventory->save();
        return response()->json($inventory);
    }
}
