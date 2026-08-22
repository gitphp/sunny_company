<!--
/**
 * 文章详情
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
                <h1>{{ article.title }}</h1>
                <p>{{ formatDate(article.published_at) }} {{ article.author_name }}</p>
            </div>
        </section>
        <article class="article-body" v-html="article.art_content"></article>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { fetchArticle } from '../../api/site';

const route = useRoute();
const article = ref({});

function formatDate(value) {
    return value ? String(value).slice(0, 10) : '';
}

async function load() {
    const { data } = await fetchArticle(route.params.id);
    article.value = data.article ?? {};
}

watch(() => route.params.id, load, { immediate: true });
</script>
