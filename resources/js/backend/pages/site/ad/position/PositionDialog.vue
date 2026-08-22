<template>
    <el-dialog :model-value="modelValue" :title="form.id ? '修改广告位' : '新增广告位'" width="520px" @close="onClose" @update:model-value="(visible) => !visible && onClose()">
        <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
            <el-form-item label="广告位名称" prop="pos_name">
                <el-input v-model="form.pos_name" />
            </el-form-item>
            <el-form-item label="标识" prop="pos_code">
                <el-input v-model="form.pos_code" placeholder="如 home_top_banner" />
            </el-form-item>
            <el-form-item label="建议宽度">
                <el-input-number v-model="form.ad_width" :min="0" style="width:100%" />
            </el-form-item>
            <el-form-item label="建议高度">
                <el-input-number v-model="form.ad_height" :min="0" style="width:100%" />
            </el-form-item>
            <el-form-item label="状态">
                <el-radio-group v-model="form.status">
                    <el-radio :value="1">正常</el-radio>
                    <el-radio :value="0">禁用</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="描述">
                <el-input v-model="form.pos_desc" type="textarea" :rows="3" />
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
import { createAdPosition, updateAdPosition } from '../../../../api/ad';

const props = defineProps({
    modelValue: Boolean,
    position: Object,
});
const emit = defineEmits(['update:modelValue', 'saved']);
const formRef = ref();
const saving = ref(false);
const form = reactive(emptyForm());
const rules = {
    pos_name: [{ required: true, message: '请输入广告位名称', trigger: 'blur' }],
    pos_code: [{ required: true, message: '请输入广告位标识', trigger: 'blur' }],
};

watch(
    () => [props.modelValue, props.position],
    () => {
        Object.assign(form, props.position ? { ...emptyForm(), ...props.position } : emptyForm());
    },
);

function emptyForm() {
    return {
        id: '',
        pos_name: '',
        pos_code: '',
        pos_desc: '',
        ad_width: 0,
        ad_height: 0,
        status: 1,
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
            await updateAdPosition(form.id, form);
            ElMessage.success('修改成功');
        } else {
            await createAdPosition(form);
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
