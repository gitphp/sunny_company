import { defineStore } from 'pinia';
import { fetchMenus, fetchUser, login as loginApi, logout as logoutApi } from '../api/auth';
import { generateRoutes } from '../router/dynamic';

export const useUserStore = defineStore('user', {
    state: () => ({
        user: null,
        menus: [],
        permissions: [],
        routes: [],
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
            } finally {
                this.ready = true;
            }
        },
        async loadMenus() {
            const { data } = await fetchMenus();
            this.menus = data.menus ?? [];
            this.permissions = data.permissions ?? [];
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
                this.ready = true;
            }
        },
        hasPermission(code) {
            if (!code) {
                return true;
            }

            return this.permissions.includes(code);
        },
    },
});
