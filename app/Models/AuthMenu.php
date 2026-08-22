<?php

/**
 * 菜单模型
 *
 * @package     App\Models
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Models;

use App\Enums\MenuStatus;
use App\Support\Snowflake;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

#[Fillable([
    'parent_id',
    'menu_name',
    'menu_icon',
    'menu_path',
    'component',
    'permission_code',
    'menu_sort',
    'menu_status',
])]
class AuthMenu extends Model
{
    use SoftDeletes;

    protected $table = 'auth_menus';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (AuthMenu $menu): void {
            if (empty($menu->id)) {
                $menu->id = Snowflake::id();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'menu_status' => MenuStatus::class,
            'deleted_at' => 'datetime',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderByDesc('menu_sort');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(AuthRole::class, 'auth_role_menus', 'menu_id', 'role_id');
    }

    public function isButton(): bool
    {
        return $this->permission_code !== '' && $this->menu_path === '' && $this->component === '';
    }

    /**
     * @return Collection<int, AuthMenu>
     */
    public static function ordered(): Collection
    {
        return static::query()
            ->orderByDesc('menu_sort')
            ->orderBy('id')
            ->get();
    }

    /**
     * @param  Collection<int, AuthMenu>  $menus
     * @return array<int, array<string, mixed>>
     */
    public static function buildTree(Collection $menus, string $parentId = '0'): array
    {
        $branch = [];

        foreach ($menus as $menu) {
            if ((string) $menu->parent_id !== $parentId) {
                continue;
            }

            $item = [
                'id' => (string) $menu->id,
                'parent_id' => (string) $menu->parent_id,
                'menu_name' => $menu->menu_name,
                'menu_icon' => $menu->menu_icon,
                'menu_path' => $menu->menu_path,
                'component' => $menu->component,
                'permission_code' => $menu->permission_code,
                'menu_sort' => $menu->menu_sort,
                'menu_status' => $menu->menu_status?->value,
                'is_button' => $menu->isButton(),
            ];

            $children = self::buildTree($menus, (string) $menu->id);

            if ($children !== []) {
                $item['children'] = $children;
            }

            $branch[] = $item;
        }

        return $branch;
    }
}
