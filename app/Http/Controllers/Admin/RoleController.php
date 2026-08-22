<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function __construct(private readonly RoleService $roles) {}

    public function index(RoleRequest $request): JsonResponse
    {
        return response()->json($this->roles->paginate($request->validated()));
    }

    public function show(RoleRequest $request): JsonResponse
    {
        return response()->json($this->roles->find($request->routeId('role')));
    }

    public function store(RoleRequest $request): JsonResponse
    {
        return response()->json($this->roles->create($request->validated()), 201);
    }

    public function update(RoleRequest $request): JsonResponse
    {
        return response()->json($this->roles->update($request->routeId('role'), $request->validated()));
    }

    public function destroy(RoleRequest $request): JsonResponse
    {
        return response()->json($this->roles->delete($request->routeId('role')));
    }

    public function changeStatus(RoleRequest $request): JsonResponse
    {
        return response()->json($this->roles->changeStatus(
            $request->routeId('role'),
            (int) $request->validated('role_status'),
        ));
    }
}
