<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DepartmentRequest;
use App\Services\DepartmentService;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    public function __construct(private readonly DepartmentService $departments) {}

    public function index(DepartmentRequest $request): JsonResponse
    {
        return response()->json($this->departments->tree());
    }

    public function store(DepartmentRequest $request): JsonResponse
    {
        return response()->json($this->departments->create(
            $request->validated(),
            (string) ($request->user()?->id ?? 0),
        ), 201);
    }

    public function update(DepartmentRequest $request): JsonResponse
    {
        return response()->json($this->departments->update(
            $request->routeId('department'),
            $request->validated(),
        ));
    }

    public function destroy(DepartmentRequest $request): JsonResponse
    {
        return response()->json($this->departments->delete($request->routeId('department')));
    }
}
