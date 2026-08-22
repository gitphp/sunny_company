import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '../composables/useAuth';
import Login from '../pages/Login.vue';
import Register from '../pages/Register.vue';
import Dashboard from '../pages/Dashboard.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', redirect: '/dashboard' },
        { path: '/login', name: 'login', component: Login, meta: { guest: true } },
        { path: '/register', name: 'register', component: Register, meta: { guest: true } },
        { path: '/dashboard', name: 'dashboard', component: Dashboard, meta: { auth: true } },
    ],
});

router.beforeEach(async (to) => {
    const auth = useAuth();

    if (!auth.ready.value) {
        await auth.fetchUser();
    }

    if (to.meta.auth && !auth.user.value) {
        return { name: 'login' };
    }

    if (to.meta.guest && auth.user.value) {
        return { name: 'dashboard' };
    }

    return true;
});

export default router;
