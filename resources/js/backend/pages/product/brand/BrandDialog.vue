<template>
    <el-dialog :model-value="modelValue" :title="form.id ? '修改品牌' : '新增品牌'" width="480px" @close="onClose" @update:model-value="(visible) => !visible && onClose()">
        <el-form ref="formRef" :model="form" :rules="rules" label-width="90px">
            <el-form-item label="品牌名称" prop="brand_name">
                <el-input v-model="form.brand_name" />
            </el-form-item>
            <el-form-item label="英文别名">
                <el-input v-model="form.alias" />
            </el-form-item>
            <el-form-item label="排序">
                <el-input-number v-model="form.sort_order" :min="0" style="width:100%" />
            </el-form-item>
            <el-form-item label="状态">
                <el-radio-group v-model="form.is_show">
                    <el-radio :value="1">显示</el-radio>
                    <el-radio :value="0">隐藏</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="备注">
                <el-input v-model="form.brand_remark" type="textarea" :rows="3" />
            </el-form-item>
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
import { createProductBrand, updateProductBrand } from '../../../api/product';

const props = defineProps({
    modelValue: Boolean,
    brand: Object,
});
const emit = defineEmits(['update:modelValue', 'saved']);
const formRef = ref();
const saving = ref(false);
const form = reactive(emptyForm());
const rules = {
    brand_name: [{ required: true, message: '请输入品牌名称', trigger: 'blur' }],
};

watch(
    () => [props.modelValue, props.brand],
    () => {
        Object.assign(form, props.brand ? { ...emptyForm(), ...props.brand } : emptyForm());
    },
);

function emptyForm() {
    return {
        id: '',
        brand_name: '',
        alias: '',
        is_show: 1,
        sort_order: 0,
        brand_remark: '',
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
            await updateProductBrand(form.id, form);
            ElMessage.success('修改成功');
        } else {
            await createProductBrand(form);
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
