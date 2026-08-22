<!--
/**
 * 友情链接编辑弹窗
 *
 * @package     Resources\Backend\Pages\Site\Link
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <el-dialog :model-value="modelValue" :title="form.id ? '修改友情链接' : '新增友情链接'" width="520px" @close="onClose" @update:model-value="(visible) => !visible && onClose()">
        <el-form ref="formRef" :model="form" :rules="rules" label-width="90px">
            <el-form-item label="网站名称" prop="link_name">
                <el-input v-model="form.link_name" />
            </el-form-item>
            <el-form-item label="网站链接" prop="link_url">
                <el-input v-model="form.link_url" placeholder="https://" />
            </el-form-item>
            <el-form-item label="Logo">
                <el-input v-model="form.link_logo" placeholder="Logo 地址" />
            </el-form-item>
            <el-form-item label="排序">
                <el-input-number v-model="form.link_sort" :min="0" style="width:100%" />
            </el-form-item>
            <el-form-item label="状态">
                <el-radio-group v-model="form.link_status">
                    <el-radio :value="1">启用</el-radio>
                    <el-radio :value="0">禁用</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="描述">
                <el-input v-model="form.link_desc" type="textarea" :rows="3" />
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
import { createFriendLink, updateFriendLink } from '../../../api/link';

const props = defineProps({
    modelValue: Boolean,
    link: Object,
});
const emit = defineEmits(['update:modelValue', 'saved']);
const formRef = ref();
const saving = ref(false);
const form = reactive(emptyForm());
const rules = {
    link_name: [{ required: true, message: '请输入网站名称', trigger: 'blur' }],
    link_url: [{ required: true, message: '请输入网站链接', trigger: 'blur' }],
};

watch(
    () => [props.modelValue, props.link],
    () => {
        Object.assign(form, props.link ? { ...emptyForm(), ...props.link } : emptyForm());
    },
);

function emptyForm() {
    return {
        id: '',
        link_name: '',
        link_url: '',
        link_logo: '',
        link_desc: '',
        link_sort: 0,
        link_status: 1,
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
            await updateFriendLink(form.id, form);
            ElMessage.success('修改成功');
        } else {
            await createFriendLink(form);
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
