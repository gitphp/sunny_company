<!--
/**
 * AI模型配置弹窗
 *
 * @package     Resources\Backend\Pages\Ai
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <el-dialog :model-value="modelValue" :title="form.id ? '修改模型' : '新增模型'" width="560px" @close="onClose" @update:model-value="(visible) => !visible && onClose()">
        <el-form ref="formRef" :model="form" :rules="rules" label-width="96px">
            <el-form-item v-if="!form.id && presets.length" label="快速填充">
                <el-select placeholder="选择预设自动填充" clearable style="width:100%" @change="applyPreset">
                    <el-option v-for="item in presets" :key="item.provider_name + item.model" :label="`${item.provider_name}（${item.model}）`" :value="item.provider_name + '|' + item.model" />
                </el-select>
            </el-form-item>
            <el-form-item label="显示名称" prop="provider_name">
                <el-input v-model="form.provider_name" />
            </el-form-item>
            <el-form-item label="接口地址" prop="base_url">
                <el-input v-model="form.base_url" placeholder="https://api.deepseek.com" />
            </el-form-item>
            <el-form-item label="模型标识" prop="model">
                <el-input v-model="form.model" placeholder="deepseek-chat" />
            </el-form-item>
            <el-form-item label="API Key" :prop="form.id ? '' : 'api_key'">
                <el-input v-model="form.api_key" type="password" show-password :placeholder="form.id ? '不填则保持原密钥' : '请输入密钥'" />
            </el-form-item>
            <el-row :gutter="12">
                <el-col :span="12">
                    <el-form-item label="温度">
                        <el-input-number v-model="form.temperature" :min="0" :max="2" :step="0.1" style="width:100%" />
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="最大Token">
                        <el-input-number v-model="form.max_tokens" :min="1" :max="8192" style="width:100%" />
                    </el-form-item>
                </el-col>
            </el-row>
            <el-form-item label="系统提示">
                <el-input v-model="form.system_prompt" type="textarea" :rows="3" placeholder="可选，会作为 system 消息发送" />
            </el-form-item>
            <el-form-item label="状态">
                <el-radio-group v-model="form.status">
                    <el-radio :value="1">启用</el-radio>
                    <el-radio :value="0">禁用</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="默认模型">
                <el-switch v-model="form.is_default" :active-value="1" :inactive-value="0" />
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
import { createAiProvider, updateAiProvider } from '../../api/ai';

const props = defineProps({
    modelValue: Boolean,
    provider: Object,
    presets: {
        type: Array,
        default: () => [],
    },
});
const emit = defineEmits(['update:modelValue', 'saved']);
const formRef = ref();
const saving = ref(false);
const form = reactive(emptyForm());
const rules = {
    provider_name: [{ required: true, message: '请输入显示名称', trigger: 'blur' }],
    base_url: [{ required: true, message: '请输入接口地址', trigger: 'blur' }],
    model: [{ required: true, message: '请输入模型标识', trigger: 'blur' }],
};

watch(
    () => [props.modelValue, props.provider],
    () => {
        Object.assign(form, props.provider ? { ...emptyForm(), ...props.provider } : emptyForm());
    },
);

function emptyForm() {
    return {
        id: '',
        provider_name: '',
        driver: 'openai',
        base_url: '',
        api_key: '',
        model: '',
        temperature: 0.7,
        max_tokens: 2048,
        system_prompt: '',
        is_default: 0,
        status: 1,
        sort_order: 0,
    };
}

function applyPreset(value) {
    const [name, model] = String(value || '').split('|');
    const preset = props.presets.find((item) => item.provider_name === name && item.model === model);
    if (!preset) {
        return;
    }
    Object.assign(form, {
        ...form,
        provider_name: preset.provider_name,
        base_url: preset.base_url,
        model: preset.model,
        system_prompt: preset.system_prompt || form.system_prompt,
        driver: 'openai',
    });
}

function onClose() {
    emit('update:modelValue', false);
}

async function submit() {
    await formRef.value.validate();
    saving.value = true;
    try {
        if (form.id) {
            await updateAiProvider(form.id, form);
            ElMessage.success('修改成功');
        } else {
            await createAiProvider(form);
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
