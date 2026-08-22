<?php

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
