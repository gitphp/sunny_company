<?php

/**
 * 角色模型
 *
 * @package     App\Models
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Models;

use App\Enums\DataScope;
use App\Enums\RoleType;
use App\Support\Snowflake;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'role_name',
    'role_code',
    'role_type',
    'role_sort',
    'data_scope',
    'scope_departments',
    'role_status',
    'role_remark',
])]
class AuthRole extends Model
{
    public const SUPER_ADMIN_CODE = 'super_admin';

    use SoftDeletes;

    protected $table = 'auth_role';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (AuthRole $role): void {
            if (empty($role->id)) {
                $role->id = Snowflake::id();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role_type' => RoleType::class,
            'data_scope' => DataScope::class,
            'scope_departments' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function isSystem(): bool
    {
        return $this->role_type === RoleType::System;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role_code === self::SUPER_ADMIN_CODE;
    }

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(AuthMenu::class, 'auth_role_menus', 'role_id', 'menu_id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(AuthPermission::class, 'auth_role_permissions', 'role_id', 'permission_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'auth_user_role', 'role_id', 'user_id');
    }
}
