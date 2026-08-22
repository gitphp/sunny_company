<?php

namespace App\Services;

use App\Models\AuthRole;
use App\Models\HrDepartment;

class OptionService
{
    /**
     * @return array<string, mixed>
     */
    public function roles(): array
    {
        $roles = AuthRole::query()
            ->where('role_status', 1)
            ->orderByDesc('role_sort')
            ->get(['id', 'role_name', 'role_code']);

        return [
            'roles' => $roles->map(fn (AuthRole $role) => [
                'id' => (string) $role->id,
                'role_name' => $role->role_name,
                'role_code' => $role->role_code,
            ])->values(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function departments(): array
    {
        return [
            'departments' => HrDepartment::buildTree(
                HrDepartment::query()->where('dept_status', 1)->orderByDesc('dept_sort')->orderBy('id')->get()
            ),
        ];
    }
}
