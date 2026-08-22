<!--
/**
 * 产品详情
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
                <h1>{{ product.product_name }}</h1>
                <p>{{ product.brand_name }} {{ product.product_model }}</p>
            </div>
        </section>
        <section class="section" style="padding-top: 0">
            <div class="site-wrap split-page">
                <div>
                    <img
                        v-if="cover"
                        :src="cover"
                        :alt="product.product_name"
                        style="width: 100%; border-radius: 20px"
                    />
                </div>
                <div>
                    <p>{{ product.short_desc }}</p>
                    <p v-if="product.material_quality">材质：{{ product.material_quality }}</p>
                    <p v-if="product.filling">填充：{{ product.filling }}</p>
                    <p v-if="product.min_price && product.min_price !== '0.00'">参考价：{{ product.min_price }}</p>
                    <p v-for="sku in product.skus || []" :key="sku.id">
                        {{ sku.spec_text || sku.sku_code }} · {{ sku.price }}
                    </p>
                    <router-link class="btn" to="/contact">咨询此产品</router-link>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { fetchProduct } from '../../api/site';

const route = useRoute();
const product = ref({});
const cover = computed(() => {
    const images = (product.value.media || []).filter((item) => {
        const url = item.file_url || '';
        return (item.media_type === 1 || item.media_type === 2) && url && !url.startsWith('blob:');
    });
    const fallback = product.value.main_image_url || '';
    return images[0]?.file_url || (fallback.startsWith('blob:') ? '' : fallback);
});

async function load() {
    const { data } = await fetchProduct(route.params.id);
    product.value = data.product ?? {};
}

watch(() => route.params.id, load, { immediate: true });
</script>
