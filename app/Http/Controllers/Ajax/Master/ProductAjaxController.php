<?php

namespace App\Http\Controllers\Ajax\Master;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductAjaxController extends Controller
{
    /**
     * Get list of Products for Datatable.
     */
    public function list(Request $request): JsonResponse
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->get('search')['value'] ?? '';

        $query = Product::with('category');

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%");
        }

        $totalCount = Product::count();
        $filteredCount = $query->count();

        $colors = \App\Models\Color::all()->pluck('hex_code', 'name');

        $data = $query->withSum('lots', 'remaining_quantity')
            ->orderBy('name', 'asc')
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(function($product) use ($colors) {
                $colorName = data_get($product->specifications, 'color', '-');
                return [
                    'id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'category_name' => $product->category?->name ?? '-',
                    'base_unit' => $product->base_unit,
                    'color' => $colorName,
                    'color_hex' => $colors[$colorName] ?? null,
                    'stock' => number_format($product->lots_sum_remaining_quantity ?? 0, 2, '.', ''),
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
