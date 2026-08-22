<?php

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
