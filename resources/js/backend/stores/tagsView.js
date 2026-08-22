import { defineStore } from 'pinia';

export const useTagsViewStore = defineStore('tagsView', {
    state: () => ({
        visited: [
            { path: '/index', title: '首页', affix: true },
        ],
    }),
    actions: {
        addView(route) {
            if (!route.path || route.path === '/login') {
                return;
            }

            if (this.visited.some((item) => item.path === route.path)) {
                return;
            }

            this.visited.push({
                path: route.path,
                title: route.meta?.title || '未命名',
                affix: Boolean(route.meta?.affix),
            });
        },
        closeView(path) {
            const index = this.visited.findIndex((item) => item.path === path && !item.affix);
            if (index > -1) {
                this.visited.splice(index, 1);
            }
        },
    },
});
