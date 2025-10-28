<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PlaceholderService;

class PlaceholderPostController extends Controller
{
    public function __construct(protected PlaceholderService $placeholderService)
    {
    }

    public function getPosts(Request $request)
    {
        $result = $this->placeholderService->fetchAndFilterPosts();

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $result['data']
        ]);
    }
}
