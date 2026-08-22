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
        return Promise.reject(error);
    },
);

export default http;
