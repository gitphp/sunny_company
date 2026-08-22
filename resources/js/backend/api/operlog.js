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
