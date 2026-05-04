<?php

namespace App\Http\Controllers\Ajax\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Lot;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SaleAjaxController extends Controller
{
    /**
     * Get list of Sales for Datatable.
     */
    public function list(Request $request): JsonResponse
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->get('search')['value'] ?? '';

        $query = Sale::with('customer');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $totalCount = Sale::count();
        $filteredCount = $query->count();

        $data = $query->orderBy('sale_date', 'desc')
            ->orderBy('invoice_number', 'desc')
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(function($sale) {
                return [
                    'id' => $sale->id,
                    'uuid' => $sale->uuid,
                    'invoice_number' => $sale->invoice_number,
                    'customer_name' => $sale->customer->name,
                    'sale_date' => $sale->sale_date->format('d M Y'),
                    'total_amount' => number_format($sale->total_amount, 2, '.', ''),
                    'status' => $sale->status,
                    'payment_method' => $sale->payment_method,
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
     * Search products with available stock.
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $q = $request->get('q');
        
        $products = Product::where('name', 'like', "%{$q}%")
            ->orWhere('sku', 'like', "%{$q}%")
            ->with(['lots' => function($query) {
                $query->where('remaining_quantity', '>', 0)->orderBy('created_at', 'asc');
            }])
            ->get()
            ->filter(function($p) {
                return $p->lots->count() > 0;
            })
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'text' => "{$p->name} (" . ($p->sku ?? 'No SKU') . ")",
                    'base_unit' => $p->base_unit,
                    'lots' => $p->lots->map(function($l) {
                        return [
                            'id' => $l->id,
                            'identifier' => $l->identifier,
                            'remaining_quantity' => $l->remaining_quantity,
                            'unit_cost' => $l->unit_cost, // maybe as suggestion price?
                        ];
                    })
                ];
            })
            ->values();

        return response()->json(['results' => $products]);
    }

    /**
     * Store new customer via AJAX.
     */
    public function storeCustomer(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:internal,external',
            'phone' => 'nullable|string|max:20',
        ]);

        $customer = Customer::create($request->only('name', 'type', 'phone', 'email', 'address'));

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil ditambahkan.',
            'customer' => $customer
        ]);
    }
}
