<?php

namespace App\Http\Controllers\Ajax\Master;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryAjaxController extends Controller
{
    /**
     * Store a newly created category via AJAX.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        $category = Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan.',
            'data' => $category
        ]);
    }
}
