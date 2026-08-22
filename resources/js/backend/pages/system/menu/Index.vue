<!--
/**
 * 菜单管理页面
 *
 * @package     Resources\Backend\Pages\System\Menu
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <div class="app-container">
        <el-table v-loading="loading" :data="menus" row-key="id" border default-expand-all>
            <el-table-column label="菜单名称" prop="menu_name" min-width="180" />
            <el-table-column label="图标" width="90" align="center">
                <template #default="{ row }">
                    <el-icon v-if="row.menu_icon"><component :is="row.menu_icon" /></el-icon>
                    <span v-else>-</span>
                </template>
            </el-table-column>
            <el-table-column label="路由路径" prop="menu_path" min-width="160" />
            <el-table-column label="组件路径" prop="component" min-width="180" />
            <el-table-column label="权限标识" prop="permission_code" min-width="180" />
            <el-table-column label="排序" prop="menu_sort" width="80" align="center" />
            <el-table-column label="状态" width="90" align="center">
                <template #default="{ row }">
                    <el-tag :type="row.menu_status === 1 ? 'success' : 'info'">
                        {{ row.menu_status === 1 ? '启用' : '禁用' }}
                    </el-tag>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { fetchAllMenus } from '../../../api/menu';

const loading = ref(false);
const menus = ref([]);

onMounted(async () => {
    loading.value = true;
    try {
        const { data } = await fetchAllMenus();
        menus.value = data.menus ?? [];
    } finally {
        loading.value = false;
    }
});
</script>
