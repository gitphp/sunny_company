<?php

namespace App\Enums;

enum UserStatus: int
{
    case Disabled = 0;
    case Normal = 1;
    case Frozen = 2;
    case Cancelled = 3;

    public function label(): string
    {
        return match ($this) {
            self::Disabled => '禁用',
            self::Normal => '正常',
            self::Frozen => '冻结',
            self::Cancelled => '注销',
        };
    }
}
