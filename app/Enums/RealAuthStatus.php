<?php

/**
 * 实名认证状态枚举
 *
 * @package     App\Enums
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Enums;

enum RealAuthStatus: int
{
    case Unverified = 0;
    case Pending = 1;
    case Verified = 2;
    case Rejected = 3;

    public function label(): string
    {
        return match ($this) {
            self::Unverified => '未实名',
            self::Pending => '待审核',
            self::Verified => '已实名',
            self::Rejected => '实名驳回',
        };
    }
}
