<template>
    <div class="app-container">
        <el-form :inline="true" :model="query" class="search-form" @submit.prevent="handleSearch">
            <el-form-item label="规格">
                <el-input v-model="query.spec_name" placeholder="规格名称" clearable />
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="handleSearch">搜索</el-button>
                <el-button v-if="can('product:spec:add')" type="primary" plain @click="openSpec()">新增规格</el-button>
            </el-form-item>
        </el-form>
        <el-table v-loading="loading" :data="rows" border>
            <el-table-column label="编码" prop="spec_code" width="120" />
            <el-table-column label="规格名称" prop="spec_name" min-width="140" />
            <el-table-column label="规格值" min-width="240">
                <template #default="{ row }">
                    <el-tag v-for="item in row.values" :key="item.id" size="small" style="margin:0 4px 4px 0">{{ item.value }}</el-tag>
                    <span v-if="!row.values?.length">-</span>
                </template>
            </el-table-column>
            <el-table-column label="排序" prop="sort_order" width="80" align="center" />
            <el-table-column label="状态" width="90" align="center">
                <template #default="{ row }">
                    <el-switch
                        :model-value="row.spec_status"
                        :active-value="1"
                        :inactive-value="0"
                        :disabled="!can('product:spec:edit')"
                        @change="(value) => handleStatus(row, value)"
                    />
                </template>
            </el-table-column>
            <el-table-column label="操作" width="200">
                <template #default="{ row }">
                    <el-button v-if="can('product:spec:edit')" link type="primary" @click="openValues(row)">规格值</el-button>
                    <el-button v-if="can('product:spec:edit')" link type="primary" @click="openSpec(row)">修改</el-button>
                    <el-button v-if="can('product:spec:remove')" link type="danger" @click="handleDelete(row)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="table-footer">
            <el-pagination
                v-model:current-page="query.page"
                v-model:page-size="query.per_page"
                :total="total"
                layout="total, sizes, prev, pager, next"
                @current-change="loadSpecs"
                @size-change="loadSpecs"
            />
        </div>
        <spec-dialog v-model="specDialog.visible" :spec="specDialog.spec" @saved="loadSpecs" />
        <value-drawer v-model="valueDrawer.visible" :spec="valueDrawer.spec" @saved="loadSpecs" />
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { changeProductSpecStatus, deleteProductSpec, fetchProductSpecs } from '../../../api/product';
import { useUserStore } from '../../../stores/user';
import SpecDialog from './SpecDialog.vue';
import ValueDrawer from './ValueDrawer.vue';

const userStore = useUserStore();
const can = (code) => userStore.hasPermission(code);
const loading = ref(false);
const rows = ref([]);
const total = ref(0);
const query = reactive({ spec_name: '', page: 1, per_page: 10 });
const specDialog = reactive({ visible: false, spec: null });
const valueDrawer = reactive({ visible: false, spec: null });

async function loadSpecs() {
    loading.value = true;
    try {
        const { data } = await fetchProductSpecs(query);
        rows.value = data.data ?? [];
        total.value = data.meta?.total ?? 0;
        if (valueDrawer.spec) {
            const current = rows.value.find((item) => item.id === valueDrawer.spec.id);
            if (current) {
                valueDrawer.spec = { ...current };
            }
        }
    } finally {
        loading.value = false;
    }
}

function handleSearch() {
    query.page = 1;
    loadSpecs();
}

function openSpec(row) {
    specDialog.spec = row ? { ...row } : null;
    specDialog.visible = true;
}

function openValues(row) {
    valueDrawer.spec = { ...row };
    valueDrawer.visible = true;
}

async function handleDelete(row) {
    await ElMessageBox.confirm(`是否确认删除规格「${row.spec_name}」？`, '警告', { type: 'warning' });
    try {
        await deleteProductSpec(row.id);
        ElMessage.success('删除成功');
        loadSpecs();
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '删除失败');
    }
}

async function handleStatus(row, value) {
    try {
        await changeProductSpecStatus(row.id, value);
        ElMessage.success('状态已更新');
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '更新失败');
    } finally {
        loadSpecs();
    }
}

onMounted(loadSpecs);
</script>
