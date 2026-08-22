<?php

namespace App\Enums;

enum ConfigInputType: string
{
    case Text = 'text';
    case Textarea = 'textarea';
    case Image = 'image';
    case File = 'file';
    case Json = 'json';

    public function label(): string
    {
        return match ($this) {
            self::Text => '单行文本',
            self::Textarea => '多行文本',
            self::Image => '图片',
            self::File => '文件',
            self::Json => 'JSON',
        };
    }
}
