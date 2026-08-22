/**
 * 后台路由
 *
 * @package     Resources\Backend\Router
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

import { createRouter, createWebHistory } from 'vue-router';
import Layout from '../layout/index.vue';
import { useUserStore } from '../stores/user';

const router = createRouter({
    history: createWebHistory('/admin'),
    routes: [
        {
            path: '/login',
            name: 'Login',
            component: () => import('../pages/login/Index.vue'),
            meta: { title: '登录' },
        },
        {
            path: '/',
            name: 'Layout',
            component: Layout,
            redirect: '/index',
            children: [
                {
                    path: '/index',
                    name: '_index',
                    component: () => import('../pages/dashboard/Index.vue'),
                    meta: { title: '首页', affix: true },
                },
            ],
        },
    ],
});

let routesAdded = false;

router.beforeEach(async (to) => {
    const userStore = useUserStore();

    if (!userStore.ready) {
        await userStore.bootstrap();
    }

    if (!userStore.user) {
        return to.path === '/login' ? true : '/login';
    }

    if (to.path === '/login') {
        return '/index';
    }

    if (!routesAdded) {
        userStore.routes.forEach((route) => {
            if (route.path !== '/index') {
                router.addRoute('Layout', route);
            }
        });
        routesAdded = true;
        return { ...to, replace: true };
    }

    return true;
});

export function resetAdminRoutes() {
    routesAdded = false;
}

export default router;
