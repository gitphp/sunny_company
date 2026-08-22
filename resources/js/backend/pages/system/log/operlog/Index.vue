<template>
    <div class="app-container">
        <el-form :inline="true" :model="query" class="search-form" @submit.prevent="handleSearch">
            <el-form-item label="操作人">
                <el-input v-model="query.operator_name" placeholder="操作人" clearable />
            </el-form-item>
            <el-form-item label="模块">
                <el-input v-model="query.biz_type" placeholder="如 user/article" clearable style="width:140px" />
            </el-form-item>
            <el-form-item label="操作">
                <el-select v-model="query.action" placeholder="操作类型" clearable style="width:130px">
                    <el-option label="新增" value="INSERT" />
                    <el-option label="修改" value="UPDATE" />
                    <el-option label="删除" value="DELETE" />
                    <el-option label="登录" value="LOGIN" />
                    <el-option label="退出" value="LOGOUT" />
                </el-select>
            </el-form-item>
            <el-form-item label="状态">
                <el-select v-model="query.operator_status" placeholder="状态" clearable style="width:110px">
                    <el-option label="成功" :value="1" />
                    <el-option label="失败" :value="0" />
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="handleSearch">搜索</el-button>
                <el-button @click="handleReset">重置</el-button>
            </el-form-item>
        </el-form>
        <div class="toolbar">
            <el-button v-if="can('system:operlog:remove')" type="danger" plain :disabled="selected.length === 0" @click="handleBatchDelete">删除</el-button>
        </div>
        <el-table v-loading="loading" :data="rows" border @selection-change="(value) => (selected = value)">
            <el-table-column type="selection" width="50" align="center" />
            <el-table-column label="操作人" prop="operator_name" width="110" />
            <el-table-column label="模块" prop="biz_type" width="100" />
            <el-table-column label="活动" prop="activity_type" min-width="130" />
            <el-table-column label="操作" prop="action" width="90" />
            <el-table-column label="摘要" prop="biz_label" min-width="160" show-overflow-tooltip />
            <el-table-column label="状态" width="80" align="center">
                <template #default="{ row }">
                    <el-tag :type="row.operator_status === 1 ? 'success' : 'danger'">{{ row.operator_status === 1 ? '成功' : '失败' }}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="IP" prop="client_ip" width="130" />
            <el-table-column label="时间" prop="created_at" min-width="180" />
            <el-table-column label="操作" width="140" fixed="right">
                <template #default="{ row }">
                    <el-button link type="primary" @click="openDetail(row)">详情</el-button>
                    <el-button v-if="can('system:operlog:remove')" link type="danger" @click="handleDelete(row)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="table-footer">
            <el-pagination
                v-model:current-page="query.page"
                v-model:page-size="query.per_page"
                :total="total"
                :page-sizes="[10, 20, 50, 100]"
                layout="total, sizes, prev, pager, next"
                @current-change="loadLogs"
                @size-change="loadLogs"
            />
        </div>
        <el-drawer v-model="detail.visible" title="日志详情" size="480px">
            <el-descriptions :column="1" border>
                <el-descriptions-item label="操作人">{{ detail.data.operator_name }}</el-descriptions-item>
                <el-descriptions-item label="模块">{{ detail.data.biz_type }}</el-descriptions-item>
                <el-descriptions-item label="活动">{{ detail.data.activity_type }}</el-descriptions-item>
                <el-descriptions-item label="操作">{{ detail.data.action }}</el-descriptions-item>
                <el-descriptions-item label="摘要">{{ detail.data.biz_label }}</el-descriptions-item>
                <el-descriptions-item label="URL">{{ detail.data.request_url }}</el-descriptions-item>
                <el-descriptions-item label="方法">{{ detail.data.method_fun }}</el-descriptions-item>
                <el-descriptions-item label="UA">{{ detail.data.user_agent }}</el-descriptions-item>
                <el-descriptions-item label="错误">{{ detail.data.error_msg || '-' }}</el-descriptions-item>
                <el-descriptions-item label="请求数据">
                    <pre class="log-json">{{ formatJson(detail.data.new_value) }}</pre>
                </el-descriptions-item>
            </el-descriptions>
        </el-drawer>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { batchDeleteOperationLogs, deleteOperationLog, fetchOperationLog, fetchOperationLogs } from '../../../../api/operlog';
import { useUserStore } from '../../../../stores/user';

const userStore = useUserStore();
const can = (code) => userStore.hasPermission(code);
const loading = ref(false);
const rows = ref([]);
const total = ref(0);
const selected = ref([]);
const query = reactive({
    operator_name: '',
    biz_type: '',
    action: '',
    operator_status: undefined,
    page: 1,
    per_page: 10,
});
const detail = reactive({
    visible: false,
    data: {},
});

function formatJson(value) {
    if (value == null || value === '') {
        return '-';
    }
    return JSON.stringify(value, null, 2);
}

async function loadLogs() {
    loading.value = true;
    try {
        const { data } = await fetchOperationLogs(query);
        rows.value = data.data ?? [];
        total.value = data.meta?.total ?? 0;
    } finally {
        loading.value = false;
    }
}

function handleSearch() {
    query.page = 1;
    loadLogs();
}

function handleReset() {
    query.operator_name = '';
    query.biz_type = '';
    query.action = '';
    query.operator_status = undefined;
    query.page = 1;
    loadLogs();
}

async function openDetail(row) {
    const { data } = await fetchOperationLog(row.id);
    detail.data = data.log ?? {};
    detail.visible = true;
}

async function handleDelete(row) {
    await ElMessageBox.confirm('是否确认删除该日志？', '警告', { type: 'warning' });
    await deleteOperationLog(row.id);
    ElMessage.success('删除成功');
    loadLogs();
}

async function handleBatchDelete() {
    await ElMessageBox.confirm(`是否确认删除选中的 ${selected.value.length} 条日志？`, '警告', { type: 'warning' });
    await batchDeleteOperationLogs(selected.value.map((item) => item.id));
    ElMessage.success('删除成功');
    loadLogs();
}

onMounted(loadLogs);
</script>

<style scoped>
.log-json {
    margin: 0;
    white-space: pre-wrap;
    word-break: break-all;
    font-size: 12px;
    max-height: 240px;
    overflow: auto;
}
</style>
