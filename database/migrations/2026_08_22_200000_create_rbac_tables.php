<?php

/**
 * RBAC相关表迁移
 *
 * @package     Database\Migrations
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('主键ID');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父级权限ID，用于树形结构');
            $table->string('per_name', 64)->default('')->comment('权限名称，如：用户删除');
            $table->string('per_code', 128)->default('')->comment('权限唯一标识，如：user:delete');
            $table->enum('per_type', ['menu', 'button', 'api'])->default('api')->comment('权限类型：menu=菜单，button=按钮，api=接口');
            $table->string('per_path', 255)->default('')->comment('前端路由路径或API路径');
            $table->string('per_method', 16)->default('')->comment('HTTP方法，仅 type=api 时有效');
            $table->string('per_icon', 64)->default('')->comment('菜单图标，仅 type=menu 时有效');
            $table->unsignedInteger('per_sort')->default(0)->comment('排序权重，值越大越靠前');
            $table->unsignedTinyInteger('per_status')->default(1)->comment('状态：0=禁用，1=启用');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deleted_at')->nullable();

            $table->unique('per_code', 'uk_code');
            $table->index('parent_id', 'idx_parent_id');
            $table->index('per_type', 'idx_type');
            $table->index('per_status', 'idx_status');
            $table->index('deleted_at', 'idx_deleted_at');
        });

        Schema::create('auth_role', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('角色ID');
            $table->string('role_name', 64)->default('')->comment('角色名称');
            $table->string('role_code', 64)->default('')->comment('角色唯一标识');
            $table->tinyInteger('role_type')->default(2)->comment('角色类型: 1=系统内置 2=用户自定义');
            $table->unsignedInteger('role_sort')->default(0)->comment('排序号');
            $table->unsignedTinyInteger('data_scope')->default(1)->comment('数据权限范围');
            $table->json('scope_departments')->nullable()->comment('指定部门IDs，JSON格式');
            $table->unsignedTinyInteger('role_status')->default(1)->comment('0禁用 1启用');
            $table->string('role_remark', 512)->default('')->comment('角色备注');
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deleted_at')->nullable();

            $table->unique(['role_code', 'deleted_at'], 'uk_role_code');
            $table->index('role_status', 'idx_status');
        });

        Schema::create('auth_role_menus', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->default(0)->comment('角色ID');
            $table->unsignedBigInteger('menu_id')->default(0)->comment('菜单ID');
            $table->dateTime('created_at')->useCurrent();
            $table->primary(['role_id', 'menu_id']);
        });

        Schema::create('auth_role_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->default(0)->comment('角色ID');
            $table->unsignedBigInteger('permission_id')->default(0)->comment('权限ID');
            $table->dateTime('created_at')->useCurrent();
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('auth_user_role', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->default(0)->comment('用户ID');
            $table->unsignedBigInteger('role_id')->default(0)->comment('角色ID');
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('hr_department', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('部门主键ID');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父部门ID，0=根节点');
            $table->string('dept_name', 64)->default('')->comment('部门名称');
            $table->string('dept_code', 64)->default('')->comment('部门唯一编码');
            $table->string('ancestors', 512)->default('')->comment('祖先ID路径，逗号分隔');
            $table->unsignedTinyInteger('dept_level')->default(1)->comment('层级深度');
            $table->unsignedBigInteger('leader_user_id')->default(0)->comment('部门负责人ID');
            $table->string('dept_phone', 16)->default('')->comment('部门联系电话');
            $table->integer('dept_sort')->default(0)->comment('树形展示排序号');
            $table->tinyInteger('dept_status')->default(1)->comment('状态 0禁用 1正常启用');
            $table->unsignedBigInteger('created_by')->default(0)->comment('创建人用户ID');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deleted_at')->nullable();

            $table->unique('dept_code', 'uk_dept_code');
            $table->index('parent_id', 'idx_parent_id');
        });

        Schema::table('user_account', function (Blueprint $table) {
            $table->unsignedBigInteger('dept_id')->default(0)->after('real_auth_status')->comment('所属部门ID');
            $table->index('dept_id', 'idx_dept_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_account', function (Blueprint $table) {
            $table->dropIndex('idx_dept_id');
            $table->dropColumn('dept_id');
        });

        Schema::dropIfExists('hr_department');
        Schema::dropIfExists('auth_user_role');
        Schema::dropIfExists('auth_role_permissions');
        Schema::dropIfExists('auth_role_menus');
        Schema::dropIfExists('auth_role');
        Schema::dropIfExists('auth_permissions');
    }
};
