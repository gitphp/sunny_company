<!--
/**
 * 前台布局
 *
 * @package     Resources\Js\Frontend\Layout
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <div class="site-shell">
        <AppHeader />
        <main class="site-main">
            <router-view />
        </main>
        <AppFooter />
    </div>
</template>

<script setup>
import AppHeader from './AppHeader.vue';
import AppFooter from './AppFooter.vue';
import { useSiteStore } from '../stores/site';

const siteStore = useSiteStore();
siteStore.bootstrap();
</script>
