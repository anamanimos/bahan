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

        $query = PurchaseRequisition::withCount('items');

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
                return [
                    'id' => $pr->identifier,
                    'date' => $pr->created_at->format('Y-m-d'),
                    'staff_name' => 'Admin Gudang', // Future: $pr->user->name
                    'items_count' => $pr->items_count,
                    'total_estimation' => 0, // Future: Calculate from items
                    'status' => $pr->status ?? 'Approved',
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
