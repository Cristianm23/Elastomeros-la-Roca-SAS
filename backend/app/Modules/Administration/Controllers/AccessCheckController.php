<?php

namespace App\Modules\Administration\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AccessCheckController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json(['data' => ['message' => 'Acceso administrativo autorizado.']]);
    }
}
