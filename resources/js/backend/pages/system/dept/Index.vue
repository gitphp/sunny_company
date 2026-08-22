<!--
/**
 * 部门管理页面
 *
 * @package     Resources\Backend\Pages\System\Dept
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <div class="app-container">
        <div class="toolbar">
            <el-button v-if="can('system:dept:add')" type="primary" plain @click="openDialog()">新增</el-button>
        </div>
        <el-table v-loading="loading" :data="rows" row-key="id" border default-expand-all>
            <el-table-column label="部门名称" prop="dept_name" min-width="180" />
            <el-table-column label="部门编码" prop="dept_code" min-width="140" />
            <el-table-column label="排序" prop="dept_sort" width="80" align="center" />
            <el-table-column label="状态" width="90" align="center">
                <template #default="{ row }">
                    <el-tag :type="row.dept_status === 1 ? 'success' : 'info'">{{ row.dept_status === 1 ? '正常' : '停用' }}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="操作" width="180">
                <template #default="{ row }">
                    <el-button v-if="can('system:dept:add')" link type="primary" @click="openDialog(null, row)">新增</el-button>
                    <el-button v-if="can('system:dept:edit')" link type="primary" @click="openDialog(row)">修改</el-button>
                    <el-button v-if="can('system:dept:remove')" link type="danger" @click="handleDelete(row)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <dept-dialog v-model="dialog.visible" :department="dialog.department" :parent="dialog.parent" :tree="rows" @saved="loadDepartments" />
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { deleteDepartment, fetchDepartments } from '../../../api/dept';
import { useUserStore } from '../../../stores/user';
import DeptDialog from './DeptDialog.vue';

const userStore = useUserStore();
const can = (code) => userStore.hasPermission(code);
const loading = ref(false);
const rows = ref([]);
const dialog = reactive({
    visible: false,
    department: null,
    parent: null,
});

async function loadDepartments() {
    loading.value = true;
    try {
        const { data } = await fetchDepartments();
        rows.value = data.departments ?? [];
    } finally {
        loading.value = false;
    }
}

function openDialog(row, parent) {
    dialog.department = row ? { ...row } : null;
    dialog.parent = parent ? { ...parent } : null;
    dialog.visible = true;
}

async function handleDelete(row) {
    await ElMessageBox.confirm(`是否确认删除部门「${row.dept_name}」？`, '警告', { type: 'warning' });
    await deleteDepartment(row.id);
    ElMessage.success('删除成功');
    loadDepartments();
}

onMounted(loadDepartments);
</script>
