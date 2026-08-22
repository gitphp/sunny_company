/**
 * 角色接口
 *
 * @package     Resources\Backend\Api
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

import http from './http';

export function fetchRoles(params) {
    return http.get('/roles', { params });
}

export function fetchRole(id) {
    return http.get(`/roles/${id}`);
}

export function createRole(payload) {
    return http.post('/roles', payload);
}

export function updateRole(id, payload) {
    return http.put(`/roles/${id}`, payload);
}

export function deleteRole(id) {
    return http.delete(`/roles/${id}`);
}

export function changeRoleStatus(id, role_status) {
    return http.put(`/roles/${id}/status`, { role_status });
}
