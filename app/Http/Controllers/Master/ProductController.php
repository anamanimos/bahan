<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index()
    {
        $categories = Category::all();
        return view('pages.master.product.index', compact('categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::all();
        $colors = \App\Models\Color::all();
        return view('pages.master.product.create', compact('categories', 'colors'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sku' => 'nullable|unique:products,sku',
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'base_unit' => 'required',
            'minimum_stock_level' => 'nullable|numeric',
        ]);

        $data = $request->all();
        
        // Default selling_unit to base_unit if not provided
        if (!isset($data['selling_unit'])) {
            $data['selling_unit'] = $request->base_unit;
        }

        // Bundle specifications
        $data['specifications'] = [
            'width' => $request->spec_width,
            'grammage' => $request->spec_grammage,
            'composition' => $request->spec_composition,
            'color' => $request->spec_color,
            'motif' => $request->spec_motif,
        ];

        // Handle tags (if any)
        if ($request->tags) {
            $data['specifications']['tags'] = explode(',', $request->tags);
        }

        Product::create($data);

        return redirect()->route('master.product.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }
}
