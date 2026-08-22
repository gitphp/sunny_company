<!--
/**
 * 文章分类页面
 *
 * @package     Resources\Backend\Pages\Site\Category
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <div class="app-container">
        <div class="toolbar">
            <el-select v-model="catType" placeholder="分类类型" clearable style="width:140px;margin-right:12px" @change="loadCategories">
                <el-option label="文章分类" :value="0" />
                <el-option label="导航分类" :value="1" />
            </el-select>
            <el-button v-if="can('cms:category:add')" type="primary" plain @click="openDialog()">新增</el-button>
        </div>
        <el-table v-loading="loading" :data="rows" row-key="id" border default-expand-all>
            <el-table-column label="分类名称" prop="cat_name" min-width="180" />
            <el-table-column label="类型" width="110" align="center">
                <template #default="{ row }">
                    <el-tag :type="row.cat_type === 1 ? 'warning' : ''">{{ row.cat_type_label }}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="URL别名" prop="cat_url" min-width="140" />
            <el-table-column label="排序" prop="cat_sort" width="80" align="center" />
            <el-table-column label="状态" width="90" align="center">
                <template #default="{ row }">
                    <el-tag :type="row.status === 1 ? 'success' : 'info'">{{ row.status === 1 ? '正常' : '停用' }}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="操作" width="180">
                <template #default="{ row }">
                    <el-button v-if="can('cms:category:add')" link type="primary" @click="openDialog(null, row)">新增</el-button>
                    <el-button v-if="can('cms:category:edit')" link type="primary" @click="openDialog(row)">修改</el-button>
                    <el-button v-if="can('cms:category:remove')" link type="danger" @click="handleDelete(row)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <category-dialog v-model="dialog.visible" :category="dialog.category" :parent="dialog.parent" :tree="rows" @saved="loadCategories" />
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { deleteCategory, fetchCategories } from '../../../api/category';
import { useUserStore } from '../../../stores/user';
import CategoryDialog from './CategoryDialog.vue';

const userStore = useUserStore();
const can = (code) => userStore.hasPermission(code);
const loading = ref(false);
const rows = ref([]);
const catType = ref();
const dialog = reactive({
    visible: false,
    category: null,
    parent: null,
});

async function loadCategories() {
    loading.value = true;
    try {
        const { data } = await fetchCategories({ cat_type: catType.value });
        rows.value = data.categories ?? [];
    } finally {
        loading.value = false;
    }
}

function openDialog(row, parent) {
    dialog.category = row ? { ...row } : null;
    dialog.parent = parent ? { ...parent } : null;
    dialog.visible = true;
}

async function handleDelete(row) {
    await ElMessageBox.confirm(`是否确认删除分类「${row.cat_name}」？`, '警告', { type: 'warning' });
    try {
        await deleteCategory(row.id);
        ElMessage.success('删除成功');
        loadCategories();
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '删除失败');
    }
}

onMounted(loadCategories);
</script>
