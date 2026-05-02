<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/products/categories",
     *      operationId="getCategoryList",
     *      tags={"Products"},
     *      summary="Get list of categories",
     *      description="Returns list of categories",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="Kain")
     *              )
     *          )
     *       )
     *     )
     */
    public function index()
    {
        $categories = Category::select('id', 'name')->get();
        return response()->json($categories);
    }

    /**
     * @OA\Post(
     *      path="/api/products/categories",
     *      operationId="storeCategory",
     *      tags={"Products"},
     *      summary="Create new category",
     *      description="Create a new category",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Benang")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Category created successfully"
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
            'name' => 'required|string|max:255|unique:categories,name'
        ]);

        $category = Category::create($validated);

        return response()->json($category, 201);
    }

    /**
     * @OA\Put(
     *      path="/api/products/categories/{id}",
     *      operationId="updateCategory",
     *      tags={"Products"},
     *      summary="Update existing category",
     *      description="Update an existing category",
     *      @OA\Parameter(
     *          name="id",
     *          description="Category ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Updated Category Name")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Category updated successfully"
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Category not found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id
        ]);

        $category->update($validated);

        return response()->json($category);
    }
}
