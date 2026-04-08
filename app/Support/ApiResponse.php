<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * @param array<string, mixed> $data
     */
    public static function success(array $data = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'version' => 'v1',
            'timestamp' => now()->toIso8601String(),
            'data' => $data,
            'error' => null,
        ], $status);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function error(string $error, int $status, array $data = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'version' => 'v1',
            'timestamp' => now()->toIso8601String(),
            'data' => $data,
            'error' => $error,
        ], $status);
    }
}
