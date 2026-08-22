<!--
/**
 * 商品管理页面
 *
 * @package     Resources\Backend\Pages\Product
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <div class="app-container">
        <div class="split-layout">
            <aside class="split-side">
                <el-tree
                    :data="categoryTree"
                    node-key="id"
                    default-expand-all
                    highlight-current
                    :props="{ label: 'category_name' }"
                    @node-click="onCategoryClick"
                />
            </aside>
            <div class="split-main">
                <el-form :inline="true" :model="query" class="search-form" @submit.prevent="handleSearch">
                    <el-form-item label="商品">
                        <el-input v-model="query.product_name" placeholder="名称 / 编码" clearable />
                    </el-form-item>
                    <el-form-item label="品牌">
                        <el-select v-model="query.brand_id" placeholder="全部品牌" clearable style="width:160px">
                            <el-option v-for="item in brands" :key="item.id" :label="item.brand_name" :value="item.id" />
                        </el-select>
                    </el-form-item>
                    <el-form-item label="状态">
                        <el-select v-model="query.product_status" placeholder="状态" clearable style="width:110px">
                            <el-option label="上架" :value="1" />
                            <el-option label="下架" :value="0" />
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="handleSearch">搜索</el-button>
                        <el-button @click="handleReset">重置</el-button>
                    </el-form-item>
                </el-form>
                <div class="toolbar">
                    <el-button v-if="can('product:add')" type="primary" plain @click="openDialog()">新增</el-button>
                    <el-button v-if="can('product:remove')" type="danger" plain :disabled="selected.length === 0" @click="handleBatchDelete">删除</el-button>
                </div>
                <el-table v-loading="loading" :data="rows" border @selection-change="(value) => (selected = value)">
                    <el-table-column type="selection" width="50" align="center" />
                    <el-table-column label="主图" width="76" align="center">
                        <template #default="{ row }">
                            <el-image
                                v-if="row.main_image_url"
                                :src="row.main_image_url"
                                :preview-src-list="[row.main_image_url]"
                                preview-teleported
                                fit="cover"
                                style="width:48px;height:48px;border-radius:4px"
                            />
                            <span v-else>-</span>
                        </template>
                    </el-table-column>
                    <el-table-column label="编码" prop="auto_code" width="120" />
                    <el-table-column label="商品名称" prop="product_name" min-width="160" show-overflow-tooltip />
                    <el-table-column label="分类" prop="category_name" min-width="110" />
                    <el-table-column label="品牌" prop="brand_name" width="110" />
                    <el-table-column label="SKU" prop="sku_count" width="70" align="center" />
                    <el-table-column label="库存" prop="stock_num" width="80" align="center" />
                    <el-table-column label="最低价" prop="min_price" width="90" align="center" />
                    <el-table-column label="状态" width="90" align="center">
                        <template #default="{ row }">
                            <el-switch
                                :model-value="row.product_status"
                                :active-value="1"
                                :inactive-value="0"
                                :disabled="!can('product:edit')"
                                @change="(value) => handleStatus(row, value)"
                            />
                        </template>
                    </el-table-column>
                    <el-table-column label="操作" width="140" fixed="right">
                        <template #default="{ row }">
                            <el-button v-if="can('product:edit')" link type="primary" @click="openDialog(row)">修改</el-button>
                            <el-button v-if="can('product:remove')" link type="danger" @click="handleDelete(row)">删除</el-button>
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
                        @current-change="loadProducts"
                        @size-change="loadProducts"
                    />
                </div>
            </div>
        </div>
        <product-dialog v-model="dialog.visible" :product-id="dialog.id" :category-id="query.category_id" @saved="loadProducts" />
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { batchDeleteProducts, changeProductStatus, deleteProduct, fetchProducts } from '../../api/product';
import { fetchOptionProductBrands, fetchOptionProductCategories } from '../../api/options';
import { useUserStore } from '../../stores/user';
import ProductDialog from './ProductDialog.vue';

const userStore = useUserStore();
const can = (code) => userStore.hasPermission(code);
const loading = ref(false);
const rows = ref([]);
const total = ref(0);
const selected = ref([]);
const brands = ref([]);
const categoryTree = ref([]);
const query = reactive({
    product_name: '',
    brand_id: '',
    category_id: '',
    product_status: undefined,
    page: 1,
    per_page: 10,
});
const dialog = reactive({ visible: false, id: '' });

async function loadProducts() {
    loading.value = true;
    try {
        const { data } = await fetchProducts(query);
        rows.value = data.data ?? [];
        total.value = data.meta?.total ?? 0;
    } finally {
        loading.value = false;
    }
}

function handleSearch() {
    query.page = 1;
    loadProducts();
}

function handleReset() {
    query.product_name = '';
    query.brand_id = '';
    query.category_id = '';
    query.product_status = undefined;
    query.page = 1;
    loadProducts();
}

function onCategoryClick(node) {
    query.category_id = node.id;
    query.page = 1;
    loadProducts();
}

function openDialog(row) {
    dialog.id = row?.id || '';
    dialog.visible = true;
}

async function handleDelete(row) {
    await ElMessageBox.confirm(`是否确认删除商品「${row.product_name}」？`, '警告', { type: 'warning' });
    await deleteProduct(row.id);
    ElMessage.success('删除成功');
    loadProducts();
}

async function handleBatchDelete() {
    await ElMessageBox.confirm(`是否确认删除选中的 ${selected.value.length} 条数据？`, '警告', { type: 'warning' });
    await batchDeleteProducts(selected.value.map((item) => item.id));
    ElMessage.success('删除成功');
    loadProducts();
}

async function handleStatus(row, value) {
    try {
        await changeProductStatus(row.id, value);
        ElMessage.success('状态已更新');
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '更新失败');
    } finally {
        loadProducts();
    }
}

onMounted(async () => {
    const [brandRes, catRes] = await Promise.all([fetchOptionProductBrands(), fetchOptionProductCategories()]);
    brands.value = brandRes.data.brands ?? [];
    categoryTree.value = [{ id: '', category_name: '全部分类', children: catRes.data.categories ?? [] }];
    loadProducts();
});
</script>
