<!--
/**
 * 产品中心
 *
 * @package     Resources\Js\Frontend\Pages\Product
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
                <h1>产品中心</h1>
            </div>
        </section>
        <section class="section" style="padding-top: 0">
            <div class="site-wrap split-page">
                <aside>
                    <p>
                        <a href="#" @click.prevent="selectCategory('')">全部产品</a>
                    </p>
                    <p v-for="item in categories" :key="item.id">
                        <a href="#" @click.prevent="selectCategory(item.id)">{{ item.category_name }}</a>
                    </p>
                </aside>
                <div class="product-grid">
                    <router-link
                        v-for="item in products"
                        :key="item.id"
                        class="product-card"
                        :to="`/products/${item.id}`"
                    >
                        <img v-if="item.main_image_url" :src="item.main_image_url" :alt="item.product_name" />
                        <div v-else class="cover-fallback"></div>
                        <div>
                            <span>{{ item.brand_name || '名杨科技' }}</span>
                            <h3>{{ item.product_name }}</h3>
                        </div>
                    </router-link>
                    <p v-if="!products.length">暂无产品</p>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { fetchProductCategories, fetchProducts } from '../../api/site';

const categories = ref([]);
const products = ref([]);
const categoryId = ref('');

async function loadProducts() {
    const params = { per_page: 24 };
    if (categoryId.value) {
        params.category_id = categoryId.value;
    }
    const { data } = await fetchProducts(params);
    products.value = data.data ?? data ?? [];
}

function selectCategory(id) {
    categoryId.value = id;
    loadProducts();
}

onMounted(async () => {
    const { data } = await fetchProductCategories();
    categories.value = flatten(data.categories ?? []);
    await loadProducts();
});

function flatten(nodes, result = []) {
    nodes.forEach((node) => {
        result.push(node);
        if (node.children?.length) {
            flatten(node.children, result);
        }
    });
    return result;
}
</script>
