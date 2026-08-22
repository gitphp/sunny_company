<?php

namespace App\Services;

use App\Models\AuthPermission;

class PermissionService
{
    /**
     * @return array<string, mixed>
     */
    public function tree(): array
    {
        return [
            'permissions' => AuthPermission::buildTree(AuthPermission::ordered()),
        ];
    }
}
