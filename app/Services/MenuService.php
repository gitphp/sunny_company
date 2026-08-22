<?php

namespace App\Services;

use App\Models\AuthMenu;

class MenuService
{
    /**
     * @return array<string, mixed>
     */
    public function tree(): array
    {
        return [
            'menus' => AuthMenu::buildTree(AuthMenu::ordered()),
        ];
    }
}
