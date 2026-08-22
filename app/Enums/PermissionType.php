<?php

/**
 * 权限类型枚举
 *
 * @package     App\Enums
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Enums;

enum PermissionType: string
{
    case Menu = 'menu';
    case Button = 'button';
    case Api = 'api';

    public function label(): string
    {
        return match ($this) {
            self::Menu => '菜单',
            self::Button => '按钮',
            self::Api => '接口',
        };
    }
}
