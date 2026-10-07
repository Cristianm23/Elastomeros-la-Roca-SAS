<?php

namespace App\Modules\Quotes\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Quotes\Requests\StoreQuoteRequest;
use App\Modules\Quotes\Resources\QuoteResource;
use App\Modules\Quotes\Services\QuoteService;
use Illuminate\Http\JsonResponse;
use App\Modules\Quotes\Models\Quote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function __construct(private readonly QuoteService $service) {}

    public function store(StoreQuoteRequest $request): JsonResponse
    {
        $quote = $this->service->create(
            $request->validated(),
            $request->user()?->id,
            $request->ip(),
        );

        return (new QuoteResource($quote))->response()->setStatusCode(201);
    }

    public function show(Request $request, string $reference): QuoteResource
    {
        $quote = Quote::query()->with('items')->where('public_reference', $reference)->firstOrFail();
        abort_unless($request->user()->can('view', $quote), 403);

        return new QuoteResource($quote);
    }
}
