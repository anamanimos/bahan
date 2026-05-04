<?php

namespace App\Http\Controllers\Ajax\Master;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ColorAjaxController extends Controller
{
    /**
     * List colors for DataTables.
     */
    public function list(Request $request): JsonResponse
    {
        $query = Color::query();

        if ($request->search['value']) {
            $query->where('name', 'like', '%' . $request->search['value'] . '%')
                  ->orWhere('hex_code', 'like', '%' . $request->search['value'] . '%');
        }

        $totalRecords = Color::count();
        $filteredRecords = $query->count();

        $colors = $query->skip($request->start)
                        ->take($request->length)
                        ->orderBy('name', 'asc')
                        ->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $colors
        ]);
    }

    /**
     * Store a newly created color via AJAX.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|unique:colors,name',
            'hex_code' => 'nullable'
        ]);

        $color = Color::create([
            'name' => $request->name,
            'hex_code' => $request->hex_code
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Warna berhasil ditambahkan.',
            'data' => $color
        ]);
    }

    /**
     * Update the specified color via AJAX.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $color = Color::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:colors,name,' . $id,
            'hex_code' => 'nullable'
        ]);

        $color->update([
            'name' => $request->name,
            'hex_code' => $request->hex_code
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Warna berhasil diperbarui.'
        ]);
    }

    /**
     * Remove the specified color from storage.
     */
    public function destroy(Request $request): JsonResponse
    {
        $ids = $request->ids;
        if (is_array($ids)) {
            Color::whereIn('id', $ids)->delete();
        } else {
            Color::findOrFail($request->id)->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Warna berhasil dihapus.'
        ]);
    }

    /**
     * Validate CSV import and find conflicts.
     */
    public function validateImport(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), "r");
        
        $header = true;
        $data = [];
        $conflicts = [];
        
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if ($header) {
                $header = false;
                continue;
            }
            
            if (empty($row[0])) continue;
            
            $name = trim($row[0]);
            $hex = trim($row[1] ?? '');
            
            $existing = Color::where('name', $name)->first();
            
            $item = [
                'name' => $name,
                'hex_code' => $hex
            ];

            if ($existing) {
                if ($existing->hex_code !== $hex) {
                    $conflicts[] = [
                        'name' => $name,
                        'imported_hex' => $hex,
                        'existing_hex' => $existing->hex_code,
                        'id' => $existing->id
                    ];
                }
            } else {
                $data[] = $item;
            }
        }
        
        fclose($handle);

        return response()->json([
            'success' => true,
            'new_data' => $data,
            'conflicts' => $conflicts
        ]);
    }

    /**
     * Finalize import with resolution.
     */
    public function confirmImport(Request $request): JsonResponse
    {
        $items = $request->items; // Array of {name, hex_code, action}
        
        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                if ($item['action'] === 'update') {
                    Color::updateOrCreate(
                        ['name' => $item['name']],
                        ['hex_code' => $item['hex_code']]
                    );
                } elseif ($item['action'] === 'create') {
                    Color::firstOrCreate(
                        ['name' => $item['name']],
                        ['hex_code' => $item['hex_code']]
                    );
                }
                // action 'skip' does nothing
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Import berhasil diselesaikan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
