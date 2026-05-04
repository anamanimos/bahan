<?php

namespace App\Http\Controllers\Ajax\Inventory;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequisition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PurchaseRequisitionAjaxController extends Controller
{
    /**
     * Get list of Purchase Requisitions for Datatable.
     */
    public function list(Request $request): JsonResponse
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->get('search')['value'] ?? '';

        $query = PurchaseRequisition::with(['items']);

        if ($search) {
            $query->where('identifier', 'like', "%{$search}%");
        }

        $totalCount = PurchaseRequisition::count();
        $filteredCount = $query->count();

        $data = $query->orderBy('created_at', 'desc')
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(function($pr) {
                $total = $pr->items->sum(function($item) {
                    return $item->requested_quantity * $item->estimated_unit_price;
                });

                return [
                    'id' => $pr->identifier,
                    'date' => $pr->created_at->format('Y-m-d'),
                    'staff_name' => 'Admin Gudang',
                    'items_count' => $pr->items->count(),
                    'total_estimation' => $total,
                    'status' => $pr->status ?? 'Submitted',
                    'can_verify' => auth()->user()->hasRole('admin') && $pr->status === 'Submitted',
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
