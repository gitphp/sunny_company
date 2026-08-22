/**
 * 认证接口
 *
 * @package     Resources\Backend\Api
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

import http from './http';

export function login(payload) {
    return http.post('/login', payload);
}

export function logout() {
    return http.post('/logout');
}

export function fetchUser() {
    return http.get('/user');
}

export function fetchMenus() {
    return http.get('/menus');
}
