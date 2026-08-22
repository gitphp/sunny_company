<template>
    <div class="app-container">
        <el-form :inline="true" :model="query" class="search-form" @submit.prevent="handleSearch">
            <el-form-item label="品牌">
                <el-input v-model="query.brand_name" placeholder="品牌名称" clearable />
            </el-form-item>
            <el-form-item label="状态">
                <el-select v-model="query.is_show" placeholder="状态" clearable style="width:120px">
                    <el-option label="显示" :value="1" />
                    <el-option label="隐藏" :value="0" />
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="handleSearch">搜索</el-button>
                <el-button @click="handleReset">重置</el-button>
            </el-form-item>
        </el-form>
        <div class="toolbar">
            <el-button v-if="can('product:brand:add')" type="primary" plain @click="openDialog()">新增</el-button>
        </div>
        <el-table v-loading="loading" :data="rows" border>
            <el-table-column label="编码" prop="brand_code" width="120" />
            <el-table-column label="品牌名称" prop="brand_name" min-width="140" />
            <el-table-column label="英文别名" prop="alias" min-width="140" />
            <el-table-column label="类型" width="100" align="center">
                <template #default="{ row }">
                    <el-tag :type="row.is_system === 1 ? 'warning' : 'info'" size="small">{{ row.is_system === 1 ? '系统' : '自定义' }}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="排序" prop="sort_order" width="80" align="center" />
            <el-table-column label="状态" width="90" align="center">
                <template #default="{ row }">
                    <el-switch
                        :model-value="row.is_show"
                        :active-value="1"
                        :inactive-value="0"
                        :disabled="!can('product:brand:edit')"
                        @change="(value) => handleStatus(row, value)"
                    />
                </template>
            </el-table-column>
            <el-table-column label="操作" width="140">
                <template #default="{ row }">
                    <el-button v-if="can('product:brand:edit')" link type="primary" @click="openDialog(row)">修改</el-button>
                    <el-button v-if="can('product:brand:remove') && row.is_system !== 1" link type="danger" @click="handleDelete(row)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="table-footer">
            <el-pagination
                v-model:current-page="query.page"
                v-model:page-size="query.per_page"
                :total="total"
                :page-sizes="[10, 20, 50]"
                layout="total, sizes, prev, pager, next"
                @current-change="loadBrands"
                @size-change="loadBrands"
            />
        </div>
        <brand-dialog v-model="dialog.visible" :brand="dialog.brand" @saved="loadBrands" />
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { changeProductBrandStatus, deleteProductBrand, fetchProductBrands } from '../../../api/product';
import { useUserStore } from '../../../stores/user';
import BrandDialog from './BrandDialog.vue';

const userStore = useUserStore();
const can = (code) => userStore.hasPermission(code);
const loading = ref(false);
const rows = ref([]);
const total = ref(0);
const query = reactive({
    brand_name: '',
    is_show: undefined,
    page: 1,
    per_page: 10,
});
const dialog = reactive({ visible: false, brand: null });

async function loadBrands() {
    loading.value = true;
    try {
        const { data } = await fetchProductBrands(query);
        rows.value = data.data ?? [];
        total.value = data.meta?.total ?? 0;
    } finally {
        loading.value = false;
    }
}

function handleSearch() {
    query.page = 1;
    loadBrands();
}

function handleReset() {
    query.brand_name = '';
    query.is_show = undefined;
    query.page = 1;
    loadBrands();
}

function openDialog(row) {
    dialog.brand = row ? { ...row } : null;
    dialog.visible = true;
}

async function handleDelete(row) {
    await ElMessageBox.confirm(`是否确认删除品牌「${row.brand_name}」？`, '警告', { type: 'warning' });
    try {
        await deleteProductBrand(row.id);
        ElMessage.success('删除成功');
        loadBrands();
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '删除失败');
    }
}

async function handleStatus(row, value) {
    try {
        await changeProductBrandStatus(row.id, value);
        ElMessage.success('状态已更新');
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '更新失败');
    } finally {
        loadBrands();
    }
}

onMounted(loadBrands);
</script>
