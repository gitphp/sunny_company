<?php

/**
 * 分类类型枚举
 *
 * @package     App\Enums
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Enums;

enum CategoryType: int
{
    case Article = 0;
    case Navigation = 1;

    public function label(): string
    {
        return match ($this) {
            self::Article => '文章分类',
            self::Navigation => '导航分类',
        };
    }
}
