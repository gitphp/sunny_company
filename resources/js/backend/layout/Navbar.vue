<template>
    <header class="admin-navbar">
        <div class="admin-navbar-left">
            <el-icon class="nav-icon" @click="appStore.toggleSidebar">
                <Fold v-if="!appStore.collapsed" />
                <Expand v-else />
            </el-icon>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item :to="{ path: '/index' }">首页</el-breadcrumb-item>
                <el-breadcrumb-item v-for="item in breadcrumbs" :key="item.path">{{ item.meta?.title }}</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="admin-navbar-right">
            <el-icon class="nav-icon"><Search /></el-icon>
            <el-badge :value="3" :hidden="false">
                <el-icon class="nav-icon"><Bell /></el-icon>
            </el-badge>
            <el-icon class="nav-icon" @click="toggleFullscreen"><FullScreen /></el-icon>
            <el-dropdown trigger="click" @command="onCommand">
                <span class="el-dropdown-link" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <el-avatar :size="28" style="background:#409eff;">{{ avatarText }}</el-avatar>
                    <span>{{ userStore.user?.real_name || userStore.user?.user_name }}</span>
                    <el-icon><ArrowDown /></el-icon>
                </span>
                <template #dropdown>
                    <el-dropdown-menu>
                        <el-dropdown-item command="frontend">前台首页</el-dropdown-item>
                        <el-dropdown-item command="logout" divided>退出登录</el-dropdown-item>
                    </el-dropdown-menu>
                </template>
            </el-dropdown>
        </div>
    </header>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { ElMessageBox } from 'element-plus';
import { useAppStore } from '../stores/app';
import { useUserStore } from '../stores/user';

const route = useRoute();
const appStore = useAppStore();
const userStore = useUserStore();

const breadcrumbs = computed(() => route.matched.filter((item) => item.meta?.title && item.path !== '/index' && item.path !== '/'));
const avatarText = computed(() => (userStore.user?.real_name || userStore.user?.user_name || 'U').slice(0, 1));

function toggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
}

async function onCommand(command) {
    if (command === 'frontend') {
        window.location.href = '/';
        return;
    }

    if (command === 'logout') {
        await ElMessageBox.confirm('确定注销并退出系统吗？', '提示', { type: 'warning' });
        await userStore.logout();
        window.location.href = '/admin/login';
    }
}
</script>
