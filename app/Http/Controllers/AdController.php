<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdRequest;
use App\Services\AdMaterialService;
use Illuminate\Http\JsonResponse;

class AdController extends Controller
{
    public function __construct(private readonly AdMaterialService $materials) {}

    public function show(AdRequest $request): JsonResponse
    {
        return response()->json($this->materials->activeByCode($request->route('code')));
    }
}
