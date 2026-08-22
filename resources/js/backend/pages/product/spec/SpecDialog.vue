<!--
/**
 * 规格编辑弹窗
 *
 * @package     Resources\Backend\Pages\Product\Spec
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <el-dialog :model-value="modelValue" :title="form.id ? '修改规格' : '新增规格'" width="520px" @close="onClose" @update:model-value="(visible) => !visible && onClose()">
        <el-form ref="formRef" :model="form" :rules="rules" label-width="90px">
            <el-form-item label="规格名称" prop="spec_name">
                <el-input v-model="form.spec_name" placeholder="如 颜色、尺寸" />
            </el-form-item>
            <el-form-item label="排序">
                <el-input-number v-model="form.sort_order" :min="0" style="width:100%" />
            </el-form-item>
            <el-form-item label="状态">
                <el-radio-group v-model="form.spec_status">
                    <el-radio :value="1">显示</el-radio>
                    <el-radio :value="0">隐藏</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item v-if="!form.id" label="规格值">
                <el-input v-model="valueText" type="textarea" :rows="3" placeholder="每行一个，如：&#10;米白&#10;灰色" />
            </el-form-item>
            <el-form-item label="备注">
                <el-input v-model="form.spec_remark" type="textarea" :rows="2" />
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
import { createProductSpec, updateProductSpec } from '../../../api/product';

const props = defineProps({
    modelValue: Boolean,
    spec: Object,
});
const emit = defineEmits(['update:modelValue', 'saved']);
const formRef = ref();
const saving = ref(false);
const valueText = ref('');
const form = reactive(emptyForm());
const rules = {
    spec_name: [{ required: true, message: '请输入规格名称', trigger: 'blur' }],
};

watch(
    () => [props.modelValue, props.spec],
    () => {
        Object.assign(form, props.spec ? { ...emptyForm(), ...props.spec } : emptyForm());
        valueText.value = '';
    },
);

function emptyForm() {
    return {
        id: '',
        spec_name: '',
        spec_remark: '',
        spec_status: 1,
        sort_order: 0,
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
            await updateProductSpec(form.id, form);
            ElMessage.success('修改成功');
        } else {
            const values = valueText.value
                .split('\n')
                .map((item) => item.trim())
                .filter(Boolean)
                .map((value, index) => ({ value, sort_order: 100 - index, value_status: 1 }));
            await createProductSpec({ ...form, values });
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
