<template>
    <div class="relative min-h-screen overflow-hidden">
        <div class="pointer-events-none absolute right-0 top-0 h-72 w-72 rounded-full bg-sun/20 blur-3xl"></div>
        <div class="relative mx-auto flex min-h-screen max-w-xl items-center px-4 py-10">
            <section class="w-full rounded-3xl border border-sand-dark bg-white/80 p-8 shadow-xl shadow-sand-dark/60 backdrop-blur">
                <p class="text-sm font-semibold tracking-[0.2em] text-sun-deep uppercase">Sunny Company</p>
                <h2 class="mt-3 text-2xl font-bold">创建账号</h2>
                <p class="mt-2 text-sm text-ink-soft">注册信息将写入 user_account 用户主表</p>

                <form class="mt-8 space-y-4" @submit.prevent="submit">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium">用户名</label>
                            <input v-model="form.user_name" type="text" placeholder="字母数字下划线" class="w-full rounded-2xl border border-sand-dark bg-sand/40 px-4 py-3 outline-none focus:border-sun focus:bg-white">
                            <p v-if="errors.user_name" class="mt-2 text-sm text-red-600">{{ errors.user_name }}</p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">真实姓名</label>
                            <input v-model="form.real_name" type="text" placeholder="请输入真名" class="w-full rounded-2xl border border-sand-dark bg-sand/40 px-4 py-3 outline-none focus:border-sun focus:bg-white">
                            <p v-if="errors.real_name" class="mt-2 text-sm text-red-600">{{ errors.real_name }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium">手机号</label>
                        <input v-model="form.user_mobile" type="tel" placeholder="用于登录的手机号" class="w-full rounded-2xl border border-sand-dark bg-sand/40 px-4 py-3 outline-none focus:border-sun focus:bg-white">
                        <p v-if="errors.user_mobile" class="mt-2 text-sm text-red-600">{{ errors.user_mobile }}</p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium">邮箱</label>
                        <input v-model="form.user_email" type="email" placeholder="用于找回密码" class="w-full rounded-2xl border border-sand-dark bg-sand/40 px-4 py-3 outline-none focus:border-sun focus:bg-white">
                        <p v-if="errors.user_email" class="mt-2 text-sm text-red-600">{{ errors.user_email }}</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium">密码</label>
                            <input v-model="form.password" type="password" autocomplete="new-password" class="w-full rounded-2xl border border-sand-dark bg-sand/40 px-4 py-3 outline-none focus:border-sun focus:bg-white">
                            <p v-if="errors.password" class="mt-2 text-sm text-red-600">{{ errors.password }}</p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">确认密码</label>
                            <input v-model="form.password_confirmation" type="password" autocomplete="new-password" class="w-full rounded-2xl border border-sand-dark bg-sand/40 px-4 py-3 outline-none focus:border-sun focus:bg-white">
                        </div>
                    </div>

                    <p v-if="errors.message" class="text-sm text-red-600">{{ errors.message }}</p>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full rounded-2xl bg-leaf px-4 py-3 font-semibold text-white transition hover:bg-[#245844] disabled:cursor-not-allowed disabled:opacity-70"
                    >
                        {{ loading ? '提交中...' : '注册并登录' }}
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-ink-soft">
                    已有账号？
                    <router-link to="/login" class="font-semibold text-sun-deep hover:underline">去登录</router-link>
                </p>
            </section>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../composables/useAuth';

const router = useRouter();
const auth = useAuth();
const loading = ref(false);
const form = reactive({
    user_name: '',
    real_name: '',
    user_mobile: '',
    user_email: '',
    password: '',
    password_confirmation: '',
});
const errors = reactive({
    user_name: '',
    real_name: '',
    user_mobile: '',
    user_email: '',
    password: '',
    message: '',
});

function resetErrors() {
    Object.keys(errors).forEach((key) => {
        errors[key] = '';
    });
}

async function submit() {
    resetErrors();
    loading.value = true;

    try {
        await auth.register(form);
        await router.push({ name: 'dashboard' });
    } catch (error) {
        const bag = error.response?.data?.errors ?? {};
        errors.user_name = bag.user_name?.[0] ?? '';
        errors.real_name = bag.real_name?.[0] ?? '';
        errors.user_mobile = bag.user_mobile?.[0] ?? '';
        errors.user_email = bag.user_email?.[0] ?? '';
        errors.password = bag.password?.[0] ?? '';
        errors.message = error.response?.data?.message && !Object.values(bag).length
            ? error.response.data.message
            : '';
    } finally {
        loading.value = false;
    }
}
</script>
