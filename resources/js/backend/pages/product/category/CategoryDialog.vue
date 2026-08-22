<template>
    <el-dialog :model-value="modelValue" :title="form.id ? '修改分类' : '新增分类'" width="480px" @close="onClose" @update:model-value="(visible) => !visible && onClose()">
        <el-form ref="formRef" :model="form" :rules="rules" label-width="90px">
            <el-form-item label="上级分类">
                <el-tree-select
                    v-model="form.parent_id"
                    :data="parentOptions"
                    check-strictly
                    node-key="id"
                    :props="{ label: 'category_name' }"
                    placeholder="顶级分类"
                    clearable
                    style="width:100%"
                />
            </el-form-item>
            <el-form-item label="分类名称" prop="category_name">
                <el-input v-model="form.category_name" />
            </el-form-item>
            <el-form-item label="数量单位">
                <el-input v-model="form.unit" placeholder="如 件 / 套" />
            </el-form-item>
            <el-form-item label="排序">
                <el-input-number v-model="form.sort_order" :min="0" style="width:100%" />
            </el-form-item>
            <el-form-item label="状态">
                <el-radio-group v-model="form.cat_status">
                    <el-radio :value="1">显示</el-radio>
                    <el-radio :value="0">隐藏</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="备注">
                <el-input v-model="form.cat_remark" type="textarea" :rows="3" />
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
import { createProductCategory, updateProductCategory } from '../../../api/product';

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
    category_name: [{ required: true, message: '请输入分类名称', trigger: 'blur' }],
};
const parentOptions = computed(() => [
    { id: '0', category_name: '顶级分类', children: props.tree },
]);

watch(
    () => [props.modelValue, props.category, props.parent],
    () => {
        if (props.category) {
            Object.assign(form, emptyForm(), props.category, {
                parent_id: String(props.category.parent_id || '0'),
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
        category_name: '',
        unit: '',
        cat_status: 1,
        sort_order: 0,
        cat_remark: '',
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
            await updateProductCategory(form.id, form);
            ElMessage.success('修改成功');
        } else {
            await createProductCategory(form);
            ElMessage.success('新增成功');
        }
        emit('saved');
        onClose();
    } catch (error) {
        const bag = error.response?.data?.errors ?? {};
        ElMessage.error(Object.values(bag)[0]?.[0] || error.response?.data?.message || '保存失败');
    } finally {
        saving.value = false;
    }
}
</script>
