<template>
    <div class="app-container">
        <div class="split-layout">
            <aside class="split-side">
                <el-input v-model="catKeyword" placeholder="请输入分类名称" clearable style="margin-bottom:12px" />
                <el-tree
                    ref="catTreeRef"
                    :data="categoryTree"
                    node-key="id"
                    default-expand-all
                    highlight-current
                    :filter-node-method="filterCategory"
                    :props="{ label: 'cat_name' }"
                    @node-click="onCategoryClick"
                />
            </aside>
            <div class="split-main">
                <el-form :inline="true" :model="query" class="search-form" @submit.prevent="handleSearch">
                    <el-form-item label="标题">
                        <el-input v-model="query.title" placeholder="请输入文章标题" clearable />
                    </el-form-item>
                    <el-form-item label="状态">
                        <el-select v-model="query.art_status" placeholder="文章状态" clearable style="width:140px">
                            <el-option v-for="item in statusOptions" :key="item.value" :label="item.label" :value="item.value" />
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="handleSearch">搜索</el-button>
                        <el-button @click="handleReset">重置</el-button>
                    </el-form-item>
                </el-form>

                <div class="toolbar">
                    <el-button v-if="can('cms:article:add')" type="primary" plain @click="openDialog()">新增</el-button>
                    <el-button v-if="can('cms:article:remove')" type="danger" plain :disabled="multiple" @click="handleBatchDelete">删除</el-button>
                </div>

                <el-table v-loading="loading" :data="rows" border @selection-change="onSelectionChange">
                    <el-table-column type="selection" width="50" align="center" />
                    <el-table-column label="标题" prop="title" min-width="220" show-overflow-tooltip />
                    <el-table-column label="分类" prop="category_name" min-width="120" />
                    <el-table-column label="作者" prop="author_name" width="100" />
                    <el-table-column label="状态" width="110" align="center">
                        <template #default="{ row }">
                            <el-tag :type="statusType(row.art_status)">{{ row.art_status_label }}</el-tag>
                        </template>
                    </el-table-column>
                    <el-table-column label="置顶" width="80" align="center">
                        <template #default="{ row }">
                            <el-switch
                                :model-value="row.is_top"
                                :active-value="1"
                                :inactive-value="0"
                                :disabled="!can('cms:article:edit')"
                                @change="(value) => handleTop(row, value)"
                            />
                        </template>
                    </el-table-column>
                    <el-table-column label="浏览" prop="view_count" width="80" align="center" />
                    <el-table-column label="发布时间" prop="published_at" min-width="170" />
                    <el-table-column label="操作" width="180" fixed="right">
                        <template #default="{ row }">
                            <el-button v-if="can('cms:article:edit')" link type="primary" @click="openDialog(row)">修改</el-button>
                            <el-button v-if="can('cms:article:remove')" link type="danger" @click="handleDelete(row)">删除</el-button>
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
                        @current-change="loadArticles"
                        @size-change="loadArticles"
                    />
                </div>
            </div>
        </div>
        <article-dialog v-model="dialog.visible" :article-id="dialog.id" @saved="loadArticles" />
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { batchDeleteArticles, changeArticleStatus, deleteArticle, fetchArticles } from '../../../api/article';
import { fetchOptionArticleCategories } from '../../../api/options';
import { useUserStore } from '../../../stores/user';
import ArticleDialog from './ArticleDialog.vue';

const userStore = useUserStore();
const can = (code) => userStore.hasPermission(code);

const statusOptions = [
    { label: '草稿', value: 1 },
    { label: '待审核', value: 2 },
    { label: '审核通过', value: 3 },
    { label: '已发布', value: 4 },
    { label: '已下线', value: 5 },
    { label: '审核驳回', value: 6 },
    { label: '回收站', value: 7 },
];

const loading = ref(false);
const rows = ref([]);
const total = ref(0);
const selected = ref([]);
const categoryTree = ref([]);
const catKeyword = ref('');
const catTreeRef = ref();
const multiple = computed(() => selected.value.length === 0);

const query = reactive({
    title: '',
    art_status: undefined,
    category_id: '',
    page: 1,
    per_page: 10,
});

const dialog = reactive({
    visible: false,
    id: '',
});

function statusType(status) {
    return {
        1: 'info',
        2: 'warning',
        3: '',
        4: 'success',
        5: 'info',
        6: 'danger',
        7: 'info',
    }[status] || 'info';
}

async function loadArticles() {
    loading.value = true;
    try {
        const { data } = await fetchArticles({
            title: query.title || undefined,
            art_status: query.art_status,
            category_id: query.category_id || undefined,
            page: query.page,
            per_page: query.per_page,
        });
        rows.value = data.data ?? [];
        total.value = data.meta?.total ?? 0;
    } finally {
        loading.value = false;
    }
}

function handleSearch() {
    query.page = 1;
    loadArticles();
}

function handleReset() {
    query.title = '';
    query.art_status = undefined;
    query.category_id = '';
    query.page = 1;
    loadArticles();
}

function filterCategory(value, data) {
    if (!value) {
        return true;
    }
    return data.cat_name.includes(value);
}

function onCategoryClick(node) {
    query.category_id = node.id;
    query.page = 1;
    loadArticles();
}

watch(catKeyword, (value) => {
    catTreeRef.value?.filter(value);
});

async function loadCategories() {
    const { data } = await fetchOptionArticleCategories({ cat_type: 0 });
    categoryTree.value = data.categories ?? [];
}

function onSelectionChange(value) {
    selected.value = value;
}

function openDialog(row) {
    dialog.id = row?.id || '';
    dialog.visible = true;
}

async function handleDelete(row) {
    await ElMessageBox.confirm(`是否确认删除文章「${row.title}」？`, '警告', { type: 'warning' });
    await deleteArticle(row.id);
    ElMessage.success('删除成功');
    loadArticles();
}

async function handleBatchDelete() {
    await ElMessageBox.confirm(`是否确认删除选中的 ${selected.value.length} 条数据？`, '警告', { type: 'warning' });
    await batchDeleteArticles(selected.value.map((item) => item.id));
    ElMessage.success('删除成功');
    loadArticles();
}

async function handleTop(row, value) {
    try {
        await changeArticleStatus(row.id, { is_top: value });
        ElMessage.success('置顶状态已更新');
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '更新失败');
    } finally {
        loadArticles();
    }
}

onMounted(async () => {
    await loadCategories();
    loadArticles();
});
</script>
