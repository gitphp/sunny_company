<!--
/**
 * 关于我们
 *
 * @package     Resources\Js\Frontend\Pages\About
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <div>
        <section class="page-banner">
            <div class="site-wrap">
                <h1>关于我们</h1>
                <p>{{ site.seo_description || '名杨科技深耕家居制造，把材料、工艺与空间体验做成可交付的产品。' }}</p>
            </div>
        </section>
        <section class="section" style="padding-top: 0">
            <div class="site-wrap article-body">
                <p>{{ intro }}</p>
                <p v-if="site.contact_address">地址：{{ site.contact_address }}</p>
                <p v-if="site.contact_phone">电话：{{ site.contact_phone }}</p>
                <p v-if="site.contact_email">邮箱：{{ site.contact_email }}</p>
            </div>
        </section>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { fetchHome } from '../../api/site';
import { useSiteStore } from '../../stores/site';

const siteStore = useSiteStore();
const site = computed(() => siteStore.site);
const intro = ref('名杨科技持续关注材料来源、结构寿命，以及产品进入空间后的真实使用体验。');

onMounted(async () => {
    const { data } = await fetchHome();
    if (data.about?.intro) {
        intro.value = data.about.intro;
    }
});
</script>
