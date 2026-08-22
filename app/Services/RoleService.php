<?php

namespace App\Services;

use App\Enums\RoleType;
use App\Models\AuthRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoleService
{
    public function __construct(private readonly RbacService $rbac) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function paginate(array $filters): array
    {
        $paginator = AuthRole::query()
            ->when(($filters['role_name'] ?? '') !== '', fn ($query) => $query->where('role_name', 'like', '%'.$filters['role_name'].'%'))
            ->when(($filters['role_code'] ?? '') !== '', fn ($query) => $query->where('role_code', 'like', '%'.$filters['role_code'].'%'))
            ->when(isset($filters['role_status']) && $filters['role_status'] !== '', fn ($query) => $query->where('role_status', $filters['role_status']))
            ->orderByDesc('role_sort')
            ->orderBy('id')
            ->paginate((int) ($filters['per_page'] ?? 10));

        return [
            'data' => collect($paginator->items())->map(fn (AuthRole $role) => $this->transform($role))->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function find(string $id): array
    {
        $role = AuthRole::query()->with(['menus:id', 'permissions:id'])->findOrFail($id);

        return [
            'role' => $this->transform($role, true),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function create(array $data): array
    {
        $role = DB::transaction(function () use ($data): AuthRole {
            $role = AuthRole::query()->create($this->payload($data, true));
            $this->syncAccess($role, $data);

            return $role->fresh();
        });

        return [
            'message' => '新增成功',
            'role' => $this->transform($role),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data): array
    {
        $role = DB::transaction(function () use ($id, $data): AuthRole {
            $role = AuthRole::query()->findOrFail($id);
            $role->fill($this->payload($data, false, $role));
            $role->save();
            $this->syncAccess($role, $data);

            return $role->fresh();
        });

        return [
            'message' => '修改成功',
            'role' => $this->transform($role),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function delete(string $id): array
    {
        $role = AuthRole::query()->findOrFail($id);

        if ($role->isSystem()) {
            throw ValidationException::withMessages([
                'id' => ['系统内置角色不能删除'],
            ]);
        }

        DB::transaction(function () use ($role): void {
            $role->menus()->detach();
            $role->permissions()->detach();
            $role->users()->detach();
            $role->delete();
        });

        return ['message' => '删除成功'];
    }

    /**
     * @return array<string, string>
     */
    public function changeStatus(string $id, int $status): array
    {
        $role = AuthRole::query()->findOrFail($id);

        if ($role->isSuperAdmin()) {
            throw ValidationException::withMessages([
                'id' => ['超级管理员不能禁用'],
            ]);
        }

        $role->forceFill(['role_status' => $status])->save();

        return ['message' => '状态已更新'];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data, bool $creating, ?AuthRole $role = null): array
    {
        $payload = [
            'role_name' => (string) ($data['role_name'] ?? ''),
            'role_sort' => (int) ($data['role_sort'] ?? 0),
            'data_scope' => (int) ($data['data_scope'] ?? 1),
            'scope_departments' => $data['scope_departments'] ?? [],
            'role_status' => (int) ($data['role_status'] ?? 1),
            'role_remark' => (string) ($data['role_remark'] ?? ''),
        ];

        if ($creating || ! $role?->isSystem()) {
            $payload['role_code'] = (string) ($data['role_code'] ?? '');
        }

        if ($creating) {
            $payload['role_type'] = RoleType::Custom;
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncAccess(AuthRole $role, array $data): void
    {
        $menuIds = $this->rbac->expandAncestorMenuIds(array_map('strval', $data['menu_ids'] ?? []));
        $role->menus()->sync($this->rbac->syncMap($menuIds));
        $role->permissions()->sync($this->rbac->syncMap(array_map('strval', $data['permission_ids'] ?? [])));
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(AuthRole $role, bool $withAccess = false): array
    {
        $data = [
            'id' => (string) $role->id,
            'role_name' => $role->role_name,
            'role_code' => $role->role_code,
            'role_type' => $role->role_type?->value,
            'role_type_label' => $role->role_type?->label(),
            'role_sort' => $role->role_sort,
            'data_scope' => $role->data_scope?->value,
            'data_scope_label' => $role->data_scope?->label(),
            'scope_departments' => collect($role->scope_departments ?? [])->map(fn ($id) => (string) $id)->all(),
            'role_status' => $role->role_status,
            'role_remark' => $role->role_remark,
            'created_at' => $role->created_at?->toDateTimeString(),
        ];

        if ($withAccess) {
            $data['menu_ids'] = $role->menus->map(fn ($menu) => (string) $menu->id)->all();
            $data['permission_ids'] = $role->permissions->map(fn ($permission) => (string) $permission->id)->all();
        }

        return $data;
    }
}
