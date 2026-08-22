/**
 * 用户接口
 *
 * @package     Resources\Backend\Api
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

import http from './http';

export function fetchUsers(params) {
    return http.get('/users', { params });
}

export function createUser(payload) {
    return http.post('/users', payload);
}

export function updateUser(id, payload) {
    return http.put(`/users/${id}`, payload);
}

export function deleteUser(id) {
    return http.delete(`/users/${id}`);
}

export function batchDeleteUsers(ids) {
    return http.post('/users/batch-delete', { ids });
}

export function changeUserStatus(id, user_status) {
    return http.put(`/users/${id}/status`, { user_status });
}

export function resetUserPassword(id, password) {
    return http.put(`/users/${id}/password`, { password });
}

export function exportUsers(params) {
    return http.get('/users/export', { params, responseType: 'blob' });
}
