<?php

/**
 * 职位状态枚举
 *
 * @package     App\Enums
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Enums;

enum JobStatus: int
{
    case Pending = 1;
    case Published = 2;
    case Closed = 3;

    public function label(): string
    {
        return match ($this) {
            self::Pending => '待发布',
            self::Published => '发布中',
            self::Closed => '已关闭',
        };
    }
}
