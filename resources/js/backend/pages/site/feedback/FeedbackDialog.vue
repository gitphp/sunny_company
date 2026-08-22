<!--
/**
 * 留言详情弹窗
 *
 * @package     Resources\Backend\Pages\Site\Feedback
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <el-dialog :model-value="modelValue" title="留言处理" width="640px" @close="onClose" @update:model-value="(visible) => !visible && onClose()">
        <el-descriptions v-loading="loading" :column="2" border>
            <el-descriptions-item label="联系人">{{ detail.fb_name }}</el-descriptions-item>
            <el-descriptions-item label="电话">{{ detail.fb_phone || '-' }}</el-descriptions-item>
            <el-descriptions-item label="邮箱">{{ detail.fb_email || '-' }}</el-descriptions-item>
            <el-descriptions-item label="公司">{{ detail.fb_company || '-' }}</el-descriptions-item>
            <el-descriptions-item label="标题" :span="2">{{ detail.fb_title }}</el-descriptions-item>
            <el-descriptions-item label="内容" :span="2">{{ detail.fb_content }}</el-descriptions-item>
            <el-descriptions-item label="IP">{{ detail.ip }}</el-descriptions-item>
            <el-descriptions-item label="时间">{{ detail.created_at }}</el-descriptions-item>
        </el-descriptions>
        <el-form style="margin-top:16px" label-width="80px">
            <el-form-item label="回复">
                <el-input v-model="reply" type="textarea" :rows="4" placeholder="填写回复内容后将标记为已处理" />
            </el-form-item>
        </el-form>
        <template #footer>
            <el-button @click="onClose">取消</el-button>
            <el-button type="primary" :loading="saving" @click="submit">回复</el-button>
        </template>
    </el-dialog>
</template>

<script setup>
import { ref, watch } from 'vue';
import { ElMessage } from 'element-plus';
import { fetchFeedback, replyFeedback } from '../../../api/feedback';

const props = defineProps({
    modelValue: Boolean,
    feedbackId: {
        type: String,
        default: '',
    },
});
const emit = defineEmits(['update:modelValue', 'saved']);
const loading = ref(false);
const saving = ref(false);
const reply = ref('');
const detail = ref({});

watch(
    () => [props.modelValue, props.feedbackId],
    async () => {
        if (!props.modelValue || !props.feedbackId) {
            return;
        }
        loading.value = true;
        try {
            const { data } = await fetchFeedback(props.feedbackId);
            detail.value = data.feedback ?? {};
            reply.value = detail.value.reply_content || '';
        } finally {
            loading.value = false;
        }
    },
);

function onClose() {
    emit('update:modelValue', false);
}

async function submit() {
    if (!reply.value.trim()) {
        ElMessage.warning('请填写回复内容');
        return;
    }
    saving.value = true;
    try {
        await replyFeedback(props.feedbackId, reply.value);
        ElMessage.success('回复成功');
        emit('saved');
        onClose();
    } catch (error) {
        ElMessage.error(error.response?.data?.message || Object.values(error.response?.data?.errors ?? {})[0]?.[0] || '回复失败');
    } finally {
        saving.value = false;
    }
}
</script>
