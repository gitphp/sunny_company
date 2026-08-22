/**
 * 操作日志接口
 *
 * @package     Resources\Backend\Api
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

import http from './http';

export function fetchOperationLogs(params) {
    return http.get('/operation-logs', { params });
}

export function fetchOperationLog(id) {
    return http.get(`/operation-logs/${id}`);
}

export function deleteOperationLog(id) {
    return http.delete(`/operation-logs/${id}`);
}

export function batchDeleteOperationLogs(ids) {
    return http.post('/operation-logs/batch-delete', { ids });
}
