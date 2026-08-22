<template>
    <div class="relative min-h-screen overflow-hidden">
        <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-sun/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -right-16 h-80 w-80 rounded-full bg-leaf/15 blur-3xl"></div>

        <div class="relative mx-auto flex min-h-screen max-w-6xl items-center px-4 py-10">
            <div class="grid w-full gap-10 lg:grid-cols-2 lg:items-center">
                <section class="hidden lg:block">
                    <p class="mb-3 text-sm font-semibold tracking-[0.2em] text-sun-deep uppercase">Sunny Company</p>
                    <h1 class="text-5xl font-bold leading-tight text-ink">把阳光放进每一次登录</h1>
                    <p class="mt-5 max-w-md text-lg leading-8 text-ink-soft">
                        支持用户名、手机号、邮箱登录。账号状态、实名信息与登录记录都会写入
                        <span class="font-semibold text-ink">user_account</span> 用户主表。
                    </p>
                </section>

                <section class="mx-auto w-full max-w-md rounded-3xl border border-sand-dark bg-white/80 p-8 shadow-xl shadow-sand-dark/60 backdrop-blur">
                    <h2 class="text-2xl font-bold">欢迎回来</h2>
                    <p class="mt-2 text-sm text-ink-soft">使用已注册的账号登录系统</p>

                    <form class="mt-8 space-y-5" @submit.prevent="submit">
                        <div>
                            <label class="mb-2 block text-sm font-medium">账号</label>
                            <input
                                v-model="form.account"
                                type="text"
                                autocomplete="username"
                                placeholder="用户名 / 手机号 / 邮箱"
                                class="w-full rounded-2xl border border-sand-dark bg-sand/40 px-4 py-3 outline-none transition focus:border-sun focus:bg-white"
                            >
                            <p v-if="errors.account" class="mt-2 text-sm text-red-600">{{ errors.account }}</p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium">密码</label>
                            <input
                                v-model="form.password"
                                type="password"
                                autocomplete="current-password"
                                placeholder="请输入密码"
                                class="w-full rounded-2xl border border-sand-dark bg-sand/40 px-4 py-3 outline-none transition focus:border-sun focus:bg-white"
                            >
                            <p v-if="errors.password" class="mt-2 text-sm text-red-600">{{ errors.password }}</p>
                        </div>

                        <p v-if="errors.message" class="text-sm text-red-600">{{ errors.message }}</p>

                        <button
                            type="submit"
                            :disabled="loading"
                            class="w-full rounded-2xl bg-sun px-4 py-3 font-semibold text-white transition hover:bg-sun-deep disabled:cursor-not-allowed disabled:opacity-70"
                        >
                            {{ loading ? '登录中...' : '登录' }}
                        </button>
                    </form>

                    <p class="mt-6 text-center text-sm text-ink-soft">
                        还没有账号？
                        <router-link to="/register" class="font-semibold text-leaf hover:underline">立即注册</router-link>
                    </p>
                </section>
            </div>
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
    account: '',
    password: '',
});
const errors = reactive({
    account: '',
    password: '',
    message: '',
});

function resetErrors() {
    errors.account = '';
    errors.password = '';
    errors.message = '';
}

async function submit() {
    resetErrors();
    loading.value = true;

    try {
        await auth.login(form);
        await router.push({ name: 'dashboard' });
    } catch (error) {
        const bag = error.response?.data?.errors ?? {};
        errors.account = bag.account?.[0] ?? '';
        errors.password = bag.password?.[0] ?? '';
        errors.message = error.response?.data?.message && !errors.account && !errors.password
            ? error.response.data.message
            : '';
    } finally {
        loading.value = false;
    }
}
</script>
