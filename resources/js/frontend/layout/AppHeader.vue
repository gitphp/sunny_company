<!--
/**
 * 顶部菜单
 *
 * @package     Resources\Js\Frontend\Layout
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <header class="site-header">
        <div class="site-wrap site-header-inner">
            <router-link class="site-logo" to="/">
                <span class="site-logo-mark"></span>
                <span>{{ site.site_name || '名杨科技' }}</span>
            </router-link>
            <nav class="site-nav" :class="{ 'is-open': open }">
                <router-link
                    v-for="item in nav"
                    :key="item.path"
                    :to="item.path"
                    :class="{ 'is-active': isActive(item.path) }"
                    @click="open = false"
                >
                    {{ item.label }}
                </router-link>
            </nav>
            <a v-if="site.contact_phone" class="site-phone" :href="`tel:${site.contact_phone}`">
                {{ site.contact_phone }}
            </a>
            <button class="menu-btn" type="button" @click="open = !open">☰</button>
        </div>
    </header>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useRoute } from 'vue-router';
import { useSiteStore } from '../stores/site';

const route = useRoute();
const siteStore = useSiteStore();
const open = ref(false);
const site = computed(() => siteStore.site);
const nav = computed(() => siteStore.nav);

function isActive(path) {
    if (path === '/') {
        return route.path === '/';
    }

    return route.path === path || route.path.startsWith(`${path}/`);
}
</script>
