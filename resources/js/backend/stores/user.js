/**
 * 用户状态仓库
 *
 * @package     Resources\Backend\Stores
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

import { defineStore } from 'pinia';
import { fetchMenus, fetchUser, login as loginApi, logout as logoutApi } from '../api/auth';
import { generateRoutes } from '../router/dynamic';

export const useUserStore = defineStore('user', {
    state: () => ({
        user: null,
        menus: [],
        permissions: [],
        routes: [],
        isSuper: false,
        ready: false,
    }),
    actions: {
        async bootstrap() {
            if (this.ready) {
                return;
            }

            try {
                const { data } = await fetchUser();
                this.user = data.user;
                await this.loadMenus();
            } catch {
                this.user = null;
                this.menus = [];
                this.permissions = [];
                this.routes = [];
                this.isSuper = false;
            } finally {
                this.ready = true;
            }
        },
        async loadMenus() {
            const { data } = await fetchMenus();
            this.menus = data.menus ?? [];
            this.permissions = data.permissions ?? [];
            this.isSuper = Boolean(data.is_super);
            this.routes = generateRoutes(this.menus);
        },
        async login(payload) {
            const { data } = await loginApi(payload);
            this.user = data.user;
            this.ready = true;
            await this.loadMenus();
            return data;
        },
        async logout() {
            try {
                await logoutApi();
            } finally {
                this.user = null;
                this.menus = [];
                this.permissions = [];
                this.routes = [];
                this.isSuper = false;
                this.ready = true;
            }
        },
        hasPermission(code) {
            if (!code || this.isSuper) {
                return true;
            }

            return this.permissions.includes(code);
        },
    },
});
