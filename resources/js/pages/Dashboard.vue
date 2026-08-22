<template>
    <div class="min-h-screen">
        <header class="border-b border-sand-dark bg-white/70 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
                <div>
                    <p class="text-xs font-semibold tracking-[0.2em] text-sun-deep uppercase">Sunny Company</p>
                    <h1 class="text-lg font-bold">账号中心</h1>
                </div>
                <button
                    type="button"
                    class="rounded-full border border-sand-dark px-4 py-2 text-sm font-medium hover:bg-sand"
                    @click="onLogout"
                >
                    退出登录
                </button>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-10">
            <section class="rounded-3xl bg-ink px-6 py-8 text-white shadow-xl">
                <p class="text-sm text-white/70">当前登录用户</p>
                <h2 class="mt-2 text-3xl font-bold">{{ user?.real_name || user?.user_name }}</h2>
                <p class="mt-2 text-white/80">ID：{{ user?.id }}</p>
            </section>

            <section class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <article v-for="item in cards" :key="item.label" class="rounded-3xl border border-sand-dark bg-white p-5">
                    <p class="text-sm text-ink-soft">{{ item.label }}</p>
                    <p class="mt-2 text-lg font-semibold break-all">{{ item.value || '-' }}</p>
                </article>
            </section>
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../composables/useAuth';

const router = useRouter();
const auth = useAuth();
const user = computed(() => auth.user.value);

const cards = computed(() => [
    { label: '用户名', value: user.value?.user_name },
    { label: '手机号', value: user.value?.user_mobile },
    { label: '邮箱', value: user.value?.user_email },
    { label: '账号状态', value: user.value?.user_status_label },
    { label: '实名状态', value: user.value?.real_auth_status_label },
    { label: '注册渠道', value: user.value?.register_channel },
    { label: '最后登录 IP', value: user.value?.last_login_ip },
    { label: '最后登录时间', value: user.value?.last_login_at },
    { label: '创建时间', value: user.value?.created_at },
]);

async function onLogout() {
    await auth.logout();
    await router.push({ name: 'login' });
}
</script>
