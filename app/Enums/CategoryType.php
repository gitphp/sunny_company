<?php

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
