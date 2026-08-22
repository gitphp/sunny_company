<?php

namespace Database\Seeders;

use App\Enums\DataScope;
use App\Enums\PermissionType;
use App\Enums\RoleType;
use App\Models\AuthMenu;
use App\Models\AuthPermission;
use App\Models\AuthRole;
use App\Models\HrDepartment;
use App\Models\User;
use App\Support\Snowflake;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureMenuMeta();
        $root = $this->seedDepartments();
        $this->syncPermissionsFromMenus();
        $super = $this->seedSuperAdminRole();
        $this->assignAdmin($root, $super);
    }

    private function ensureMenuMeta(): void
    {
        $this->updateMenu('/system/role', 'system/role/Index', 'system:role:list', [
            ['新增', 'system:role:add', 50],
            ['修改', 'system:role:edit', 40],
            ['删除', 'system:role:remove', 30],
        ]);

        $this->updateMenu('/system/dept', 'system/dept/Index', 'system:dept:list', [
            ['新增', 'system:dept:add', 50],
            ['修改', 'system:dept:edit', 40],
            ['删除', 'system:dept:remove', 30],
        ]);

        $this->updateMenu('/system/menu', 'system/menu/Index', 'system:menu:list', []);
    }

    /**
     * @param  list<array{0: string, 1: string, 2: int}>  $buttons
     */
    private function updateMenu(string $path, string $component, string $code, array $buttons): void
    {
        $menu = AuthMenu::query()->where('menu_path', $path)->first();

        if (! $menu) {
            return;
        }

        $menu->forceFill([
            'component' => $component,
            'permission_code' => $code,
        ])->save();

        foreach ($buttons as [$name, $buttonCode, $sort]) {
            AuthMenu::query()->firstOrCreate(
                ['permission_code' => $buttonCode],
                [
                    'id' => Snowflake::id(),
                    'parent_id' => $menu->id,
                    'menu_name' => $name,
                    'menu_icon' => '',
                    'menu_path' => '',
                    'component' => '',
                    'menu_sort' => $sort,
                    'menu_status' => 1,
                ]
            );
        }
    }

    private function seedDepartments(): HrDepartment
    {
        $existing = HrDepartment::query()->where('dept_code', 'ROOT')->first();

        if ($existing) {
            return $existing;
        }

        $root = $this->createDept('阳光科技', 'ROOT', 0, '0', 1, 90);
        $sz = $this->createDept('深圳总公司', 'SZ', $root->id, '0,'.$root->id, 2, 80);
        $cs = $this->createDept('长沙分公司', 'CS', $root->id, '0,'.$root->id, 2, 70);

        foreach ([['研发部门', 'SZ_RD', 50], ['市场部门', 'SZ_MK', 40], ['测试部门', 'SZ_QA', 30], ['财务部门', 'SZ_FN', 20], ['运维部门', 'SZ_OPS', 10]] as [$name, $code, $sort]) {
            $this->createDept($name, $code, $sz->id, $sz->ancestors.','.$sz->id, 3, $sort);
        }

        foreach ([['市场部门', 'CS_MK', 20], ['财务部门', 'CS_FN', 10]] as [$name, $code, $sort]) {
            $this->createDept($name, $code, $cs->id, $cs->ancestors.','.$cs->id, 3, $sort);
        }

        return $root;
    }

    private function createDept(string $name, string $code, int|string $parentId, string $ancestors, int $level, int $sort): HrDepartment
    {
        return HrDepartment::query()->create([
            'id' => Snowflake::id(),
            'parent_id' => $parentId,
            'dept_name' => $name,
            'dept_code' => $code,
            'ancestors' => $ancestors,
            'dept_level' => $level,
            'leader_user_id' => 0,
            'dept_phone' => '',
            'dept_sort' => $sort,
            'dept_status' => 1,
            'created_by' => 0,
        ]);
    }

    private function syncPermissionsFromMenus(): void
    {
        $this->syncMenuPermissions('0', '0');
    }

    private function syncMenuPermissions(string $parentMenuId, string $parentPermId): void
    {
        $menus = AuthMenu::query()
            ->where('parent_id', $parentMenuId)
            ->orderByDesc('menu_sort')
            ->get();

        foreach ($menus as $menu) {
            $code = $this->permissionCode($menu);

            $permission = AuthPermission::query()->firstOrCreate(
                ['per_code' => $code],
                [
                    'id' => Snowflake::id(),
                    'parent_id' => $parentPermId,
                    'per_name' => $menu->menu_name,
                    'per_type' => $menu->isButton() ? PermissionType::Button : PermissionType::Menu,
                    'per_path' => $menu->menu_path,
                    'per_method' => '',
                    'per_icon' => $menu->menu_icon,
                    'per_sort' => $menu->menu_sort,
                    'per_status' => 1,
                ]
            );

            $this->syncMenuPermissions((string) $menu->id, (string) $permission->id);
        }
    }

    private function permissionCode(AuthMenu $menu): string
    {
        if ($menu->permission_code !== '') {
            return $menu->permission_code;
        }

        if ($menu->menu_path !== '') {
            return trim(str_replace('/', ':', $menu->menu_path), ':');
        }

        return 'menu_'.$menu->id;
    }

    private function seedSuperAdminRole(): AuthRole
    {
        $role = AuthRole::query()->firstOrCreate(
            ['role_code' => AuthRole::SUPER_ADMIN_CODE],
            [
                'id' => Snowflake::id(),
                'role_name' => '超级管理员',
                'role_type' => RoleType::System,
                'role_sort' => 999,
                'data_scope' => DataScope::All,
                'scope_departments' => [],
                'role_status' => 1,
                'role_remark' => '拥有全部菜单和权限',
            ]
        );

        $now = now();
        $role->menus()->sync(
            AuthMenu::query()->pluck('id')->mapWithKeys(fn ($id) => [(string) $id => ['created_at' => $now]])->all()
        );
        $role->permissions()->sync(
            AuthPermission::query()->pluck('id')->mapWithKeys(fn ($id) => [(string) $id => ['created_at' => $now]])->all()
        );

        return $role;
    }

    private function assignAdmin(HrDepartment $root, AuthRole $super): void
    {
        $admin = User::query()->where('user_name', 'admin')->first();

        if (! $admin) {
            return;
        }

        $admin->forceFill([
            'dept_id' => $root->id,
        ])->save();

        $root->forceFill([
            'leader_user_id' => $admin->id,
        ])->save();

        $admin->roles()->sync([
            (string) $super->id => ['created_at' => now()],
        ]);
    }
}
