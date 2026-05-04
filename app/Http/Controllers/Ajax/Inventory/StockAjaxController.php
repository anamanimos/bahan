<?php

namespace App\Http\Controllers\Ajax\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Lot;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StockAjaxController extends Controller
{
    /**
     * Get list of Stocks for Datatable.
     */
    public function list(Request $request): JsonResponse
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->get('search')['value'] ?? '';
        $order = $request->get('order')[0] ?? null;

        $query = Product::with('category')->withSum('lots', 'remaining_quantity');
        $filters = $request->get('filters', []);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('products.name', 'like', "%{$search}%")
                  ->orWhere('products.sku', 'like', "%{$search}%");
            });
        }

        // Apply Category Filter
        if (!empty($filters['categories'])) {
            $query->whereHas('category', function($q) use ($filters) {
                $q->whereIn('name', (array)$filters['categories']);
            });
        }

        // Apply Stock Status Filter
        if (!empty($filters['stock_status'])) {
            if ($filters['stock_status'] === 'available') {
                $query->having('lots_sum_remaining_quantity', '>=', 10);
            } elseif ($filters['stock_status'] === 'low') {
                $query->having('lots_sum_remaining_quantity', '>', 0)->having('lots_sum_remaining_quantity', '<', 10);
            } elseif ($filters['stock_status'] === 'out') {
                $query->havingRaw('COALESCE(lots_sum_remaining_quantity, 0) <= 0');
            }
        }

        $totalCount = Product::count();
        
        // Robust way to count when using having
        $filteredCount = \DB::table(\DB::raw("({$query->toSql()}) as sub"))
            ->mergeBindings($query->getQuery())
            ->count();

        // Ordering
        if ($order) {
            $colIndex = $order['column'];
            $dir = $order['dir'];
            if ($colIndex == 0) { // Produk
                $query->orderBy('products.name', $dir);
            } elseif ($colIndex == 1) { // Kategori
                $query->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                    ->orderBy('categories.name', $dir)
                    ->select('products.*');
            } elseif ($colIndex == 3) { // Total Stock
                $query->orderBy('lots_sum_remaining_quantity', $dir);
            } else {
                $query->orderBy('products.name', 'asc');
            }
        } else {
            $query->orderBy('products.name', 'asc');
        }

        $data = $query->with(['lots' => function($q) {
                $q->where('remaining_quantity', '>', 0)->orderBy('created_at', 'asc');
            }])
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(function($product) {
                $totalStock = $product->lots_sum_remaining_quantity ?? 0;
                
                $status = 'available';
                if ($totalStock <= 0) $status = 'out';
                elseif ($totalStock < 10) $status = 'low';

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category_name' => $product->category?->name ?? '-',
                    'base_unit' => $product->base_unit,
                    'lot_count' => $product->lots->count(),
                    'total_stock' => number_format($totalStock, 2, '.', ''),
                    'status' => $status,
                    'lots' => $product->lots->map(function($lot) {
                        return [
                            'identifier' => $lot->identifier,
                            'created_at' => $lot->created_at->format('d M Y'),
                            'initial_quantity' => number_format($lot->initial_quantity, 2, '.', ''),
                            'remaining_quantity' => number_format($lot->remaining_quantity, 2, '.', ''),
                        ];
                    })
                ];
            });

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $filteredCount,
            'data' => $data,
        ]);
    }
}
