<?php

namespace App\Http\Controllers\Ajax\Master;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UnitAjaxController extends Controller
{
    /**
     * List units for DataTables.
     */
    public function list(Request $request): JsonResponse
    {
        $query = Unit::query();

        if ($request->search['value']) {
            $query->where('name', 'like', '%' . $request->search['value'] . '%')
                  ->orWhere('symbol', 'like', '%' . $request->search['value'] . '%');
        }

        $totalRecords = Unit::count();
        $filteredRecords = $query->count();

        $units = $query->skip($request->start)
                       ->take($request->length)
                       ->orderBy('name', 'asc')
                       ->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $units
        ]);
    }

    /**
     * Store a newly created unit via AJAX.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|unique:units,name',
            'symbol' => 'required|max:10',
            'description' => 'nullable'
        ]);

        $unit = Unit::create([
            'name' => $request->name,
            'symbol' => $request->symbol,
            'description' => $request->description
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Satuan berhasil ditambahkan.',
            'data' => $unit
        ]);
    }

    /**
     * Update the specified unit via AJAX.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $unit = Unit::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:units,name,' . $id,
            'symbol' => 'required|max:10',
            'description' => 'nullable'
        ]);

        $unit->update([
            'name' => $request->name,
            'symbol' => $request->symbol,
            'description' => $request->description
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Satuan berhasil diperbarui.'
        ]);
    }

    /**
     * Remove the specified unit from storage.
     */
    public function destroy(Request $request): JsonResponse
    {
        $ids = $request->ids;
        if (is_array($ids)) {
            Unit::whereIn('id', $ids)->delete();
        } else {
            Unit::findOrFail($request->id)->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Satuan berhasil dihapus.'
        ]);
    }
}
