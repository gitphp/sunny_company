<!--
/**
 * 后台主布局
 *
 * @package     Resources\Backend\Layout
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <div class="admin-layout">
        <aside class="admin-sidebar" :class="{ 'is-collapse': appStore.collapsed }">
            <div class="admin-logo">
                <span class="logo-mark">阳</span>
                <span v-show="!appStore.collapsed">名杨科技管理系统</span>
            </div>
            <div class="admin-menu-wrap">
                <el-menu
                    :default-active="activeMenu"
                    :collapse="appStore.collapsed"
                    background-color="#304156"
                    text-color="#bfcbd9"
                    active-text-color="#409EFF"
                    :unique-opened="true"
                    router
                >
                    <sidebar-item v-for="menu in userStore.menus" :key="menu.id" :item="menu" />
                </el-menu>
            </div>
        </aside>

        <section class="admin-main">
            <navbar />
            <tags-view />
            <main class="admin-content">
                <router-view />
            </main>
            <footer class="admin-footer">Copyright © 2026 Sunny Company All Rights Reserved.</footer>
        </section>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useAppStore } from '../stores/app';
import { useUserStore } from '../stores/user';
import Navbar from './Navbar.vue';
import SidebarItem from './SidebarItem.vue';
import TagsView from './TagsView.vue';

const route = useRoute();
const appStore = useAppStore();
const userStore = useUserStore();
const activeMenu = computed(() => route.path);
</script>
