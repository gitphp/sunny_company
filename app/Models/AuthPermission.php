<?php

/**
 * 权限模型
 *
 * @package     App\Models
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Models;

use App\Enums\PermissionType;
use App\Support\Snowflake;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

#[Fillable([
    'parent_id',
    'per_name',
    'per_code',
    'per_type',
    'per_path',
    'per_method',
    'per_icon',
    'per_sort',
    'per_status',
])]
class AuthPermission extends Model
{
    use SoftDeletes;

    protected $table = 'auth_permissions';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (AuthPermission $permission): void {
            if (empty($permission->id)) {
                $permission->id = Snowflake::id();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'per_type' => PermissionType::class,
            'deleted_at' => 'datetime',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(AuthRole::class, 'auth_role_permissions', 'permission_id', 'role_id');
    }

    /**
     * @return Collection<int, AuthPermission>
     */
    public static function ordered(): Collection
    {
        return static::query()
            ->orderByDesc('per_sort')
            ->orderBy('id')
            ->get();
    }

    /**
     * @param  Collection<int, AuthPermission>  $permissions
     * @return array<int, array<string, mixed>>
     */
    public static function buildTree(Collection $permissions, string $parentId = '0'): array
    {
        $branch = [];

        foreach ($permissions as $permission) {
            if ((string) $permission->parent_id !== $parentId) {
                continue;
            }

            $item = [
                'id' => (string) $permission->id,
                'parent_id' => (string) $permission->parent_id,
                'per_name' => $permission->per_name,
                'per_code' => $permission->per_code,
                'per_type' => $permission->per_type?->value,
                'per_type_label' => $permission->per_type?->label(),
                'per_path' => $permission->per_path,
                'per_method' => $permission->per_method,
                'per_icon' => $permission->per_icon,
                'per_sort' => $permission->per_sort,
                'per_status' => $permission->per_status,
            ];

            $children = self::buildTree($permissions, (string) $permission->id);

            if ($children !== []) {
                $item['children'] = $children;
            }

            $branch[] = $item;
        }

        return $branch;
    }
}
