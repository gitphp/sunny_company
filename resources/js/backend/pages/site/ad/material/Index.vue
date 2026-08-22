<!--
/**
 * 广告素材页面
 *
 * @package     Resources\Backend\Pages\Site\Ad\Material
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <div class="app-container">
        <el-form :inline="true" :model="query" class="search-form" @submit.prevent="handleSearch">
            <el-form-item label="广告位">
                <el-select v-model="query.position_id" placeholder="全部广告位" clearable style="width:200px">
                    <el-option v-for="item in positions" :key="item.id" :label="item.pos_name" :value="item.id" />
                </el-select>
            </el-form-item>
            <el-form-item label="标题">
                <el-input v-model="query.title" placeholder="广告标题" clearable />
            </el-form-item>
            <el-form-item label="状态">
                <el-select v-model="query.status" placeholder="状态" clearable style="width:120px">
                    <el-option label="上线" :value="1" />
                    <el-option label="下线" :value="0" />
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="handleSearch">搜索</el-button>
                <el-button @click="handleReset">重置</el-button>
            </el-form-item>
        </el-form>
        <div class="toolbar">
            <el-button v-if="can('cms:ad-material:add')" type="primary" plain @click="openDialog()">新增</el-button>
        </div>
        <el-table v-loading="loading" :data="rows" border>
            <el-table-column label="标题" prop="title" min-width="160" />
            <el-table-column label="广告位" prop="position_name" min-width="140" />
            <el-table-column label="图片" min-width="160" show-overflow-tooltip>
                <template #default="{ row }">{{ row.image_url || '-' }}</template>
            </el-table-column>
            <el-table-column label="跳转" prop="link_url" min-width="160" show-overflow-tooltip />
            <el-table-column label="排序" prop="sort_order" width="80" align="center" />
            <el-table-column label="有效期" min-width="200">
                <template #default="{ row }">
                    {{ row.start_time || '立即' }} ~ {{ row.end_time || '永久' }}
                </template>
            </el-table-column>
            <el-table-column label="状态" width="90" align="center">
                <template #default="{ row }">
                    <el-switch
                        :model-value="row.status"
                        :active-value="1"
                        :inactive-value="0"
                        :disabled="!can('cms:ad-material:edit')"
                        @change="(value) => handleStatus(row, value)"
                    />
                </template>
            </el-table-column>
            <el-table-column label="操作" width="140">
                <template #default="{ row }">
                    <el-button v-if="can('cms:ad-material:edit')" link type="primary" @click="openDialog(row)">修改</el-button>
                    <el-button v-if="can('cms:ad-material:remove')" link type="danger" @click="handleDelete(row)">删除</el-button>
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
                @current-change="loadMaterials"
                @size-change="loadMaterials"
            />
        </div>
        <material-dialog v-model="dialog.visible" :material="dialog.material" :positions="positions" @saved="loadMaterials" />
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { changeAdMaterialStatus, deleteAdMaterial, fetchAdMaterials } from '../../../../api/ad';
import { fetchOptionAdPositions } from '../../../../api/options';
import { useUserStore } from '../../../../stores/user';
import MaterialDialog from './MaterialDialog.vue';

const userStore = useUserStore();
const can = (code) => userStore.hasPermission(code);
const loading = ref(false);
const rows = ref([]);
const total = ref(0);
const positions = ref([]);
const query = reactive({
    title: '',
    position_id: '',
    status: undefined,
    page: 1,
    per_page: 10,
});
const dialog = reactive({
    visible: false,
    material: null,
});

async function loadMaterials() {
    loading.value = true;
    try {
        const { data } = await fetchAdMaterials(query);
        rows.value = data.data ?? [];
        total.value = data.meta?.total ?? 0;
    } finally {
        loading.value = false;
    }
}

async function loadPositions() {
    const { data } = await fetchOptionAdPositions();
    positions.value = data.positions ?? [];
}

function handleSearch() {
    query.page = 1;
    loadMaterials();
}

function handleReset() {
    query.title = '';
    query.position_id = '';
    query.status = undefined;
    query.page = 1;
    loadMaterials();
}

function openDialog(row) {
    dialog.material = row ? { ...row } : null;
    dialog.visible = true;
}

async function handleDelete(row) {
    await ElMessageBox.confirm(`是否确认删除素材「${row.title}」？`, '警告', { type: 'warning' });
    await deleteAdMaterial(row.id);
    ElMessage.success('删除成功');
    loadMaterials();
}

async function handleStatus(row, value) {
    try {
        await changeAdMaterialStatus(row.id, value);
        ElMessage.success('状态已更新');
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '更新失败');
    } finally {
        loadMaterials();
    }
}

onMounted(async () => {
    await loadPositions();
    loadMaterials();
});
</script>
