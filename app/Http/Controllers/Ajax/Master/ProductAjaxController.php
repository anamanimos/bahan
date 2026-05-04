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
        $filters = $request->get('filters', []);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Apply Category Filter
        if (!empty($filters['categories'])) {
            $query->whereHas('category', function($q) use ($filters) {
                $q->whereIn('name', (array)$filters['categories']);
            });
        }

        // Apply Color Filter
        if (!empty($filters['colors'])) {
            $query->whereIn('specifications->color', (array)$filters['colors']);
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

    /**
     * Duplicate a product.
     */
    public function duplicate(Request $request): JsonResponse
    {
        try {
            $id = $request->get('id');
            $product = Product::findOrFail($id);

            $newProduct = $product->replicate();
            
            $baseName = $product->name . ' - Copy';
            $newName = $baseName;
            $counter = 1;
            while (Product::where('name', $newName)->exists()) {
                $newName = $baseName . ' (' . $counter . ')';
                $counter++;
            }
            $newProduct->name = $newName;
            $newProduct->sku = null; // SKU must be unique if not nullable
            $newProduct->save();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diduplikasi.',
                'product' => $newProduct
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menduplikasi produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a product.
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $id = $request->get('id');
            $product = Product::findOrFail($id);
            $product->delete();
            
            // Trigger Webhook for deletion
            \App\Services\StockWebhookService::notify('delete', 'product', [
                'product_id' => $id,
                'sku' => $product->sku,
                'name' => $product->name,
                'deleted_at' => now()->toIso8601String()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Merge multiple products into one.
     */
    public function merge(Request $request): JsonResponse
    {
        try {
            $targetId = $request->get('target_id');
            $sourceIds = $request->get('source_ids'); // array of ids

            if (empty($sourceIds)) {
                return response()->json(['success' => false, 'message' => 'Pilih produk sumber untuk digabung.'], 400);
            }

            \DB::beginTransaction();

            // 1. Move Lots
            \DB::table('lots')->whereIn('product_id', $sourceIds)->update(['product_id' => $targetId]);

            // 2. Move Purchase Requisition Items
            \DB::table('purchase_requisition_items')->whereIn('product_id', $sourceIds)->update(['product_id' => $targetId]);

            // 3. Move Goods Receipt Items
            \DB::table('goods_receipt_items')->whereIn('product_id', $sourceIds)->update(['product_id' => $targetId]);

            // 4. Move Product Prices
            \DB::table('product_prices')->whereIn('product_id', $sourceIds)->update(['product_id' => $targetId]);

            // 5. Delete Source Products (Soft Delete if trait is used)
            Product::whereIn('id', $sourceIds)->update(['merged_into_id' => $targetId]);
            Product::whereIn('id', $sourceIds)->delete();

            \DB::commit();

            // Trigger Webhook for merge
            \App\Services\StockWebhookService::notify('merge', 'product', [
                'target_id' => $targetId,
                'source_ids' => $sourceIds,
                'timestamp' => now()->toIso8601String()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil digabung.'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menggabung produk: ' . $e->getMessage()
            ], 500);
        }
    }
}
