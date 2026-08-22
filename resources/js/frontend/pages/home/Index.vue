<!--
/**
 * 前台首页
 *
 * @package     Resources\Js\Frontend\Pages\Home
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <div>
        <section class="hero">
            <img v-if="currentBanner?.image_url" :src="currentBanner.image_url" :alt="currentBanner.title || '首页轮播'" />
            <div class="hero-copy">
                <p>名杨科技</p>
                <h1>{{ currentBanner?.title || '把家做成可交付的作品' }}</h1>
                <p>{{ currentBanner?.summary || home.about?.intro || '从材料、结构到空间体验，我们为住宅与公共空间提供完整的产品方案。' }}</p>
                <router-link class="btn" to="/products">进入产品中心</router-link>
            </div>
            <div v-if="banners.length > 1" class="hero-dots">
                <button
                    v-for="(item, index) in banners"
                    :key="item.id || index"
                    type="button"
                    :class="{ 'is-on': index === bannerIndex }"
                    @click="bannerIndex = index"
                />
            </div>
        </section>

        <section class="section">
            <div class="site-wrap">
                <div class="about-grid">
                    <div class="about-copy">
                        <h2>{{ home.about?.title || '关于我们' }}</h2>
                        <p>{{ home.about?.intro }}</p>
                        <router-link class="more-link" to="/about">了解更多 →</router-link>
                    </div>
                    <div class="about-panel">
                        <h3>我们关注什么</h3>
                        <p>材料来源、结构寿命，以及产品进入空间后的真实使用体验。名杨科技把工艺沉淀落实到每一件产品上。</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="section" style="padding-top: 0">
            <div class="site-wrap">
                <div class="section-head">
                    <h2>{{ home.news?.category?.cat_name || '公司新闻' }}</h2>
                    <router-link class="more-link" to="/news">查看全部</router-link>
                </div>
                <div class="news-grid">
                    <router-link
                        v-for="item in home.news?.data || []"
                        :key="item.id"
                        class="news-card"
                        :to="`/news/${item.id}`"
                    >
                        <time>{{ formatDate(item.published_at) }}</time>
                        <h3>{{ item.title }}</h3>
                        <p>{{ item.summary }}</p>
                    </router-link>
                    <p v-if="!(home.news?.data || []).length">暂无公司新闻</p>
                </div>
            </div>
        </section>

        <section class="section" style="background: var(--color-sand)">
            <div class="site-wrap">
                <div class="section-head">
                    <h2>{{ home.industry?.category?.cat_name || '行业动态' }}</h2>
                    <router-link class="more-link" to="/industry">查看全部</router-link>
                </div>
                <div class="news-grid">
                    <router-link
                        v-for="item in home.industry?.data || []"
                        :key="item.id"
                        class="news-card"
                        :to="`/industry/${item.id}`"
                    >
                        <time>{{ formatDate(item.published_at) }}</time>
                        <h3>{{ item.title }}</h3>
                        <p>{{ item.summary }}</p>
                    </router-link>
                    <p v-if="!(home.industry?.data || []).length">暂无行业动态</p>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="site-wrap">
                <div class="section-head">
                    <h2>产品中心</h2>
                    <router-link class="more-link" to="/products">全部产品</router-link>
                </div>
                <div class="product-grid">
                    <router-link
                        v-for="item in home.products || []"
                        :key="item.id"
                        class="product-card"
                        :to="`/products/${item.id}`"
                    >
                        <img v-if="imageSrc(item.main_image_url)" :src="imageSrc(item.main_image_url)" :alt="item.product_name" />
                        <div v-else class="cover-fallback"></div>
                        <div>
                            <span>{{ item.brand_name || '名杨科技' }}</span>
                            <h3>{{ item.product_name }}</h3>
                        </div>
                    </router-link>
                    <p v-if="!(home.products || []).length">暂无产品</p>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { fetchHome } from '../../api/site';

const home = ref({});
const bannerIndex = ref(0);
let timer = null;

const banners = computed(() => home.value.banners || []);
const currentBanner = computed(() => banners.value[bannerIndex.value] || null);

function formatDate(value) {
    return value ? String(value).slice(0, 10) : '';
}

function imageSrc(url) {
    return url && !String(url).startsWith('blob:') ? url : '';
}

onMounted(async () => {
    const { data } = await fetchHome();
    home.value = data;
    timer = setInterval(() => {
        if (banners.value.length > 1) {
            bannerIndex.value = (bannerIndex.value + 1) % banners.value.length;
        }
    }, 6000);
});

onUnmounted(() => {
    if (timer) {
        clearInterval(timer);
    }
});
</script>
