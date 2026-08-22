import { computed, reactive } from 'vue';
import axios from 'axios';

const state = reactive({
    user: null,
    ready: false,
});

export function useAuth() {
    const user = computed(() => state.user);
    const isAuthenticated = computed(() => !!state.user);
    const ready = computed(() => state.ready);

    async function fetchUser() {
        try {
            const { data } = await axios.get('/api/user');
            state.user = data.user;
        } catch {
            state.user = null;
        } finally {
            state.ready = true;
        }
    }

    async function login(payload) {
        const { data } = await axios.post('/api/login', payload);
        state.user = data.user;
        return data;
    }

    async function register(payload) {
        const { data } = await axios.post('/api/register', payload);
        state.user = data.user;
        return data;
    }

    async function logout() {
        await axios.post('/api/logout');
        state.user = null;
    }

    return {
        user,
        isAuthenticated,
        ready,
        fetchUser,
        login,
        register,
        logout,
    };
}
