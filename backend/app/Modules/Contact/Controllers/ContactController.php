<?php

namespace App\Modules\Contact\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Contact\Requests\StoreContactMessageRequest;
use App\Modules\Contact\Resources\ContactMessageResource;
use App\Modules\Contact\Services\ContactMessageService;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function __construct(private readonly ContactMessageService $service) {}

    public function store(StoreContactMessageRequest $request): JsonResponse
    {
        $message = $this->service->create($request->validated());

        return (new ContactMessageResource($message))
            ->response()
            ->setStatusCode(201);
    }
}
