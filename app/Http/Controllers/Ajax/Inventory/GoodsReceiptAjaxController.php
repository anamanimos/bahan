<?php

namespace App\Http\Controllers\Ajax\Inventory;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceipt;
use App\Models\PurchaseRequisition;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GoodsReceiptAjaxController extends Controller
{
    /**
     * Get list of Goods Receipts for Datatable.
     */
    public function list(Request $request): JsonResponse
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->get('search')['value'] ?? '';

        $query = GoodsReceipt::with(['supplier', 'purchaseRequisition'])
            ->withCount('items');

        if ($search) {
            $query->where('identifier', 'like', "%{$search}%")
                ->orWhereHas('supplier', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        }

        $totalCount = GoodsReceipt::count();
        $filteredCount = $query->count();

        $data = $query->orderBy('created_at', 'desc')
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(function($receipt) {
                return [
                    'id' => $receipt->identifier,
                    'purchase_requisition_identifier' => $receipt->purchaseRequisition?->identifier,
                    'date' => $receipt->received_date->format('Y-m-d'),
                    'supplier_name' => $receipt->supplier->name,
                    'items_count' => $receipt->items_count,
                ];
            });

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $filteredCount,
            'data' => $data,
        ]);
    }

    /**
     * Search products for autocomplete.
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $term = $request->get('q');
        $products = Product::where('name', 'like', "%{$term}%")
            ->orWhere('sku', 'like', "%{$term}%")
            ->limit(10)
            ->get();

        $results = $products->map(function($product) {
            return [
                'id' => $product->id,
                'text' => "[{$product->sku}] {$product->name}",
                'base_unit' => $product->base_unit
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * Search suppliers for autocomplete.
     */
    public function searchSuppliers(Request $request): JsonResponse
    {
        $term = $request->get('q');
        $suppliers = Supplier::where('name', 'like', "%{$term}%")
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($suppliers);
    }

    /**
     * Get Purchase Requisition details for form populating.
     */
    public function getPurchaseRequisition(Request $request): JsonResponse
    {
        $identifier = $request->get('identifier');
        $pr = PurchaseRequisition::with(['items.product', 'items.supplier'])
            ->where('identifier', $identifier)
            ->first();

        if (!$pr) {
            return response()->json(['message' => 'Purchase Requisition not found'], 404);
        }

        $items = $pr->items->where('status', 'Approved')->map(function($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'order_reference' => $item->erp_order_reference,
                'requested_quantity' => $item->requested_quantity,
                'unit' => $item->unit,
                'estimated_unit_price' => $item->estimated_unit_price,
            ];
        })->values();

        return response()->json([
            'identifier' => $pr->identifier,
            'supplier_id' => $pr->items->first()?->supplier_id,
            'items' => $items
        ]);
    }
}
