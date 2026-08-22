import { ElMessage } from 'element-plus';
import axios from 'axios';

const http = axios.create({
    baseURL: '/api/admin',
    withCredentials: true,
    xsrfCookieName: 'XSRF-TOKEN',
    xsrfHeaderName: 'X-XSRF-TOKEN',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'application/json',
    },
});

http.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            const path = window.location.pathname;
            if (!path.endsWith('/login')) {
                window.location.href = '/admin/login';
            }
        }
        if (error.response?.status === 403) {
            ElMessage.error(error.response.data?.message || '没有访问权限');
        }
        return Promise.reject(error);
    },
);

export default http;
