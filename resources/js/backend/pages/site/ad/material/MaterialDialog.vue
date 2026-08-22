<!--
/**
 * 广告素材编辑弹窗
 *
 * @package     Resources\Backend\Pages\Site\Ad\Material
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <el-dialog :model-value="modelValue" :title="form.id ? '修改广告素材' : '新增广告素材'" width="560px" @close="onClose" @update:model-value="(visible) => !visible && onClose()">
        <el-form ref="formRef" :model="form" :rules="rules" label-width="90px">
            <el-form-item label="广告位" prop="position_id">
                <el-select v-model="form.position_id" style="width:100%">
                    <el-option v-for="item in positions" :key="item.id" :label="`${item.pos_name}（${item.pos_code}）`" :value="item.id" />
                </el-select>
            </el-form-item>
            <el-form-item label="标题" prop="title">
                <el-input v-model="form.title" />
            </el-form-item>
            <el-form-item label="图片地址" prop="image_url">
                <el-input v-model="form.image_url" placeholder="广告图片 URL" />
            </el-form-item>
            <el-form-item label="跳转链接">
                <el-input v-model="form.link_url" />
            </el-form-item>
            <el-form-item label="打开方式">
                <el-radio-group v-model="form.target">
                    <el-radio value="_blank">新窗口</el-radio>
                    <el-radio value="_self">当前窗口</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="排序">
                <el-input-number v-model="form.sort_order" style="width:100%" />
            </el-form-item>
            <el-form-item label="生效时间">
                <el-date-picker
                    v-model="form.daterange"
                    type="datetimerange"
                    value-format="YYYY-MM-DD HH:mm:ss"
                    start-placeholder="开始时间"
                    end-placeholder="结束时间"
                    style="width:100%"
                />
            </el-form-item>
            <el-form-item label="状态">
                <el-radio-group v-model="form.status">
                    <el-radio :value="1">上线</el-radio>
                    <el-radio :value="0">下线</el-radio>
                </el-radio-group>
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
import { createAdMaterial, updateAdMaterial } from '../../../../api/ad';

const props = defineProps({
    modelValue: Boolean,
    material: Object,
    positions: {
        type: Array,
        default: () => [],
    },
});
const emit = defineEmits(['update:modelValue', 'saved']);
const formRef = ref();
const saving = ref(false);
const form = reactive(emptyForm());
const rules = {
    position_id: [{ required: true, message: '请选择广告位', trigger: 'change' }],
    title: [{ required: true, message: '请输入广告标题', trigger: 'blur' }],
    image_url: [{ required: true, message: '请输入广告图片地址', trigger: 'blur' }],
};

watch(
    () => [props.modelValue, props.material],
    () => {
        if (props.material) {
            Object.assign(form, emptyForm(), props.material, {
                daterange: props.material.start_time || props.material.end_time
                    ? [props.material.start_time || '', props.material.end_time || '']
                    : [],
            });
        } else {
            Object.assign(form, emptyForm());
        }
    },
);

function emptyForm() {
    return {
        id: '',
        position_id: '',
        title: '',
        image_url: '',
        link_url: '',
        target: '_blank',
        sort_order: 0,
        daterange: [],
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
        const [start_time, end_time] = form.daterange || [];
        const payload = {
            ...form,
            start_time: start_time || null,
            end_time: end_time || null,
        };
        delete payload.daterange;
        if (form.id) {
            await updateAdMaterial(form.id, payload);
            ElMessage.success('修改成功');
        } else {
            await createAdMaterial(payload);
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
