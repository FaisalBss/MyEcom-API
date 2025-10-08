<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function __construct(private ProductService $productService)
    {
        $this->middleware('auth:api')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $search   = $request->input('search');
        $products = $this->productService->searchProducts($search);

        return response()->json([
            'success' => true,
            'data'    => $products
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->create($request->validated(), $request);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully!',
            'data'    => $product
        ], 201);
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $product
        ]);
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $this->productService->update($product, $request->validated(), $request);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully!',
            'data'    => $product
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $this->productService->delete($product);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully!'
        ]);
    }
}
