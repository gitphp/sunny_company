<?php

/**
 * RBAC服务类
 *
 * @package     App\Services
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Services;

use App\Enums\DataScope;
use App\Models\AuthMenu;
use App\Models\AuthPermission;
use App\Models\AuthRole;
use App\Models\HrDepartment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class RbacService
{
    public function isSuperAdmin(User $user): bool
    {
        return $this->activeRoles($user)->contains(fn (AuthRole $role): bool => $role->isSuperAdmin());
    }

    public function userCan(User $user, string $permission): bool
    {
        if ($permission === '') {
            return true;
        }

        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->permissionCodes($user)->contains($permission);
    }

    /**
     * @return Collection<int, AuthRole>
     */
    public function activeRoles(User $user): Collection
    {
        $user->loadMissing('roles');

        return $user->roles
            ->filter(fn (AuthRole $role): bool => (int) $role->role_status === 1);
    }

    /**
     * @return Collection<int, string>
     */
    public function permissionCodes(User $user): Collection
    {
        if ($this->isSuperAdmin($user)) {
            return AuthPermission::query()
                ->where('per_status', 1)
                ->pluck('per_code')
                ->filter()
                ->values();
        }

        $roleIds = $this->activeRoles($user)->pluck('id');

        if ($roleIds->isEmpty()) {
            return collect();
        }

        return AuthPermission::query()
            ->where('per_status', 1)
            ->whereHas('roles', fn (Builder $query) => $query->whereIn('auth_role.id', $roleIds))
            ->pluck('per_code')
            ->filter()
            ->values();
    }

    /**
     * @return list<string>
     */
    public function menuIds(User $user): array
    {
        if ($this->isSuperAdmin($user)) {
            return AuthMenu::query()->pluck('id')->map(fn ($id): string => (string) $id)->all();
        }

        $roleIds = $this->activeRoles($user)->pluck('id');

        if ($roleIds->isEmpty()) {
            return [];
        }

        $ids = AuthMenu::query()
            ->whereHas('roles', fn (Builder $query) => $query->whereIn('auth_role.id', $roleIds))
            ->pluck('id')
            ->map(fn ($id): string => (string) $id)
            ->all();

        return $this->expandAncestorMenuIds($ids);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function sidebarMenus(User $user): array
    {
        $allowed = array_fill_keys($this->menuIds($user), true);
        $tree = AuthMenu::buildTree(AuthMenu::ordered());

        return $this->filterMenuTree($tree, $allowed);
    }

    public function applyDataScope(Builder $query, User $user, string $deptColumn = 'dept_id', string $userColumn = 'id'): Builder
    {
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        $roles = $this->activeRoles($user);

        if ($roles->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        $scopes = $roles->map(fn (AuthRole $role) => $role->data_scope ?? DataScope::Self);

        if ($scopes->contains(DataScope::All)) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($roles, $user, $deptColumn, $userColumn): void {
            foreach ($roles as $role) {
                $builder->orWhere(function (Builder $scopeQuery) use ($role, $user, $deptColumn, $userColumn): void {
                    $this->constrainByScope($scopeQuery, $role, $user, $deptColumn, $userColumn);
                });
            }
        });
    }

    /**
     * @param  list<string>  $ids
     * @return list<string>
     */
    public function expandAncestorMenuIds(array $ids): array
    {
        $menus = AuthMenu::ordered()->keyBy(fn (AuthMenu $menu): string => (string) $menu->id);
        $set = [];

        foreach ($ids as $id) {
            $currentId = (string) $id;
            while ($currentId !== '0' && $currentId !== '') {
                $set[$currentId] = true;
                $current = $menus->get($currentId);
                $currentId = $current ? (string) $current->parent_id : '0';
            }
        }

        return array_keys($set);
    }

    /**
     * @param  list<string|int>  $ids
     * @return array<string, array{created_at: Carbon}>
     */
    public function syncMap(array $ids): array
    {
        $map = [];

        foreach ($ids as $id) {
            if ((string) $id === '' || (string) $id === '0') {
                continue;
            }

            $map[(string) $id] = ['created_at' => now()];
        }

        return $map;
    }

    private function constrainByScope(Builder $query, AuthRole $role, User $user, string $deptColumn, string $userColumn): void
    {
        $scope = $role->data_scope ?? DataScope::Self;
        $deptId = (string) $user->dept_id;

        match ($scope) {
            DataScope::DeptAndChildren => $query->whereIn($deptColumn, $deptId === '0' ? [0] : HrDepartment::selfAndDescendantIds($deptId)),
            DataScope::Dept => $query->where($deptColumn, $user->dept_id),
            DataScope::Custom => $query->whereIn($deptColumn, $role->scope_departments ?? []),
            default => $query->where($userColumn, $user->id),
        };
    }

    /**
     * @param  array<int, array<string, mixed>>  $tree
     * @param  array<string, bool>  $allowed
     * @return array<int, array<string, mixed>>
     */
    private function filterMenuTree(array $tree, array $allowed): array
    {
        $items = [];

        foreach ($tree as $node) {
            if (($node['menu_status'] ?? 1) !== 1) {
                continue;
            }

            if ($node['is_button'] ?? false) {
                continue;
            }

            if (isset($node['children'])) {
                $node['children'] = $this->filterMenuTree($node['children'], $allowed);
            }

            $id = (string) $node['id'];
            $hasChildren = ($node['children'] ?? []) !== [];

            if (! isset($allowed[$id]) && ! $hasChildren) {
                continue;
            }

            $items[] = $node;
        }

        return $items;
    }
}
