import http from './http';

export function fetchJobs(params) {
    return http.get('/jobs', { params });
}

export function fetchJob(id) {
    return http.get(`/jobs/${id}`);
}

export function createJob(payload) {
    return http.post('/jobs', payload);
}

export function updateJob(id, payload) {
    return http.put(`/jobs/${id}`, payload);
}

export function deleteJob(id) {
    return http.delete(`/jobs/${id}`);
}

export function batchDeleteJobs(ids) {
    return http.post('/jobs/batch-delete', { ids });
}

export function changeJobStatus(id, payload) {
    return http.put(`/jobs/${id}/status`, payload);
}
