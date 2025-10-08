<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class HomeApiController extends Controller
{
    public function categories()
    {
        $categories = Category::paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $categories
        ]);
    }

    public function products(Request $request, $catid = null)
    {
        $query = Product::query();

        if ($catid) {
            $query->where('category_id', $catid);
        }

        $products = $query->paginate(9);

        return response()->json([
            'success' => true,
            'data'    => $products
        ]);
    }

    public function search(Request $request)
    {
        $searchKey = $request->input('searchKey');

        $products = Product::where('name', 'LIKE', '%' . $searchKey . '%')
            ->orWhere('description', 'LIKE', '%' . $searchKey . '%')
            ->paginate(9);

        return response()->json([
            'success' => true,
            'data'    => $products
        ]);
    }
}
