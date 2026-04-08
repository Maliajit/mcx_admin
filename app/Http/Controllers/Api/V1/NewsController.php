<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class NewsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $items = Post::query()
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Post $post): array => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'slug' => $post->slug,
                'views' => $post->views,
                'published_at' => optional($post->published_at)->toIso8601String(),
            ])
            ->values();

        return ApiResponse::success([
            'items' => $items->all(),
        ]);
    }
}
