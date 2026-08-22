<!--
/**
 * 角色管理页面
 *
 * @package     Resources\Backend\Pages\System\Role
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <div class="app-container">
        <el-form :inline="true" :model="query" class="search-form" @submit.prevent="handleSearch">
            <el-form-item label="角色名称">
                <el-input v-model="query.role_name" placeholder="请输入角色名称" clearable />
            </el-form-item>
            <el-form-item label="权限字符">
                <el-input v-model="query.role_code" placeholder="请输入权限字符" clearable />
            </el-form-item>
            <el-form-item label="状态">
                <el-select v-model="query.role_status" placeholder="角色状态" clearable style="width:140px">
                    <el-option label="禁用" :value="0" />
                    <el-option label="启用" :value="1" />
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="handleSearch">搜索</el-button>
                <el-button @click="handleReset">重置</el-button>
            </el-form-item>
        </el-form>

        <div class="toolbar">
            <el-button v-if="can('system:role:add')" type="primary" plain @click="openDialog()">新增</el-button>
        </div>

        <el-table v-loading="loading" :data="rows" border>
            <el-table-column label="角色编号" prop="id" min-width="160" show-overflow-tooltip />
            <el-table-column label="角色名称" prop="role_name" min-width="120" />
            <el-table-column label="权限字符" prop="role_code" min-width="120" />
            <el-table-column label="显示顺序" prop="role_sort" width="90" align="center" />
            <el-table-column label="数据范围" prop="data_scope_label" min-width="120" />
            <el-table-column label="状态" width="90" align="center">
                <template #default="{ row }">
                    <el-switch
                        :model-value="row.role_status"
                        :active-value="1"
                        :inactive-value="0"
                        :disabled="row.role_code === 'super_admin'"
                        @change="(value) => handleStatus(row, value)"
                    />
                </template>
            </el-table-column>
            <el-table-column label="创建时间" prop="created_at" min-width="170" />
            <el-table-column label="操作" width="160" fixed="right">
                <template #default="{ row }">
                    <el-button v-if="can('system:role:edit')" link type="primary" @click="openDialog(row)">修改</el-button>
                    <el-button v-if="can('system:role:remove')" link type="danger" :disabled="row.role_type === 1" @click="handleDelete(row)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>

        <div class="table-footer">
            <el-pagination
                v-model:current-page="query.page"
                v-model:page-size="query.per_page"
                :total="total"
                :page-sizes="[10, 20, 50]"
                layout="total, sizes, prev, pager, next, jumper"
                @current-change="loadRoles"
                @size-change="loadRoles"
            />
        </div>

        <role-dialog v-model="dialog.visible" :role-id="dialog.id" @saved="loadRoles" />
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { changeRoleStatus, deleteRole, fetchRoles } from '../../../api/role';
import { useUserStore } from '../../../stores/user';
import RoleDialog from './RoleDialog.vue';

const userStore = useUserStore();
const can = (code) => userStore.hasPermission(code);

const loading = ref(false);
const rows = ref([]);
const total = ref(0);
const query = reactive({
    role_name: '',
    role_code: '',
    role_status: undefined,
    page: 1,
    per_page: 10,
});
const dialog = reactive({
    visible: false,
    id: '',
});

async function loadRoles() {
    loading.value = true;
    try {
        const { data } = await fetchRoles(query);
        rows.value = data.data ?? [];
        total.value = data.meta?.total ?? 0;
    } finally {
        loading.value = false;
    }
}

function handleSearch() {
    query.page = 1;
    loadRoles();
}

function handleReset() {
    query.role_name = '';
    query.role_code = '';
    query.role_status = undefined;
    query.page = 1;
    loadRoles();
}

function openDialog(row) {
    dialog.id = row?.id || '';
    dialog.visible = true;
}

async function handleDelete(row) {
    await ElMessageBox.confirm(`是否确认删除角色「${row.role_name}」？`, '警告', { type: 'warning' });
    await deleteRole(row.id);
    ElMessage.success('删除成功');
    loadRoles();
}

async function handleStatus(row, value) {
    try {
        await changeRoleStatus(row.id, value);
        ElMessage.success('状态已更新');
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '状态更新失败');
    } finally {
        loadRoles();
    }
}

onMounted(loadRoles);
</script>
