<?php

namespace App\Modules\Analytics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Analytics\Requests\StoreAnalyticsEventRequest;
use App\Modules\Analytics\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    public function __construct(private readonly AnalyticsService $service) {}

    public function store(StoreAnalyticsEventRequest $request): JsonResponse
    {
        $this->service->record($request->validated(), $request);

        return response()->json(['data' => ['recorded' => true]], 202);
    }
}
