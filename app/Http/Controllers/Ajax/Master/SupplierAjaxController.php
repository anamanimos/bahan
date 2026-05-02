<?php

namespace App\Http\Controllers\Ajax\Master;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SupplierAjaxController extends Controller
{
    /**
     * List suppliers for DataTables.
     */
    public function list(Request $request): JsonResponse
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->get('search')['value'] ?? '';

        $query = Supplier::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }

        $totalCount = Supplier::count();
        $filteredCount = $query->count();

        $data = $query->orderBy('name', 'asc')
            ->offset($start)
            ->limit($length)
            ->get();

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $filteredCount,
            'data' => $data,
        ]);
    }

    /**
     * Store a new supplier.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|unique:suppliers,name',
            ]);

            $supplier = Supplier::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Supplier berhasil ditambahkan.',
                'data' => $supplier
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan supplier: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Update an existing supplier.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $supplier = Supplier::findOrFail($id);

            $request->validate([
                'name' => 'required|unique:suppliers,name,' . $id,
            ]);

            $supplier->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Supplier berhasil diperbarui.',
                'data' => $supplier
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui supplier: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Delete a supplier (Soft Delete).
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $id = $request->get('id');
            $supplier = Supplier::findOrFail($id);
            $supplier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Supplier berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Merge multiple suppliers into one.
     */
    public function merge(Request $request): JsonResponse
    {
        $sourceIds = $request->input('source_ids');
        $targetId = $request->input('target_id');

        if (empty($sourceIds) || !$targetId) {
            return response()->json(['success' => false, 'message' => 'Data tidak lengkap.'], 422);
        }

        $sourceIds = array_diff($sourceIds, [$targetId]);

        if (empty($sourceIds)) {
            return response()->json(['success' => false, 'message' => 'Supplier sumber tidak valid.'], 422);
        }

        try {
            \DB::beginTransaction();

            $targetSupplier = Supplier::findOrFail($targetId);

            if (\Schema::hasTable('purchase_requisition_items')) {
                \DB::table('purchase_requisition_items')
                    ->whereIn('supplier_id', $sourceIds)
                    ->update(['supplier_id' => $targetId]);
            }

            if (\Schema::hasTable('goods_receipts')) {
                \DB::table('goods_receipts')
                    ->whereIn('supplier_id', $sourceIds)
                    ->update(['supplier_id' => $targetId]);
            }

            Supplier::whereIn('id', $sourceIds)->delete();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($sourceIds) . ' supplier berhasil digabungkan ke ' . $targetSupplier->name
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menggabungkan supplier: ' . $e->getMessage()
            ], 500);
        }
    }
}
