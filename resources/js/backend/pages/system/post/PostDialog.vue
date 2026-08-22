<template>
    <el-dialog :model-value="modelValue" :title="form.id ? '修改岗位' : '新增岗位'" width="520px" @close="onClose" @update:model-value="(visible) => !visible && onClose()">
        <el-form ref="formRef" :model="form" :rules="rules" label-width="90px">
            <el-form-item label="上级岗位" prop="parent_id">
                <el-tree-select
                    v-model="form.parent_id"
                    :data="parentOptions"
                    check-strictly
                    node-key="id"
                    :props="{ label: 'post_name' }"
                    placeholder="顶级岗位"
                    clearable
                    style="width:100%"
                />
            </el-form-item>
            <el-form-item label="岗位名称" prop="post_name">
                <el-input v-model="form.post_name" />
            </el-form-item>
            <el-form-item label="岗位编码" prop="post_code">
                <el-input v-model="form.post_code" />
            </el-form-item>
            <el-form-item label="显示排序">
                <el-input-number v-model="form.post_sort" :min="0" style="width:100%" />
            </el-form-item>
            <el-form-item label="状态">
                <el-radio-group v-model="form.post_status">
                    <el-radio :value="1">正常</el-radio>
                    <el-radio :value="0">停用</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="备注">
                <el-input v-model="form.remark" type="textarea" :rows="3" />
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
import { createPost, updatePost } from '../../../api/post';

const props = defineProps({
    modelValue: Boolean,
    post: Object,
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
    post_name: [{ required: true, message: '请输入岗位名称', trigger: 'blur' }],
    post_code: [{ required: true, message: '请输入岗位编码', trigger: 'blur' }],
};
const parentOptions = computed(() => [
    { id: '0', post_name: '顶级岗位', children: props.tree },
]);

watch(
    () => [props.modelValue, props.post, props.parent],
    () => {
        if (props.post) {
            Object.assign(form, {
                id: props.post.id,
                parent_id: String(props.post.parent_id || '0'),
                post_name: props.post.post_name,
                post_code: props.post.post_code,
                post_sort: props.post.post_sort,
                post_status: props.post.post_status,
                remark: props.post.remark,
            });
        } else {
            Object.assign(form, emptyForm(), {
                parent_id: props.parent?.id || '0',
            });
        }
    },
);

function emptyForm() {
    return {
        id: '',
        parent_id: '0',
        post_name: '',
        post_code: '',
        post_sort: 0,
        post_status: 1,
        remark: '',
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
            await updatePost(form.id, form);
            ElMessage.success('修改成功');
        } else {
            await createPost(form);
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
