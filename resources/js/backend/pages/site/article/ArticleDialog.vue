<template>
    <el-dialog :model-value="modelValue" :title="form.id ? '修改文章' : '新增文章'" width="820px" top="6vh" @close="onClose" @update:model-value="(visible) => !visible && onClose()">
        <el-form ref="formRef" v-loading="loading" :model="form" :rules="rules" label-width="90px">
            <el-tabs v-model="activeTab">
                <el-tab-pane label="基本信息" name="basic">
                    <el-form-item label="文章标题" prop="title">
                        <el-input v-model="form.title" />
                    </el-form-item>
                    <el-form-item label="副标题">
                        <el-input v-model="form.subtitle" />
                    </el-form-item>
                    <el-form-item label="所属分类" prop="category_id">
                        <el-tree-select
                            v-model="form.category_id"
                            :data="categoryOptions"
                            check-strictly
                            node-key="id"
                            :props="{ label: 'cat_name' }"
                            placeholder="请选择分类"
                            clearable
                            style="width:100%"
                        />
                    </el-form-item>
                    <el-form-item label="封面图">
                        <el-input v-model="form.art_cover" placeholder="封面图 URL" />
                    </el-form-item>
                    <el-form-item label="摘要">
                        <el-input v-model="form.summary" type="textarea" :rows="3" />
                    </el-form-item>
                    <el-form-item label="文章状态">
                        <el-select v-model="form.art_status" style="width:100%">
                            <el-option v-for="item in statusOptions" :key="item.value" :label="item.label" :value="item.value" />
                        </el-select>
                    </el-form-item>
                    <el-form-item v-if="form.art_status === 6" label="驳回原因" prop="reject_reason">
                        <el-input v-model="form.reject_reason" type="textarea" :rows="2" />
                    </el-form-item>
                    <el-form-item label="来源">
                        <el-input v-model="form.source" placeholder="原创 / 转载 / 翻译" />
                    </el-form-item>
                    <el-form-item label="原文链接">
                        <el-input v-model="form.source_url" />
                    </el-form-item>
                    <el-form-item label="选项">
                        <el-checkbox :model-value="form.is_top === 1" @change="(value) => (form.is_top = value ? 1 : 0)">置顶</el-checkbox>
                        <el-checkbox :model-value="form.is_original === 1" @change="(value) => (form.is_original = value ? 1 : 0)">原创</el-checkbox>
                        <el-checkbox :model-value="form.is_commentable === 1" @change="(value) => (form.is_commentable = value ? 1 : 0)">允许评论</el-checkbox>
                    </el-form-item>
                </el-tab-pane>
                <el-tab-pane label="正文内容" name="content">
                    <el-form-item label="内容类型">
                        <el-radio-group v-model="form.content_type">
                            <el-radio :value="1">富文本</el-radio>
                            <el-radio :value="2">Markdown</el-radio>
                            <el-radio :value="3">纯文本</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="正文">
                        <el-input v-model="form.art_content" type="textarea" :rows="16" />
                    </el-form-item>
                </el-tab-pane>
                <el-tab-pane label="SEO" name="seo">
                    <el-form-item label="SEO标题">
                        <el-input v-model="form.seo_title" />
                    </el-form-item>
                    <el-form-item label="关键词">
                        <el-input v-model="form.seo_keywords" />
                    </el-form-item>
                    <el-form-item label="SEO描述">
                        <el-input v-model="form.seo_description" type="textarea" :rows="3" />
                    </el-form-item>
                </el-tab-pane>
            </el-tabs>
        </el-form>
        <template #footer>
            <el-button @click="onClose">取消</el-button>
            <el-button type="primary" :loading="saving" @click="submit">确定</el-button>
        </template>
    </el-dialog>
</template>

<script setup>
import { reactive, ref, watch } from 'vue';
import { ElMessage } from 'element-plus';
import { createArticle, fetchArticle, updateArticle } from '../../../api/article';
import { fetchOptionArticleCategories } from '../../../api/options';

const props = defineProps({
    modelValue: Boolean,
    articleId: {
        type: String,
        default: '',
    },
});
const emit = defineEmits(['update:modelValue', 'saved']);
const formRef = ref();
const saving = ref(false);
const loading = ref(false);
const activeTab = ref('basic');
const categoryOptions = ref([]);
const form = reactive(emptyForm());
const statusOptions = [
    { label: '草稿', value: 1 },
    { label: '待审核', value: 2 },
    { label: '审核通过', value: 3 },
    { label: '已发布', value: 4 },
    { label: '已下线', value: 5 },
    { label: '审核驳回', value: 6 },
    { label: '回收站', value: 7 },
];
const rules = {
    title: [{ required: true, message: '请输入文章标题', trigger: 'blur' }],
};

watch(
    () => [props.modelValue, props.articleId],
    async () => {
        if (!props.modelValue) {
            return;
        }

        activeTab.value = 'basic';
        await loadCategories();

        if (props.articleId) {
            loading.value = true;
            try {
                const { data } = await fetchArticle(props.articleId);
                Object.assign(form, emptyForm(), data.article, {
                    category_id: data.article.category_id && data.article.category_id !== '0' ? data.article.category_id : '',
                });
            } finally {
                loading.value = false;
            }
        } else {
            Object.assign(form, emptyForm());
        }
    },
);

function emptyForm() {
    return {
        id: '',
        title: '',
        subtitle: '',
        art_cover: '',
        art_content: '',
        content_type: 1,
        summary: '',
        category_id: '',
        tag_ids: [],
        source: '原创',
        source_url: '',
        art_status: 1,
        is_top: 0,
        is_original: 1,
        is_commentable: 1,
        seo_title: '',
        seo_keywords: '',
        seo_description: '',
        reject_reason: '',
    };
}

async function loadCategories() {
    const { data } = await fetchOptionArticleCategories({ cat_type: 0 });
    categoryOptions.value = data.categories ?? [];
}

function onClose() {
    emit('update:modelValue', false);
}

async function submit() {
    await formRef.value.validate();
    saving.value = true;
    try {
        const payload = {
            ...form,
            category_id: form.category_id || '0',
        };
        if (form.id) {
            await updateArticle(form.id, payload);
            ElMessage.success('修改成功');
        } else {
            await createArticle(payload);
            ElMessage.success('新增成功');
        }
        emit('saved');
        onClose();
    } catch (error) {
        const bag = error.response?.data?.errors ?? {};
        const first = Object.values(bag)[0]?.[0];
        ElMessage.error(first || error.response?.data?.message || '保存失败');
    } finally {
        saving.value = false;
    }
}
</script>
