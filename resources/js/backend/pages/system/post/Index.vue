<!--
/**
 * 岗位管理页面
 *
 * @package     Resources\Backend\Pages\System\Post
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <div class="app-container">
        <div class="toolbar">
            <el-button v-if="can('system:post:add')" type="primary" plain @click="openDialog()">新增</el-button>
        </div>
        <el-table v-loading="loading" :data="rows" row-key="id" border default-expand-all>
            <el-table-column label="岗位名称" prop="post_name" min-width="180" />
            <el-table-column label="岗位编码" prop="post_code" min-width="140" />
            <el-table-column label="排序" prop="post_sort" width="80" align="center" />
            <el-table-column label="状态" width="90" align="center">
                <template #default="{ row }">
                    <el-tag :type="row.post_status === 1 ? 'success' : 'info'">{{ row.post_status === 1 ? '正常' : '停用' }}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="备注" prop="remark" min-width="180" show-overflow-tooltip />
            <el-table-column label="操作" width="180">
                <template #default="{ row }">
                    <el-button v-if="can('system:post:add')" link type="primary" @click="openDialog(null, row)">新增</el-button>
                    <el-button v-if="can('system:post:edit')" link type="primary" @click="openDialog(row)">修改</el-button>
                    <el-button v-if="can('system:post:remove')" link type="danger" @click="handleDelete(row)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <post-dialog v-model="dialog.visible" :post="dialog.post" :parent="dialog.parent" :tree="rows" @saved="loadPosts" />
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { deletePost, fetchPosts } from '../../../api/post';
import { useUserStore } from '../../../stores/user';
import PostDialog from './PostDialog.vue';

const userStore = useUserStore();
const can = (code) => userStore.hasPermission(code);
const loading = ref(false);
const rows = ref([]);
const dialog = reactive({
    visible: false,
    post: null,
    parent: null,
});

async function loadPosts() {
    loading.value = true;
    try {
        const { data } = await fetchPosts();
        rows.value = data.posts ?? [];
    } finally {
        loading.value = false;
    }
}

function openDialog(row, parent) {
    dialog.post = row ? { ...row } : null;
    dialog.parent = parent ? { ...parent } : null;
    dialog.visible = true;
}

async function handleDelete(row) {
    await ElMessageBox.confirm(`是否确认删除岗位「${row.post_name}」？`, '警告', { type: 'warning' });
    try {
        await deletePost(row.id);
        ElMessage.success('删除成功');
        loadPosts();
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '删除失败');
    }
}

onMounted(loadPosts);
</script>
