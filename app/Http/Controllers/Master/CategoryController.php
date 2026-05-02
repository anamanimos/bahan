<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::with('parent')->get();
        $rootCategories = Category::whereNull('parent_id')->get();
        return view('pages.master.category.index', compact('categories', 'rootCategories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan.',
                'data' => $category
            ]);
        }

        return redirect()->route('master.category.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }
}
