<?php

namespace App\Enums;

enum DataScope: int
{
    case All = 1;
    case DeptAndChildren = 2;
    case Dept = 3;
    case Self = 4;
    case Custom = 5;

    public function label(): string
    {
        return match ($this) {
            self::All => '全部数据',
            self::DeptAndChildren => '本部门及下级',
            self::Dept => '本部门',
            self::Self => '仅本人',
            self::Custom => '自定义部门',
        };
    }
}
