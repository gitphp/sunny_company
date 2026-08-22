<?php

/**
 * 文章内容类型枚举
 *
 * @package     App\Enums
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Enums;

enum ArticleContentType: int
{
    case RichText = 1;
    case Markdown = 2;
    case PlainText = 3;

    public function label(): string
    {
        return match ($this) {
            self::RichText => '富文本',
            self::Markdown => 'Markdown',
            self::PlainText => '纯文本',
        };
    }
}
