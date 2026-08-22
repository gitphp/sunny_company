<template>
    <div class="app-container">
        <el-row :gutter="16">
            <el-col :span="6">
                <el-card shadow="never">
                    <template #header>欢迎回来</template>
                    <p style="margin:0;font-size:20px;font-weight:600;">{{ userStore.user?.real_name }}</p>
                    <p style="margin:8px 0 0;color:#909399;">{{ userStore.user?.user_name }}</p>
                </el-card>
            </el-col>
            <el-col :span="6">
                <el-card shadow="never">
                    <template #header>账号状态</template>
                    <p style="margin:0;font-size:20px;font-weight:600;">{{ userStore.user?.user_status_label }}</p>
                </el-card>
            </el-col>
            <el-col :span="6">
                <el-card shadow="never">
                    <template #header>实名状态</template>
                    <p style="margin:0;font-size:20px;font-weight:600;">{{ userStore.user?.real_auth_status_label }}</p>
                </el-card>
            </el-col>
            <el-col :span="6">
                <el-card shadow="never">
                    <template #header>最后登录</template>
                    <p style="margin:0;font-size:16px;font-weight:600;">{{ userStore.user?.last_login_at || '-' }}</p>
                </el-card>
            </el-col>
        </el-row>
        <el-card shadow="never" style="margin-top:16px;">
            <template #header>系统说明</template>
            <p>后台菜单来自 <code>auth_menus</code> 表，用户数据来自 <code>user_account</code> 表。</p>
            <p>请通过左侧「系统管理 / 用户管理」维护账号。</p>
        </el-card>
    </div>
</template>

<script setup>
import { useUserStore } from '../../stores/user';

const userStore = useUserStore();
</script>
