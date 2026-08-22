<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthRole;
use App\Models\HrDepartment;
use Illuminate\Http\JsonResponse;

class OptionController extends Controller
{
    public function roles(): JsonResponse
    {
        $roles = AuthRole::query()
            ->where('role_status', 1)
            ->orderByDesc('role_sort')
            ->get(['id', 'role_name', 'role_code']);

        return response()->json([
            'roles' => $roles->map(fn (AuthRole $role) => [
                'id' => (string) $role->id,
                'role_name' => $role->role_name,
                'role_code' => $role->role_code,
            ])->values(),
        ]);
    }

    public function departments(): JsonResponse
    {
        return response()->json([
            'departments' => HrDepartment::buildTree(
                HrDepartment::query()->where('dept_status', 1)->orderByDesc('dept_sort')->orderBy('id')->get()
            ),
        ]);
    }
}
