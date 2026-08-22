<?php

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
