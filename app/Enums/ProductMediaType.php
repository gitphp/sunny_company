<?php

/**
 * 商品媒体类型枚举
 *
 * @package     App\Enums
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Enums;

enum ProductMediaType: int
{
    case MainImage = 1;
    case DetailImage = 2;
    case Video = 3;
    case Certificate = 4;
    case Attachment = 5;

    public function label(): string
    {
        return match ($this) {
            self::MainImage => '主图',
            self::DetailImage => '详情图',
            self::Video => '视频',
            self::Certificate => '资质文件',
            self::Attachment => '其他附件',
        };
    }
}
