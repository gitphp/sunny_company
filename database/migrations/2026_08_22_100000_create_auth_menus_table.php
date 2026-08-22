<?php

/**
 * 菜单表迁移
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
        if (Schema::hasTable('auth_menus')) {
            return;
        }

        Schema::create('auth_menus', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('主键ID');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父级菜单ID, 0表示顶级菜单');
            $table->string('menu_name', 64)->default('')->comment('菜单名称，如：用户管理');
            $table->string('menu_icon', 64)->default('')->comment('菜单图标，如：el-icon-user');
            $table->string('menu_path', 255)->default('')->comment('前端路由路径，如：/user/list');
            $table->string('component', 255)->default('')->comment('前端组件路径，如：user/Index');
            $table->string('permission_code', 128)->default('')->comment('关联的权限标识，用于按钮级控制');
            $table->unsignedInteger('menu_sort')->default(0)->comment('排序权重，值越大越靠前');
            $table->unsignedTinyInteger('menu_status')->default(1)->comment('状态：0=禁用, 1=启用');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

            $table->index('parent_id', 'idx_parent_id');
            $table->index('permission_code', 'idx_permission_code');
            $table->index('menu_status', 'idx_status');
            $table->index('deleted_at', 'idx_deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_menus');
    }
};
