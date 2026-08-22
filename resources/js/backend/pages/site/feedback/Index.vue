<template>
    <div class="app-container">
        <el-form :inline="true" :model="query" class="search-form" @submit.prevent="handleSearch">
            <el-form-item label="关键词">
                <el-input v-model="query.keyword" placeholder="姓名/标题/电话/邮箱" clearable />
            </el-form-item>
            <el-form-item label="状态">
                <el-select v-model="query.fb_status" placeholder="处理状态" clearable style="width:140px">
                    <el-option label="未处理" :value="0" />
                    <el-option label="已处理" :value="1" />
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="handleSearch">搜索</el-button>
                <el-button @click="handleReset">重置</el-button>
            </el-form-item>
        </el-form>
        <div class="toolbar">
            <el-button v-if="can('cms:feedback:remove')" type="danger" plain :disabled="selected.length === 0" @click="handleBatchDelete">删除</el-button>
        </div>
        <el-table v-loading="loading" :data="rows" border @selection-change="(value) => (selected = value)">
            <el-table-column type="selection" width="50" align="center" />
            <el-table-column label="联系人" prop="fb_name" width="110" />
            <el-table-column label="标题" prop="fb_title" min-width="180" show-overflow-tooltip />
            <el-table-column label="电话" prop="fb_phone" width="130" />
            <el-table-column label="邮箱" prop="fb_email" min-width="160" show-overflow-tooltip />
            <el-table-column label="公司" prop="fb_company" min-width="140" show-overflow-tooltip />
            <el-table-column label="状态" width="100" align="center">
                <template #default="{ row }">
                    <el-tag :type="row.fb_status === 1 ? 'success' : 'warning'">{{ row.fb_status_label }}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="留言时间" prop="created_at" min-width="170" />
            <el-table-column label="操作" width="160" fixed="right">
                <template #default="{ row }">
                    <el-button v-if="can('cms:feedback:reply')" link type="primary" @click="openDialog(row)">处理</el-button>
                    <el-button v-if="can('cms:feedback:remove')" link type="danger" @click="handleDelete(row)">删除</el-button>
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
                @current-change="loadFeedbacks"
                @size-change="loadFeedbacks"
            />
        </div>
        <feedback-dialog v-model="dialog.visible" :feedback-id="dialog.id" @saved="loadFeedbacks" />
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { batchDeleteFeedbacks, deleteFeedback, fetchFeedbacks } from '../../../api/feedback';
import { useUserStore } from '../../../stores/user';
import FeedbackDialog from './FeedbackDialog.vue';

const userStore = useUserStore();
const can = (code) => userStore.hasPermission(code);
const loading = ref(false);
const rows = ref([]);
const total = ref(0);
const selected = ref([]);
const query = reactive({
    keyword: '',
    fb_status: undefined,
    page: 1,
    per_page: 10,
});
const dialog = reactive({
    visible: false,
    id: '',
});

async function loadFeedbacks() {
    loading.value = true;
    try {
        const { data } = await fetchFeedbacks(query);
        rows.value = data.data ?? [];
        total.value = data.meta?.total ?? 0;
    } finally {
        loading.value = false;
    }
}

function handleSearch() {
    query.page = 1;
    loadFeedbacks();
}

function handleReset() {
    query.keyword = '';
    query.fb_status = undefined;
    query.page = 1;
    loadFeedbacks();
}

function openDialog(row) {
    dialog.id = row.id;
    dialog.visible = true;
}

async function handleDelete(row) {
    await ElMessageBox.confirm(`是否确认删除留言「${row.fb_title}」？`, '警告', { type: 'warning' });
    await deleteFeedback(row.id);
    ElMessage.success('删除成功');
    loadFeedbacks();
}

async function handleBatchDelete() {
    await ElMessageBox.confirm(`是否确认删除选中的 ${selected.value.length} 条留言？`, '警告', { type: 'warning' });
    await batchDeleteFeedbacks(selected.value.map((item) => item.id));
    ElMessage.success('删除成功');
    loadFeedbacks();
}

onMounted(loadFeedbacks);
</script>
