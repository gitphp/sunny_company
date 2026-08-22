<!--
/**
 * 文章列表
 *
 * @package     Resources\Js\Frontend\Pages\Article
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
                <h1>{{ title }}</h1>
            </div>
        </section>
        <section class="section" style="padding-top: 0">
            <div class="site-wrap news-grid">
                <router-link
                    v-for="item in items"
                    :key="item.id"
                    class="news-card"
                    :to="`${basePath}/${item.id}`"
                >
                    <time>{{ formatDate(item.published_at) }}</time>
                    <h3>{{ item.title }}</h3>
                    <p>{{ item.summary }}</p>
                </router-link>
                <p v-if="!items.length">暂无内容</p>
            </div>
        </section>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { fetchArticles } from '../../api/site';

const route = useRoute();
const items = ref([]);
const title = computed(() => route.meta.title || '资讯');
const categoryUrl = computed(() => route.meta.categoryUrl);
const basePath = computed(() => (categoryUrl.value === 'industry' ? '/industry' : '/news'));

function formatDate(value) {
    return value ? String(value).slice(0, 10) : '';
}

async function load() {
    const { data } = await fetchArticles({ category_url: categoryUrl.value, per_page: 20 });
    items.value = data.data ?? data ?? [];
}

watch(() => route.fullPath, load, { immediate: true });
</script>
