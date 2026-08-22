<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\AuthRole;
use App\Services\RbacService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(private readonly RbacService $rbac) {}

    public function index(Request $request): JsonResponse
    {
        $query = AuthRole::query()
            ->when($request->filled('role_name'), fn ($builder) => $builder->where('role_name', 'like', '%'.$request->string('role_name').'%'))
            ->when($request->filled('role_code'), fn ($builder) => $builder->where('role_code', 'like', '%'.$request->string('role_code').'%'))
            ->when($request->filled('role_status'), fn ($builder) => $builder->where('role_status', $request->integer('role_status')))
            ->orderByDesc('role_sort')
            ->orderBy('id');

        $perPage = (int) $request->integer('per_page', 10);
        $paginator = $query->paginate($perPage);

        return response()->json([
            'data' => collect($paginator->items())->map(fn (AuthRole $role) => $this->transform($role))->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function show(AuthRole $role): JsonResponse
    {
        $role->load(['menus:id', 'permissions:id']);

        return response()->json([
            'role' => $this->transform($role, true),
        ]);
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = AuthRole::query()->create($this->payload($request, true));
        $this->syncAccess($role, $request);

        return response()->json([
            'message' => '新增成功',
            'role' => $this->transform($role->fresh()),
        ], 201);
    }

    public function update(UpdateRoleRequest $request, AuthRole $role): JsonResponse
    {
        $role->fill($this->payload($request, false, $role));
        $role->save();
        $this->syncAccess($role, $request);

        return response()->json([
            'message' => '修改成功',
            'role' => $this->transform($role->fresh()),
        ]);
    }

    public function destroy(AuthRole $role): JsonResponse
    {
        if ($role->isSystem()) {
            abort(422, '系统内置角色不能删除');
        }

        $role->menus()->detach();
        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();

        return response()->json([
            'message' => '删除成功',
        ]);
    }

    public function changeStatus(Request $request, AuthRole $role): JsonResponse
    {
        if ($role->isSuperAdmin()) {
            abort(422, '超级管理员不能禁用');
        }

        $validated = $request->validate([
            'role_status' => ['required', 'integer', 'in:0,1'],
        ]);

        $role->forceFill(['role_status' => $validated['role_status']])->save();

        return response()->json([
            'message' => '状态已更新',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Request $request, bool $creating, ?AuthRole $role = null): array
    {
        $data = [
            'role_name' => $request->string('role_name')->toString(),
            'role_sort' => $request->integer('role_sort', 0),
            'data_scope' => $request->integer('data_scope', 1),
            'scope_departments' => $request->input('scope_departments', []),
            'role_status' => $request->integer('role_status', 1),
            'role_remark' => $request->string('role_remark')->toString(),
        ];

        if ($creating || ! $role?->isSystem()) {
            $data['role_code'] = $request->string('role_code')->toString();
        }

        if ($creating) {
            $data['role_type'] = RoleType::Custom;
        }

        return $data;
    }

    private function syncAccess(AuthRole $role, Request $request): void
    {
        $menuIds = $this->rbac->expandAncestorMenuIds(
            array_map('strval', $request->input('menu_ids', []))
        );

        $role->menus()->sync($this->rbac->syncMap($menuIds));
        $role->permissions()->sync($this->rbac->syncMap($request->input('permission_ids', [])));
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
