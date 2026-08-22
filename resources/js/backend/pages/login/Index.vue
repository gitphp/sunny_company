<template>
    <div class="login-page">
        <div class="login-card">
            <h1>阳光管理系统</h1>
            <p>Sunny Company Admin</p>
            <el-form ref="formRef" :model="form" :rules="rules" @keyup.enter="submit">
                <el-form-item prop="account">
                    <el-input v-model="form.account" size="large" placeholder="用户名 / 手机号 / 邮箱">
                        <template #prefix><el-icon><User /></el-icon></template>
                    </el-input>
                </el-form-item>
                <el-form-item prop="password">
                    <el-input v-model="form.password" size="large" type="password" show-password placeholder="密码">
                        <template #prefix><el-icon><Lock /></el-icon></template>
                    </el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" size="large" style="width:100%" :loading="loading" @click="submit">登 录</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';
import { useUserStore } from '../../stores/user';

const router = useRouter();
const userStore = useUserStore();
const formRef = ref();
const loading = ref(false);
const form = reactive({
    account: 'admin',
    password: 'password',
});
const rules = {
    account: [{ required: true, message: '请输入账号', trigger: 'blur' }],
    password: [{ required: true, message: '请输入密码', trigger: 'blur' }],
};

async function submit() {
    await formRef.value.validate();
    loading.value = true;
    try {
        await userStore.login(form);
        ElMessage.success('登录成功');
        await router.push('/index');
    } catch (error) {
        const bag = error.response?.data?.errors ?? {};
        ElMessage.error(bag.account?.[0] || error.response?.data?.message || '登录失败');
    } finally {
        loading.value = false;
    }
}
</script>
