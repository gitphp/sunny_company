<template>
    <el-dialog :model-value="modelValue" :title="form.id ? '修改职位' : '新增职位'" width="680px" top="6vh" @close="onClose" @update:model-value="(visible) => !visible && onClose()">
        <el-form ref="formRef" v-loading="loading" :model="form" :rules="rules" label-width="90px">
            <el-form-item label="职位名称" prop="job_title">
                <el-input v-model="form.job_title" />
            </el-form-item>
            <el-row :gutter="12">
                <el-col :span="12">
                    <el-form-item label="所属部门">
                        <el-input v-model="form.department" />
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="工作地点">
                        <el-input v-model="form.workplace" />
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row :gutter="12">
                <el-col :span="12">
                    <el-form-item label="经验要求">
                        <el-input v-model="form.experience" placeholder="如 3-5年" />
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="学历要求">
                        <el-input v-model="form.education" placeholder="如 本科" />
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row :gutter="12">
                <el-col :span="12">
                    <el-form-item label="薪资范围">
                        <el-input v-model="form.salary_range" placeholder="如 15-25K" />
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="排序">
                        <el-input-number v-model="form.job_sort" :min="0" style="width:100%" />
                    </el-form-item>
                </el-col>
            </el-row>
            <el-form-item label="过期时间">
                <el-date-picker
                    v-model="form.expire_at"
                    type="datetime"
                    value-format="YYYY-MM-DD HH:mm:ss"
                    placeholder="空表示长期有效"
                    style="width:100%"
                    clearable
                />
            </el-form-item>
            <el-form-item label="状态">
                <el-radio-group v-model="form.job_status">
                    <el-radio :value="1">待发布</el-radio>
                    <el-radio :value="2">发布中</el-radio>
                    <el-radio :value="3">已关闭</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="急聘">
                <el-switch v-model="form.is_hot" :active-value="1" :inactive-value="0" />
            </el-form-item>
            <el-form-item label="职位描述">
                <el-input v-model="form.description" type="textarea" :rows="4" />
            </el-form-item>
            <el-form-item label="任职要求">
                <el-input v-model="form.requirements" type="textarea" :rows="4" />
            </el-form-item>
            <el-form-item label="福利待遇">
                <el-input v-model="form.benefits" type="textarea" :rows="3" />
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
import { createJob, fetchJob, updateJob } from '../../../api/job';

const props = defineProps({
    modelValue: Boolean,
    jobId: {
        type: String,
        default: '',
    },
});
const emit = defineEmits(['update:modelValue', 'saved']);
const formRef = ref();
const saving = ref(false);
const loading = ref(false);
const form = reactive(emptyForm());
const rules = {
    job_title: [{ required: true, message: '请输入职位名称', trigger: 'blur' }],
};

watch(
    () => [props.modelValue, props.jobId],
    async () => {
        if (!props.modelValue) {
            return;
        }
        Object.assign(form, emptyForm());
        if (!props.jobId) {
            return;
        }
        loading.value = true;
        try {
            const { data } = await fetchJob(props.jobId);
            Object.assign(form, emptyForm(), data.job ?? {});
        } finally {
            loading.value = false;
        }
    },
);

function emptyForm() {
    return {
        id: '',
        job_title: '',
        department: '',
        workplace: '',
        experience: '',
        education: '',
        salary_range: '',
        description: '',
        requirements: '',
        benefits: '',
        is_hot: 0,
        job_status: 1,
        expire_at: '',
        job_sort: 0,
    };
}

function onClose() {
    emit('update:modelValue', false);
}

async function submit() {
    await formRef.value.validate();
    saving.value = true;
    try {
        const payload = { ...form, expire_at: form.expire_at || null };
        if (form.id) {
            await updateJob(form.id, payload);
            ElMessage.success('修改成功');
        } else {
            await createJob(payload);
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
