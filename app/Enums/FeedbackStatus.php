<?php

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
