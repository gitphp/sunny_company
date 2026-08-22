<?php

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
