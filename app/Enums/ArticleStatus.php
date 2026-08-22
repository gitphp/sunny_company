<?php

namespace App\Enums;

enum ArticleStatus: int
{
    case Draft = 1;
    case Pending = 2;
    case Approved = 3;
    case Published = 4;
    case Offline = 5;
    case Rejected = 6;
    case Trash = 7;

    public function label(): string
    {
        return match ($this) {
            self::Draft => '草稿',
            self::Pending => '待审核',
            self::Approved => '审核通过',
            self::Published => '已发布',
            self::Offline => '已下线',
            self::Rejected => '审核驳回',
            self::Trash => '回收站',
        };
    }
}
