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
        $colors = \App\Models\Color::all();
        return view('pages.master.product.index', compact('categories', 'colors'));
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
            'name' => 'required|unique:products,name',
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

        $product = Product::create($data);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan.',
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'base_unit' => $product->base_unit
                ]
            ]);
        }

        return redirect()->route('master.product.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }
    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        $colors = \App\Models\Color::all();
        return view('pages.master.product.edit', compact('product', 'categories', 'colors'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'sku' => 'nullable|unique:products,sku,' . $id,
            'name' => 'required|unique:products,name,' . $id,
            'category_id' => 'required|exists:categories,id',
            'base_unit' => 'required',
            'minimum_stock_level' => 'nullable|numeric',
        ]);

        $data = $request->all();
        
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
            $data['specifications']['tags'] = is_array($request->tags) ? $request->tags : explode(',', $request->tags);
        }

        $product->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diperbarui.'
            ]);
        }

        return redirect()->route('master.product.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Export products to CSV.
     */
    public function exportCsv(Request $request)
    {
        $productIds = $request->input('product_ids', []);
        
        $query = Product::with('category');
        if (!empty($productIds)) {
            $query->whereIn('id', $productIds);
        }
        
        $products = $query->get();

        $filename = "products_export_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'SKU', 'Name', 'Description', 'Category Name', 'Base Unit', 'Selling Unit', 
            'Minimum Stock Level', 'Is Active', 'Spec Width', 'Spec Grammage', 
            'Spec Composition', 'Spec Color', 'Spec Motif'
        ];

        $callback = function() use($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $product) {
                $specs = $product->specifications ?? [];
                $row = [
                    $product->sku,
                    $product->name,
                    $product->description,
                    $product->category ? $product->category->name : '',
                    $product->base_unit,
                    $product->selling_unit,
                    $product->minimum_stock_level,
                    $product->is_active ? '1' : '0',
                    $specs['width'] ?? '',
                    $specs['grammage'] ?? '',
                    $specs['composition'] ?? '',
                    $specs['color'] ?? '',
                    $specs['motif'] ?? ''
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import products from CSV.
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), "r");
        
        $header = true;
        $successCount = 0;
        $skippedCount = 0;
        
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if ($header) {
                $header = false;
                continue;
            }
            
            if (empty($row[1])) continue; // Name is required
            
            $sku = $row[0] ?? null;
            $categoryName = trim($row[3] ?? '');
            
            $categoryId = null;
            if ($categoryName) {
                $category = Category::firstOrCreate(['name' => $categoryName]);
                $categoryId = $category->id;
            } else {
                $category = Category::first();
                $categoryId = $category ? $category->id : null;
            }
            
            $data = [
                'name' => $row[1],
                'description' => $row[2] ?? null,
                'category_id' => $categoryId,
                'base_unit' => $row[4] ?? 'Pcs',
                'selling_unit' => $row[5] ?? ($row[4] ?? 'Pcs'),
                'minimum_stock_level' => isset($row[6]) && $row[6] !== '' ? $row[6] : 0,
                'is_active' => isset($row[7]) ? (bool)$row[7] : true,
                'specifications' => [
                    'width' => $row[8] ?? null,
                    'grammage' => $row[9] ?? null,
                    'composition' => $row[10] ?? null,
                    'color' => $row[11] ?? null,
                    'motif' => $row[12] ?? null,
                ]
            ];
            
            $baseName = $row[1];
            $newName = $baseName;
            
            $existingByName = Product::where('name', $newName)->first();
            
            if ($sku) {
                $product = Product::where('sku', $sku)->first();
                if ($product) {
                    if ($existingByName && $existingByName->id !== $product->id) {
                        $counter = 1;
                        $newName = $baseName . ' - Copy';
                        while (Product::where('name', $newName)->exists()) {
                            $newName = $baseName . ' - Copy (' . $counter . ')';
                            $counter++;
                        }
                    }
                    $data['name'] = $newName;
                    $product->update($data);
                } else {
                    if ($existingByName) {
                        $counter = 1;
                        $newName = $baseName . ' - Copy';
                        while (Product::where('name', $newName)->exists()) {
                            $newName = $baseName . ' - Copy (' . $counter . ')';
                            $counter++;
                        }
                    }
                    $data['name'] = $newName;
                    $data['sku'] = $sku;
                    Product::create($data);
                }
            } else {
                if ($existingByName) {
                    $counter = 1;
                    $newName = $baseName . ' - Copy';
                    while (Product::where('name', $newName)->exists()) {
                        $newName = $baseName . ' - Copy (' . $counter . ')';
                        $counter++;
                    }
                }
                $data['name'] = $newName;
                Product::create($data);
            }
            
            $successCount++;
        }
        
        fclose($handle);
        
        $message = "$successCount produk berhasil di-import.";
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Download CSV template for import.
     */
    public function downloadTemplate()
    {
        $filename = "product_import_template.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'SKU', 'Name', 'Description', 'Category Name', 'Base Unit', 'Selling Unit', 
            'Minimum Stock Level', 'Is Active', 'Spec Width', 'Spec Grammage', 
            'Spec Composition', 'Spec Color', 'Spec Motif'
        ];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, [
                'SKU-001', 'Kain Combed 30s', 'Kain katun berkualitas', 'Cotton Combed', 'Kg', 'Roll', '10', '1', '36"', '140-150', '100% Cotton', 'Hitam Reaktif', 'Polos'
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
