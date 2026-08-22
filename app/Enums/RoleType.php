<?php

/**
 * 角色类型枚举
 *
 * @package     App\Enums
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Enums;

enum RoleType: int
{
    case System = 1;
    case Custom = 2;

    public function label(): string
    {
        return match ($this) {
            self::System => '系统内置',
            self::Custom => '自定义',
        };
    }
}
