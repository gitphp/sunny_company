<template>
    <div class="app-container">
        <el-form :inline="true" :model="query" class="search-form" @submit.prevent="handleSearch">
            <el-form-item label="用户名称">
                <el-input v-model="query.user_name" placeholder="请输入用户名称" clearable />
            </el-form-item>
            <el-form-item label="手机号码">
                <el-input v-model="query.user_mobile" placeholder="请输入手机号码" clearable />
            </el-form-item>
            <el-form-item label="状态">
                <el-select v-model="query.user_status" placeholder="用户状态" clearable style="width:140px">
                    <el-option v-for="item in statusOptions" :key="item.value" :label="item.label" :value="item.value" />
                </el-select>
            </el-form-item>
            <el-form-item label="创建时间">
                <el-date-picker
                    v-model="query.daterange"
                    type="daterange"
                    value-format="YYYY-MM-DD"
                    start-placeholder="开始日期"
                    end-placeholder="结束日期"
                />
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="handleSearch">搜索</el-button>
                <el-button @click="handleReset">重置</el-button>
            </el-form-item>
        </el-form>

        <div class="toolbar">
            <el-button v-if="can('system:user:add')" type="primary" plain @click="openDialog()">新增</el-button>
            <el-button v-if="can('system:user:edit')" type="success" plain :disabled="single" @click="openDialog(selected[0])">修改</el-button>
            <el-button v-if="can('system:user:remove')" type="danger" plain :disabled="multiple" @click="handleBatchDelete">删除</el-button>
            <el-button v-if="can('system:user:export')" type="warning" plain @click="handleExport">导出</el-button>
        </div>

        <el-table v-loading="loading" :data="rows" border @selection-change="onSelectionChange">
            <el-table-column type="selection" width="50" align="center" />
            <el-table-column label="用户编号" prop="id" min-width="170" show-overflow-tooltip />
            <el-table-column label="用户名称" prop="user_name" min-width="110" />
            <el-table-column label="真实姓名" prop="real_name" min-width="110" />
            <el-table-column label="手机号码" prop="user_mobile" min-width="120" />
            <el-table-column label="邮箱" prop="user_email" min-width="180" show-overflow-tooltip />
            <el-table-column label="状态" width="90" align="center">
                <template #default="{ row }">
                    <el-switch
                        v-if="row.user_status === 0 || row.user_status === 1"
                        :model-value="row.user_status"
                        :active-value="1"
                        :inactive-value="0"
                        @change="(value) => handleStatus(row, value)"
                    />
                    <el-tag v-else :type="row.user_status === 2 ? 'warning' : 'info'">{{ row.user_status_label }}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="创建时间" prop="created_at" min-width="170" />
            <el-table-column label="操作" width="180" fixed="right">
                <template #default="{ row }">
                    <el-button v-if="can('system:user:edit')" link type="primary" @click="openDialog(row)">修改</el-button>
                    <el-button v-if="can('system:user:remove')" link type="danger" @click="handleDelete(row)">删除</el-button>
                    <el-dropdown v-if="can('system:user:resetPwd')" trigger="click" @command="() => openReset(row)">
                        <el-button link type="primary">更多</el-button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item>重置密码</el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </template>
            </el-table-column>
        </el-table>

        <div class="table-footer">
            <el-pagination
                v-model:current-page="query.page"
                v-model:page-size="query.per_page"
                :total="total"
                :page-sizes="[10, 20, 50, 100]"
                layout="total, sizes, prev, pager, next, jumper"
                @current-change="loadUsers"
                @size-change="loadUsers"
            />
        </div>

        <user-dialog v-model="dialog.visible" :user="dialog.user" @saved="loadUsers" />

        <el-dialog v-model="reset.visible" title="重置密码" width="420px">
            <el-form :model="reset" label-width="80px">
                <el-form-item label="新密码">
                    <el-input v-model="reset.password" type="password" show-password />
                </el-form-item>
            </el-form>
            <template #footer>
                <el-button @click="reset.visible = false">取消</el-button>
                <el-button type="primary" @click="submitReset">确定</el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { batchDeleteUsers, changeUserStatus, deleteUser, exportUsers, fetchUsers, resetUserPassword } from '../../../api/user';
import { useUserStore } from '../../../stores/user';
import UserDialog from './UserDialog.vue';

const userStore = useUserStore();
const can = (code) => userStore.hasPermission(code);

const statusOptions = [
    { label: '禁用', value: 0 },
    { label: '正常', value: 1 },
    { label: '冻结', value: 2 },
    { label: '注销', value: 3 },
];

const loading = ref(false);
const rows = ref([]);
const total = ref(0);
const selected = ref([]);
const single = computed(() => selected.value.length !== 1);
const multiple = computed(() => selected.value.length === 0);

const query = reactive({
    user_name: '',
    user_mobile: '',
    user_status: undefined,
    daterange: [],
    page: 1,
    per_page: 10,
});

const dialog = reactive({
    visible: false,
    user: null,
});

const reset = reactive({
    visible: false,
    user: null,
    password: '',
});

function queryParams() {
    const [begin_time, end_time] = query.daterange || [];
    return {
        user_name: query.user_name || undefined,
        user_mobile: query.user_mobile || undefined,
        user_status: query.user_status,
        begin_time,
        end_time,
        page: query.page,
        per_page: query.per_page,
    };
}

async function loadUsers() {
    loading.value = true;
    try {
        const { data } = await fetchUsers(queryParams());
        rows.value = data.data ?? [];
        total.value = data.meta?.total ?? 0;
    } finally {
        loading.value = false;
    }
}

function handleSearch() {
    query.page = 1;
    loadUsers();
}

function handleReset() {
    query.user_name = '';
    query.user_mobile = '';
    query.user_status = undefined;
    query.daterange = [];
    query.page = 1;
    loadUsers();
}

function onSelectionChange(value) {
    selected.value = value;
}

function openDialog(row) {
    dialog.user = row ? { ...row } : null;
    dialog.visible = true;
}

async function handleDelete(row) {
    await ElMessageBox.confirm(`是否确认删除用户「${row.user_name}」？`, '警告', { type: 'warning' });
    await deleteUser(row.id);
    ElMessage.success('删除成功');
    loadUsers();
}

async function handleBatchDelete() {
    await ElMessageBox.confirm(`是否确认删除选中的 ${selected.value.length} 条数据？`, '警告', { type: 'warning' });
    await batchDeleteUsers(selected.value.map((item) => item.id));
    ElMessage.success('删除成功');
    loadUsers();
}

async function handleStatus(row, value) {
    try {
        await changeUserStatus(row.id, value);
        ElMessage.success('状态已更新');
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '状态更新失败');
    } finally {
        loadUsers();
    }
}

async function handleExport() {
    const { data } = await exportUsers(queryParams());
    const url = URL.createObjectURL(data);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'user_account.csv';
    link.click();
    URL.revokeObjectURL(url);
}

function openReset(row) {
    reset.user = row;
    reset.password = '';
    reset.visible = true;
}

async function submitReset() {
    if (!reset.password || reset.password.length < 6) {
        ElMessage.warning('密码至少 6 位');
        return;
    }
    await resetUserPassword(reset.user.id, reset.password);
    ElMessage.success('密码已重置');
    reset.visible = false;
}

onMounted(loadUsers);
</script>
