<!--
/**
 * 底部导航
 *
 * @package     Resources\Js\Frontend\Layout
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <footer class="site-footer">
        <div class="site-wrap">
            <div class="footer-grid">
                <div>
                    <h3>{{ site.site_name || '名杨科技' }}</h3>
                    <p>{{ site.seo_description || '深耕家居制造，把材料、工艺与空间体验做成可交付的产品。' }}</p>
                    <p v-if="site.contact_address">{{ site.contact_address }}</p>
                    <p v-if="site.contact_phone">电话：{{ site.contact_phone }}</p>
                    <p v-if="site.contact_email">邮箱：{{ site.contact_email }}</p>
                </div>
                <div>
                    <h3>网站导航</h3>
                    <p v-for="item in nav" :key="item.path">
                        <router-link :to="item.path">{{ item.label }}</router-link>
                    </p>
                </div>
                <div>
                    <h3>友情链接</h3>
                    <p v-if="!links.length">欢迎互换友情链接</p>
                    <p v-for="link in links" :key="link.id">
                        <a :href="link.link_url" target="_blank" rel="noopener">{{ link.link_name }}</a>
                    </p>
                </div>
            </div>
            <div class="footer-copy">
                {{ site.site_copyright || 'Copyright © 名杨科技' }}
                <span v-if="site.site_icp"> · {{ site.site_icp }}</span>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { computed } from 'vue';
import { useSiteStore } from '../stores/site';

const siteStore = useSiteStore();
const site = computed(() => siteStore.site);
const nav = computed(() => siteStore.nav);
const links = computed(() => siteStore.links);
</script>
