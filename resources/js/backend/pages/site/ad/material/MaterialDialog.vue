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
            <el-form-item label="图片" prop="image_url">
                <el-upload
                    v-model:file-list="imageFiles"
                    list-type="picture-card"
                    :limit="1"
                    accept="image/jpeg,image/png,image/gif,image/webp,image/bmp"
                    :http-request="handleUpload"
                    :on-remove="onRemoveImage"
                    :on-preview="previewImage"
                    :on-exceed="() => ElMessage.warning('只能上传一张图片')"
                >
                    <el-icon><Plus /></el-icon>
                </el-upload>
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
        <el-dialog v-model="preview.visible" width="640px" append-to-body title="预览">
            <img :src="preview.url" alt="" style="display:block;width:100%" />
        </el-dialog>
    </el-dialog>
</template>

<script setup>
import { nextTick, reactive, ref, watch } from 'vue';
import { ElMessage } from 'element-plus';
import { createAdMaterial, updateAdMaterial } from '../../../../api/ad';
import { uploadFile } from '../../../../api/upload';

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
const imageFiles = ref([]);
const preview = reactive({ visible: false, url: '' });
const form = reactive(emptyForm());
const rules = {
    position_id: [{ required: true, message: '请选择广告位', trigger: 'change' }],
    title: [{ required: true, message: '请输入广告标题', trigger: 'blur' }],
    image_url: [{ required: true, message: '请上传广告图片', trigger: 'change' }],
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
        fillImage(form.image_url);
        preview.visible = false;
        preview.url = '';
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

function fillImage(url) {
    imageFiles.value = url
        ? [{ uid: url, name: 'image', url, status: 'success' }]
        : [];
}

async function handleUpload(option) {
    try {
        const { data } = await uploadFile(option.file, 'ads');
        form.image_url = data.file.file_url;
        option.onSuccess(data.file);
        await nextTick();
        imageFiles.value = imageFiles.value.map((item) => (
            item.uid === option.file.uid
                ? { ...item, url: data.file.file_url, status: 'success', response: data.file }
                : item
        ));
        formRef.value?.validateField('image_url');
    } catch (error) {
        option.onError(error);
        ElMessage.error(error.response?.data?.errors?.file?.[0] || error.response?.data?.message || '上传失败');
    }
}

function onRemoveImage() {
    form.image_url = '';
}

function previewImage(file) {
    const url = file.response?.file_url || file.url || '';
    preview.url = url.startsWith('blob:') ? '' : url;
    if (preview.url) {
        preview.visible = true;
    }
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
