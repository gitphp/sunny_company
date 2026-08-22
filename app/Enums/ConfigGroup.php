<?php

namespace App\Enums;

enum ConfigGroup: string
{
    case Basic = 'basic';
    case Seo = 'seo';
    case Contact = 'contact';
    case Social = 'social';

    public function label(): string
    {
        return match ($this) {
            self::Basic => '基础信息',
            self::Seo => 'SEO',
            self::Contact => '联系方式',
            self::Social => '社交账号',
        };
    }
}
