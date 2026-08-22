/**
 * HTTP请求封装
 *
 * @package     Resources\Backend\Api
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

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
