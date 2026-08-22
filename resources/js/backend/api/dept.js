/**
 * 部门接口
 *
 * @package     Resources\Backend\Api
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

import http from './http';

export function fetchDepartments() {
    return http.get('/departments');
}

export function createDepartment(payload) {
    return http.post('/departments', payload);
}

export function updateDepartment(id, payload) {
    return http.put(`/departments/${id}`, payload);
}

export function deleteDepartment(id) {
    return http.delete(`/departments/${id}`);
}
