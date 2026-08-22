<template>
    <div class="app-container">
        <div class="toolbar">
            <el-button v-if="can('product:category:add')" type="primary" plain @click="openDialog()">新增</el-button>
        </div>
        <el-table v-loading="loading" :data="rows" row-key="id" border default-expand-all>
            <el-table-column label="分类名称" prop="category_name" min-width="180" />
            <el-table-column label="编码" prop="category_code" width="120" />
            <el-table-column label="级别" prop="level" width="70" align="center" />
            <el-table-column label="单位" prop="unit" width="90" />
            <el-table-column label="商品数" prop="product_count" width="90" align="center" />
            <el-table-column label="排序" prop="sort_order" width="80" align="center" />
            <el-table-column label="状态" width="90" align="center">
                <template #default="{ row }">
                    <el-tag :type="row.cat_status === 1 ? 'success' : 'info'">{{ row.cat_status === 1 ? '显示' : '隐藏' }}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="操作" width="180">
                <template #default="{ row }">
                    <el-button v-if="can('product:category:add') && row.level < 3" link type="primary" @click="openDialog(null, row)">新增</el-button>
                    <el-button v-if="can('product:category:edit')" link type="primary" @click="openDialog(row)">修改</el-button>
                    <el-button v-if="can('product:category:remove')" link type="danger" @click="handleDelete(row)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <category-dialog v-model="dialog.visible" :category="dialog.category" :parent="dialog.parent" :tree="rows" @saved="loadCategories" />
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { deleteProductCategory, fetchProductCategories } from '../../../api/product';
import { useUserStore } from '../../../stores/user';
import CategoryDialog from './CategoryDialog.vue';

const userStore = useUserStore();
const can = (code) => userStore.hasPermission(code);
const loading = ref(false);
const rows = ref([]);
const dialog = reactive({ visible: false, category: null, parent: null });

async function loadCategories() {
    loading.value = true;
    try {
        const { data } = await fetchProductCategories();
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
    await ElMessageBox.confirm(`是否确认删除分类「${row.category_name}」？`, '警告', { type: 'warning' });
    try {
        await deleteProductCategory(row.id);
        ElMessage.success('删除成功');
        loadCategories();
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '删除失败');
    }
}

onMounted(loadCategories);
</script>
