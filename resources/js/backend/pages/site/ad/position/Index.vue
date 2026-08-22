<template>
    <div class="app-container">
        <el-form :inline="true" :model="query" class="search-form" @submit.prevent="handleSearch">
            <el-form-item label="广告位">
                <el-input v-model="query.pos_name" placeholder="名称" clearable />
            </el-form-item>
            <el-form-item label="标识">
                <el-input v-model="query.pos_code" placeholder="如 home_top_banner" clearable />
            </el-form-item>
            <el-form-item label="状态">
                <el-select v-model="query.status" placeholder="状态" clearable style="width:120px">
                    <el-option label="正常" :value="1" />
                    <el-option label="禁用" :value="0" />
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="handleSearch">搜索</el-button>
                <el-button @click="handleReset">重置</el-button>
            </el-form-item>
        </el-form>
        <div class="toolbar">
            <el-button v-if="can('cms:ad-position:add')" type="primary" plain @click="openDialog()">新增</el-button>
        </div>
        <el-table v-loading="loading" :data="rows" border>
            <el-table-column label="广告位名称" prop="pos_name" min-width="160" />
            <el-table-column label="标识" prop="pos_code" min-width="160" />
            <el-table-column label="建议尺寸" width="130" align="center">
                <template #default="{ row }">{{ row.ad_width }} × {{ row.ad_height }}</template>
            </el-table-column>
            <el-table-column label="素材数" prop="material_count" width="90" align="center" />
            <el-table-column label="描述" prop="pos_desc" min-width="180" show-overflow-tooltip />
            <el-table-column label="状态" width="90" align="center">
                <template #default="{ row }">
                    <el-switch
                        :model-value="row.status"
                        :active-value="1"
                        :inactive-value="0"
                        :disabled="!can('cms:ad-position:edit')"
                        @change="(value) => handleStatus(row, value)"
                    />
                </template>
            </el-table-column>
            <el-table-column label="操作" width="140">
                <template #default="{ row }">
                    <el-button v-if="can('cms:ad-position:edit')" link type="primary" @click="openDialog(row)">修改</el-button>
                    <el-button v-if="can('cms:ad-position:remove')" link type="danger" @click="handleDelete(row)">删除</el-button>
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
                @current-change="loadPositions"
                @size-change="loadPositions"
            />
        </div>
        <position-dialog v-model="dialog.visible" :position="dialog.position" @saved="loadPositions" />
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { changeAdPositionStatus, deleteAdPosition, fetchAdPositions } from '../../../../api/ad';
import { useUserStore } from '../../../../stores/user';
import PositionDialog from './PositionDialog.vue';

const userStore = useUserStore();
const can = (code) => userStore.hasPermission(code);
const loading = ref(false);
const rows = ref([]);
const total = ref(0);
const query = reactive({
    pos_name: '',
    pos_code: '',
    status: undefined,
    page: 1,
    per_page: 10,
});
const dialog = reactive({
    visible: false,
    position: null,
});

async function loadPositions() {
    loading.value = true;
    try {
        const { data } = await fetchAdPositions(query);
        rows.value = data.data ?? [];
        total.value = data.meta?.total ?? 0;
    } finally {
        loading.value = false;
    }
}

function handleSearch() {
    query.page = 1;
    loadPositions();
}

function handleReset() {
    query.pos_name = '';
    query.pos_code = '';
    query.status = undefined;
    query.page = 1;
    loadPositions();
}

function openDialog(row) {
    dialog.position = row ? { ...row } : null;
    dialog.visible = true;
}

async function handleDelete(row) {
    await ElMessageBox.confirm(`是否确认删除广告位「${row.pos_name}」？`, '警告', { type: 'warning' });
    try {
        await deleteAdPosition(row.id);
        ElMessage.success('删除成功');
        loadPositions();
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '删除失败');
    }
}

async function handleStatus(row, value) {
    try {
        await changeAdPositionStatus(row.id, value);
        ElMessage.success('状态已更新');
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '更新失败');
    } finally {
        loadPositions();
    }
}

onMounted(loadPositions);
</script>
