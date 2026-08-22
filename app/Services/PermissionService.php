<?php

/**
 * 权限服务类
 *
 * @package     App\Services
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

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
