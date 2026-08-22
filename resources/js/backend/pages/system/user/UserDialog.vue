<template>
    <el-dialog :model-value="modelValue" :title="form.id ? '修改用户' : '新增用户'" width="640px" @close="onClose" @update:model-value="(visible) => !visible && onClose()">
        <el-form ref="formRef" :model="form" :rules="rules" label-width="90px">
            <el-row :gutter="16">
                <el-col :span="12">
                    <el-form-item label="用户名称" prop="user_name">
                        <el-input v-model="form.user_name" maxlength="32" />
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="真实姓名" prop="real_name">
                        <el-input v-model="form.real_name" maxlength="16" />
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="手机号码" prop="user_mobile">
                        <el-input v-model="form.user_mobile" maxlength="16" />
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="邮箱" prop="user_email">
                        <el-input v-model="form.user_email" maxlength="128" />
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="密码" prop="password">
                        <el-input v-model="form.password" type="password" show-password :placeholder="form.id ? '不填则不修改' : '请输入密码'" />
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="归属部门">
                        <el-tree-select
                            v-model="form.dept_id"
                            :data="deptTree"
                            check-strictly
                            node-key="id"
                            :props="{ label: 'dept_name' }"
                            clearable
                            style="width:100%"
                        />
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="角色">
                        <el-select v-model="form.role_ids" multiple collapse-tags style="width:100%">
                            <el-option v-for="role in roles" :key="role.id" :label="role.role_name" :value="role.id" />
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="账号状态" prop="user_status">
                        <el-select v-model="form.user_status" style="width:100%">
                            <el-option label="禁用" :value="0" />
                            <el-option label="正常" :value="1" />
                            <el-option label="冻结" :value="2" />
                            <el-option label="注销" :value="3" />
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="实名状态" prop="real_auth_status">
                        <el-select v-model="form.real_auth_status" style="width:100%">
                            <el-option label="未实名" :value="0" />
                            <el-option label="待审核" :value="1" />
                            <el-option label="已实名" :value="2" />
                            <el-option label="实名驳回" :value="3" />
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="注册渠道">
                        <el-input v-model="form.register_channel" />
                    </el-form-item>
                </el-col>
                <el-col :span="24">
                    <el-form-item label="冻结原因">
                        <el-input v-model="form.lock_reason" type="textarea" :rows="2" />
                    </el-form-item>
                </el-col>
            </el-row>
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
import { createUser, updateUser } from '../../../api/user';
import { fetchOptionDepartments, fetchOptionRoles } from '../../../api/options';

const props = defineProps({
    modelValue: Boolean,
    user: Object,
});

const emit = defineEmits(['update:modelValue', 'saved']);
const formRef = ref();
const saving = ref(false);
const form = reactive(emptyForm());
const roles = ref([]);
const deptTree = ref([]);

const rules = {
    user_name: [{ required: true, message: '请输入用户名称', trigger: 'blur' }],
    real_name: [{ required: true, message: '请输入真实姓名', trigger: 'blur' }],
    user_mobile: [{ required: true, message: '请输入手机号码', trigger: 'blur' }],
    user_email: [{ required: true, message: '请输入邮箱', trigger: 'blur' }],
    password: [
        {
            validator: (_rule, value, callback) => {
                if (!form.id && !value) {
                    callback(new Error('请输入密码'));
                    return;
                }
                callback();
            },
            trigger: 'blur',
        },
    ],
};

watch(
    () => [props.modelValue, props.user],
    async () => {
        if (props.modelValue) {
            const [roleRes, deptRes] = await Promise.all([fetchOptionRoles(), fetchOptionDepartments()]);
            roles.value = roleRes.data.roles ?? [];
            deptTree.value = deptRes.data.departments ?? [];
        }
        Object.assign(form, props.user ? {
            id: props.user.id,
            user_name: props.user.user_name,
            real_name: props.user.real_name,
            user_mobile: props.user.user_mobile,
            user_email: props.user.user_email,
            password: '',
            user_status: props.user.user_status,
            real_auth_status: props.user.real_auth_status,
            register_channel: props.user.register_channel || 'web',
            lock_reason: props.user.lock_reason || '',
            dept_id: props.user.dept_id && props.user.dept_id !== '0' ? props.user.dept_id : '',
            role_ids: props.user.role_ids || [],
        } : emptyForm());
    },
);

function emptyForm() {
    return {
        id: '',
        user_name: '',
        real_name: '',
        user_mobile: '',
        user_email: '',
        password: '',
        user_status: 1,
        real_auth_status: 0,
        register_channel: 'web',
        lock_reason: '',
        dept_id: '',
        role_ids: [],
    };
}

function onClose() {
    emit('update:modelValue', false);
}

async function submit() {
    await formRef.value.validate();
    saving.value = true;
    try {
        const payload = { ...form };
        if (!payload.password) {
            delete payload.password;
        }
        if (form.id) {
            await updateUser(form.id, payload);
            ElMessage.success('修改成功');
        } else {
            await createUser(payload);
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
