<template>
    <div class="app-container">
        <el-form :inline="true" :model="query" class="search-form" @submit.prevent="handleSearch">
            <el-form-item label="职位">
                <el-input v-model="query.job_title" placeholder="职位名称" clearable />
            </el-form-item>
            <el-form-item label="部门">
                <el-input v-model="query.department" placeholder="所属部门" clearable />
            </el-form-item>
            <el-form-item label="状态">
                <el-select v-model="query.job_status" placeholder="职位状态" clearable style="width:130px">
                    <el-option v-for="item in statusOptions" :key="item.value" :label="item.label" :value="item.value" />
                </el-select>
            </el-form-item>
            <el-form-item label="急聘">
                <el-select v-model="query.is_hot" placeholder="全部" clearable style="width:110px">
                    <el-option label="急聘" :value="1" />
                    <el-option label="普通" :value="0" />
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="handleSearch">搜索</el-button>
                <el-button @click="handleReset">重置</el-button>
            </el-form-item>
        </el-form>
        <div class="toolbar">
            <el-button v-if="can('cms:job:add')" type="primary" plain @click="openDialog()">新增</el-button>
            <el-button v-if="can('cms:job:remove')" type="danger" plain :disabled="multiple" @click="handleBatchDelete">删除</el-button>
        </div>
        <el-table v-loading="loading" :data="rows" border @selection-change="onSelectionChange">
            <el-table-column type="selection" width="50" align="center" />
            <el-table-column label="职位名称" prop="job_title" min-width="160" show-overflow-tooltip />
            <el-table-column label="部门" prop="department" min-width="110" />
            <el-table-column label="地点" prop="workplace" min-width="120" show-overflow-tooltip />
            <el-table-column label="薪资" prop="salary_range" width="120" />
            <el-table-column label="经验 / 学历" min-width="140">
                <template #default="{ row }">{{ row.experience || '-' }} / {{ row.education || '-' }}</template>
            </el-table-column>
            <el-table-column label="急聘" width="80" align="center">
                <template #default="{ row }">
                    <el-switch
                        :model-value="row.is_hot"
                        :active-value="1"
                        :inactive-value="0"
                        :disabled="!can('cms:job:edit')"
                        @change="(value) => handleHot(row, value)"
                    />
                </template>
            </el-table-column>
            <el-table-column label="状态" width="130" align="center">
                <template #default="{ row }">
                    <el-select
                        :model-value="row.job_status"
                        size="small"
                        :disabled="!can('cms:job:edit')"
                        @change="(value) => handleStatus(row, value)"
                    >
                        <el-option v-for="item in statusOptions" :key="item.value" :label="item.label" :value="item.value" />
                    </el-select>
                </template>
            </el-table-column>
            <el-table-column label="浏览" prop="view_count" width="80" align="center" />
            <el-table-column label="过期时间" prop="expire_at" min-width="170" />
            <el-table-column label="操作" width="140" fixed="right">
                <template #default="{ row }">
                    <el-button v-if="can('cms:job:edit')" link type="primary" @click="openDialog(row)">修改</el-button>
                    <el-button v-if="can('cms:job:remove')" link type="danger" @click="handleDelete(row)">删除</el-button>
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
                @current-change="loadJobs"
                @size-change="loadJobs"
            />
        </div>
        <job-dialog v-model="dialog.visible" :job-id="dialog.id" @saved="loadJobs" />
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { batchDeleteJobs, changeJobStatus, deleteJob, fetchJobs } from '../../../api/job';
import { useUserStore } from '../../../stores/user';
import JobDialog from './JobDialog.vue';

const userStore = useUserStore();
const can = (code) => userStore.hasPermission(code);
const statusOptions = [
    { label: '待发布', value: 1 },
    { label: '发布中', value: 2 },
    { label: '已关闭', value: 3 },
];
const loading = ref(false);
const rows = ref([]);
const total = ref(0);
const selected = ref([]);
const multiple = computed(() => selected.value.length === 0);
const query = reactive({
    job_title: '',
    department: '',
    job_status: undefined,
    is_hot: undefined,
    page: 1,
    per_page: 10,
});
const dialog = reactive({
    visible: false,
    id: '',
});

async function loadJobs() {
    loading.value = true;
    try {
        const { data } = await fetchJobs(query);
        rows.value = data.data ?? [];
        total.value = data.meta?.total ?? 0;
    } finally {
        loading.value = false;
    }
}

function handleSearch() {
    query.page = 1;
    loadJobs();
}

function handleReset() {
    query.job_title = '';
    query.department = '';
    query.job_status = undefined;
    query.is_hot = undefined;
    query.page = 1;
    loadJobs();
}

function onSelectionChange(value) {
    selected.value = value;
}

function openDialog(row) {
    dialog.id = row?.id || '';
    dialog.visible = true;
}

async function handleDelete(row) {
    await ElMessageBox.confirm(`是否确认删除职位「${row.job_title}」？`, '警告', { type: 'warning' });
    await deleteJob(row.id);
    ElMessage.success('删除成功');
    loadJobs();
}

async function handleBatchDelete() {
    await ElMessageBox.confirm(`是否确认删除选中的 ${selected.value.length} 条数据？`, '警告', { type: 'warning' });
    await batchDeleteJobs(selected.value.map((item) => item.id));
    ElMessage.success('删除成功');
    loadJobs();
}

async function handleHot(row, value) {
    try {
        await changeJobStatus(row.id, { is_hot: value });
        ElMessage.success('急聘状态已更新');
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '更新失败');
    } finally {
        loadJobs();
    }
}

async function handleStatus(row, value) {
    try {
        await changeJobStatus(row.id, { job_status: value });
        ElMessage.success('状态已更新');
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '更新失败');
    } finally {
        loadJobs();
    }
}

onMounted(loadJobs);
</script>
