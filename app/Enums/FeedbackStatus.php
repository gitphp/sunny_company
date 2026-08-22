<?php

/**
 * 留言状态枚举
 *
 * @package     App\Enums
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Enums;

enum FeedbackStatus: int
{
    case Pending = 0;
    case Processed = 1;

    public function label(): string
    {
        return match ($this) {
            self::Pending => '未处理',
            self::Processed => '已处理',
        };
    }
}
