<?php

namespace App\Enums;

enum PermissionType: string
{
    case Menu = 'menu';
    case Button = 'button';
    case Api = 'api';

    public function label(): string
    {
        return match ($this) {
            self::Menu => '菜单',
            self::Button => '按钮',
            self::Api => '接口',
        };
    }
}
