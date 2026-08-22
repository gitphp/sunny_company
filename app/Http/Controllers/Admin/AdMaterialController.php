<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdMaterialRequest;
use App\Services\AdMaterialService;
use Illuminate\Http\JsonResponse;

class AdMaterialController extends Controller
{
    public function __construct(private readonly AdMaterialService $materials) {}

    public function index(AdMaterialRequest $request): JsonResponse
    {
        return response()->json($this->materials->paginate($request->validated()));
    }

    public function store(AdMaterialRequest $request): JsonResponse
    {
        return response()->json($this->materials->create($request->validated()), 201);
    }

    public function update(AdMaterialRequest $request): JsonResponse
    {
        return response()->json($this->materials->update($request->routeId('material'), $request->validated()));
    }

    public function destroy(AdMaterialRequest $request): JsonResponse
    {
        return response()->json($this->materials->delete($request->routeId('material')));
    }

    public function changeStatus(AdMaterialRequest $request): JsonResponse
    {
        return response()->json($this->materials->changeStatus(
            $request->routeId('material'),
            (int) $request->validated('status'),
        ));
    }
}
