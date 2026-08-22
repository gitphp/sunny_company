/**
 * 前台路由
 *
 * @package     Resources\Js\Frontend\Router
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

import { createRouter, createWebHistory } from 'vue-router';
import Layout from '../layout/index.vue';

const router = createRouter({
    history: createWebHistory('/'),
    scrollBehavior() {
        return { top: 0 };
    },
    routes: [
        {
            path: '/',
            component: Layout,
            children: [
                { path: '', name: 'home', component: () => import('../pages/home/Index.vue'), meta: { title: '首页' } },
                { path: 'about', name: 'about', component: () => import('../pages/about/Index.vue'), meta: { title: '关于我们' } },
                { path: 'news', name: 'news', component: () => import('../pages/article/Index.vue'), meta: { title: '公司新闻', categoryUrl: 'company-news' } },
                { path: 'news/:id', name: 'news-show', component: () => import('../pages/article/Show.vue'), meta: { title: '新闻详情' } },
                { path: 'industry', name: 'industry', component: () => import('../pages/article/Index.vue'), meta: { title: '行业动态', categoryUrl: 'industry' } },
                { path: 'industry/:id', name: 'industry-show', component: () => import('../pages/article/Show.vue'), meta: { title: '资讯详情' } },
                { path: 'products', name: 'products', component: () => import('../pages/product/Index.vue'), meta: { title: '产品中心' } },
                { path: 'products/:id', name: 'product-show', component: () => import('../pages/product/Show.vue'), meta: { title: '产品详情' } },
                { path: 'contact', name: 'contact', component: () => import('../pages/contact/Index.vue'), meta: { title: '联系我们' } },
            ],
        },
        { path: '/:pathMatch(.*)*', redirect: '/' },
    ],
});

router.afterEach((to) => {
    const title = to.meta.title ? `${to.meta.title} · 名杨科技` : '名杨科技';
    document.title = title;
});

export default router;
