<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryApiController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
        $this->middleware('auth:api')->except(['index', 'show']);
    }

    public function index()
    {
        $categories = Category::paginate(10);

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->createCategory($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Category added successfully!',
            'data' => $category
        ], 201);
    }

    public function show(Category $category)
    {
        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $updated = $this->categoryService->updateCategory($category, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully!',
            'data' => $updated
        ]);
    }

    public function destroy(Category $category)
    {
        $this->categoryService->deleteCategory($category);

        return response()->json([
            'success' => true,
            'message' => 'Category and its products deleted successfully!'
        ]);
    }
}
