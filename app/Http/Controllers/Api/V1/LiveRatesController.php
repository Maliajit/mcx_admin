<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\LiveRates\LiveRatesService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class LiveRatesController extends Controller
{
    public function __invoke(LiveRatesService $liveRatesService): JsonResponse
    {
        $result = $liveRatesService->getLiveRates();

        if ($result['success']) {
            return ApiResponse::success($result['data'], $result['status']);
        }

        return ApiResponse::error($result['error'] ?? 'Unknown error.', $result['status'], $result['data']);
    }
}
