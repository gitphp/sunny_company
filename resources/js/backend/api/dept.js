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
