<!--
/**
 * 友情链接页面
 *
 * @package     Resources\Backend\Pages\Site\Link
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <div class="app-container">
        <el-form :inline="true" :model="query" class="search-form" @submit.prevent="handleSearch">
            <el-form-item label="网站名称">
                <el-input v-model="query.link_name" placeholder="请输入网站名称" clearable />
            </el-form-item>
            <el-form-item label="状态">
                <el-select v-model="query.link_status" placeholder="状态" clearable style="width:120px">
                    <el-option label="启用" :value="1" />
                    <el-option label="禁用" :value="0" />
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="handleSearch">搜索</el-button>
                <el-button @click="handleReset">重置</el-button>
            </el-form-item>
        </el-form>
        <div class="toolbar">
            <el-button v-if="can('cms:link:add')" type="primary" plain @click="openDialog()">新增</el-button>
        </div>
        <el-table v-loading="loading" :data="rows" border>
            <el-table-column label="网站名称" prop="link_name" min-width="140" />
            <el-table-column label="链接" prop="link_url" min-width="220" show-overflow-tooltip />
            <el-table-column label="描述" prop="link_desc" min-width="180" show-overflow-tooltip />
            <el-table-column label="排序" prop="link_sort" width="80" align="center" />
            <el-table-column label="状态" width="90" align="center">
                <template #default="{ row }">
                    <el-switch
                        :model-value="row.link_status"
                        :active-value="1"
                        :inactive-value="0"
                        :disabled="!can('cms:link:edit')"
                        @change="(value) => handleStatus(row, value)"
                    />
                </template>
            </el-table-column>
            <el-table-column label="操作" width="140">
                <template #default="{ row }">
                    <el-button v-if="can('cms:link:edit')" link type="primary" @click="openDialog(row)">修改</el-button>
                    <el-button v-if="can('cms:link:remove')" link type="danger" @click="handleDelete(row)">删除</el-button>
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
                @current-change="loadLinks"
                @size-change="loadLinks"
            />
        </div>
        <link-dialog v-model="dialog.visible" :link="dialog.link" @saved="loadLinks" />
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { changeFriendLinkStatus, deleteFriendLink, fetchFriendLinks } from '../../../api/link';
import { useUserStore } from '../../../stores/user';
import LinkDialog from './LinkDialog.vue';

const userStore = useUserStore();
const can = (code) => userStore.hasPermission(code);
const loading = ref(false);
const rows = ref([]);
const total = ref(0);
const query = reactive({
    link_name: '',
    link_status: undefined,
    page: 1,
    per_page: 10,
});
const dialog = reactive({
    visible: false,
    link: null,
});

async function loadLinks() {
    loading.value = true;
    try {
        const { data } = await fetchFriendLinks(query);
        rows.value = data.data ?? [];
        total.value = data.meta?.total ?? 0;
    } finally {
        loading.value = false;
    }
}

function handleSearch() {
    query.page = 1;
    loadLinks();
}

function handleReset() {
    query.link_name = '';
    query.link_status = undefined;
    query.page = 1;
    loadLinks();
}

function openDialog(row) {
    dialog.link = row ? { ...row } : null;
    dialog.visible = true;
}

async function handleDelete(row) {
    await ElMessageBox.confirm(`是否确认删除友情链接「${row.link_name}」？`, '警告', { type: 'warning' });
    await deleteFriendLink(row.id);
    ElMessage.success('删除成功');
    loadLinks();
}

async function handleStatus(row, value) {
    try {
        await changeFriendLinkStatus(row.id, value);
        ElMessage.success('状态已更新');
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '更新失败');
    } finally {
        loadLinks();
    }
}

onMounted(loadLinks);
</script>
