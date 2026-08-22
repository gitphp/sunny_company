<template>
    <el-dialog :model-value="modelValue" :title="form.id ? '修改分类' : '新增分类'" width="520px" @close="onClose" @update:model-value="(visible) => !visible && onClose()">
        <el-form ref="formRef" :model="form" :rules="rules" label-width="90px">
            <el-form-item label="上级分类" prop="parent_id">
                <el-tree-select
                    v-model="form.parent_id"
                    :data="parentOptions"
                    check-strictly
                    node-key="id"
                    :props="{ label: 'cat_name' }"
                    placeholder="顶级分类"
                    clearable
                    style="width:100%"
                />
            </el-form-item>
            <el-form-item label="分类类型" prop="cat_type">
                <el-radio-group v-model="form.cat_type">
                    <el-radio :value="0">文章分类</el-radio>
                    <el-radio :value="1">导航分类</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="分类名称" prop="cat_name">
                <el-input v-model="form.cat_name" />
            </el-form-item>
            <el-form-item label="URL别名" prop="cat_url">
                <el-input v-model="form.cat_url" placeholder="如 company-news" />
            </el-form-item>
            <el-form-item label="显示排序">
                <el-input-number v-model="form.cat_sort" :min="0" style="width:100%" />
            </el-form-item>
            <el-form-item label="状态">
                <el-radio-group v-model="form.status">
                    <el-radio :value="1">正常</el-radio>
                    <el-radio :value="0">停用</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="描述">
                <el-input v-model="form.description" type="textarea" :rows="3" />
            </el-form-item>
        </el-form>
        <template #footer>
            <el-button @click="onClose">取消</el-button>
            <el-button type="primary" :loading="saving" @click="submit">确定</el-button>
        </template>
    </el-dialog>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { ElMessage } from 'element-plus';
import { createCategory, updateCategory } from '../../../api/category';

const props = defineProps({
    modelValue: Boolean,
    category: Object,
    parent: Object,
    tree: {
        type: Array,
        default: () => [],
    },
});
const emit = defineEmits(['update:modelValue', 'saved']);
const formRef = ref();
const saving = ref(false);
const form = reactive(emptyForm());
const rules = {
    cat_name: [{ required: true, message: '请输入分类名称', trigger: 'blur' }],
    cat_type: [{ required: true, message: '请选择分类类型', trigger: 'change' }],
};
const parentOptions = computed(() => [
    { id: '0', cat_name: '顶级分类', children: props.tree },
]);

watch(
    () => [props.modelValue, props.category, props.parent],
    () => {
        if (props.category) {
            Object.assign(form, {
                id: props.category.id,
                parent_id: String(props.category.parent_id || '0'),
                cat_type: props.category.cat_type,
                cat_name: props.category.cat_name,
                cat_url: props.category.cat_url,
                cat_sort: props.category.cat_sort,
                status: props.category.status,
                description: props.category.description,
            });
        } else {
            Object.assign(form, emptyForm(), {
                parent_id: props.parent?.id || '0',
                cat_type: props.parent?.cat_type ?? 0,
            });
        }
    },
);

function emptyForm() {
    return {
        id: '',
        parent_id: '0',
        cat_type: 0,
        cat_name: '',
        cat_url: '',
        cat_sort: 0,
        status: 1,
        description: '',
    };
}

function onClose() {
    emit('update:modelValue', false);
}

async function submit() {
    await formRef.value.validate();
    saving.value = true;
    try {
        if (form.id) {
            await updateCategory(form.id, form);
            ElMessage.success('修改成功');
        } else {
            await createCategory(form);
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
