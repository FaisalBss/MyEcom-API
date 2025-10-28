<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlaceholderService
{

    public function fetchAndFilterPosts()
    {
        try {
            $response = Http::get('https://jsonplaceholder.typicode.com/posts');

            if ($response->failed()) {
                Log::error('Failed to fetch from JSONPlaceholder: ' . $response->body());
                return [
                    'success' => false,
                    'message' => 'Failed to fetch external data.'
                ];
            }

            $allPosts = $response->json();

            $filteredPosts = collect($allPosts)->take(5)->map(function ($post) {
                return [
                    'post_id' => $post['id'],
                    'title' => $post['title'],
                    'body_summary' => substr($post['body'], 0, 50) . '...'
                ];
            });

            return [
                'success' => true,
                'data' => $filteredPosts
            ];

        } catch (\Exception $e) {
            Log::error('Exception in PlaceholderService: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An internal error occurred.'
            ];
        }
    }
}
