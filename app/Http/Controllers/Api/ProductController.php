<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="API Documentation",
 *      description="API Documentation for the application",
 *      @OA\Contact(
 *          email="admin@example.com"
 *      )
 * )
 */
class ProductController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/products",
     *      operationId="getProductList",
     *      tags={"Products"},
     *      summary="Get list of products",
     *      description="Returns list of products with id and name",
     *      @OA\Parameter(
     *          name="search",
     *          description="Search term (by name or sku)",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="per_page",
     *          description="Number of items per page (default: 15)",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="current_page", type="integer", example=1),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="Kain Cotton Combed 30s")
     *                  )
     *              )
     *          )
     *       )
     *     )
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('sku', 'like', "%{$searchTerm}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $products = $query->select('id', 'name', 'sku')->paginate($perPage);

        return response()->json($products);
    }

    /**
     * @OA\Get(
     *      path="/api/products/{id}",
     *      operationId="getProductById",
     *      tags={"Products"},
     *      summary="Get product detail",
     *      description="Returns product detail data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Product ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Product not found"
     *      )
     * )
     */
    public function show($id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            // Check if it was merged
            $deletedProduct = Product::withTrashed()->find($id);
            if ($deletedProduct && $deletedProduct->merged_into_id) {
                $targetProduct = Product::find($deletedProduct->merged_into_id);
                if ($targetProduct) {
                    $responseData = $targetProduct->toArray();
                    $responseData['was_merged_from'] = $deletedProduct->id;
                    $responseData['_message'] = 'The requested product ID has been merged into this product.';
                    return response()->json($responseData);
                }
            }
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    /**
     * @OA\Post(
     *      path="/api/products",
     *      operationId="storeProduct",
     *      tags={"Products"},
     *      summary="Create new product",
     *      description="Create a new product",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"sku","name","category_id"},
     *              @OA\Property(property="sku", type="string", example="SKU-001"),
     *              @OA\Property(property="name", type="string", example="New Product"),
     *              @OA\Property(property="category_id", type="integer", example=1),
     *              @OA\Property(property="description", type="string", example="Product description"),
     *              @OA\Property(property="base_unit", type="string", example="kg"),
     *              @OA\Property(property="selling_unit", type="string", example="roll"),
     *              @OA\Property(property="conversion_factor", type="number", format="float", example=25.5),
     *              @OA\Property(property="warehouse_location", type="string", example="Rack A1")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Product created successfully"
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|unique:products,sku',
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'description' => 'nullable|string',
            'base_unit' => 'nullable|string',
            'selling_unit' => 'nullable|string',
            'conversion_factor' => 'nullable|numeric',
            'warehouse_location' => 'nullable|string',
            'minimum_stock_level' => 'nullable|numeric',
            'is_active' => 'nullable|boolean'
        ]);

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    /**
     * @OA\Put(
     *      path="/api/products/{id}",
     *      operationId="updateProduct",
     *      tags={"Products"},
     *      summary="Update existing product",
     *      description="Update an existing product",
     *      @OA\Parameter(
     *          name="id",
     *          description="Product ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="sku", type="string", example="SKU-001"),
     *              @OA\Property(property="name", type="string", example="Updated Product"),
     *              @OA\Property(property="category_id", type="integer", example=1),
     *              @OA\Property(property="description", type="string", example="Updated description"),
     *              @OA\Property(property="base_unit", type="string", example="kg"),
     *              @OA\Property(property="selling_unit", type="string", example="roll"),
     *              @OA\Property(property="conversion_factor", type="number", format="float", example=25.5),
     *              @OA\Property(property="warehouse_location", type="string", example="Rack A2")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Product updated successfully"
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Product not found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validated = $request->validate([
            'sku' => 'nullable|string|unique:products,sku,' . $id,
            'name' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer',
            'description' => 'nullable|string',
            'base_unit' => 'nullable|string',
            'selling_unit' => 'nullable|string',
            'conversion_factor' => 'nullable|numeric',
            'warehouse_location' => 'nullable|string',
            'minimum_stock_level' => 'nullable|numeric',
            'is_active' => 'nullable|boolean'
        ]);

        $product->update($validated);

        return response()->json($product);
    }
}
