import http from './http';

export function fetchFeedbacks(params) {
    return http.get('/feedbacks', { params });
}

export function fetchFeedback(id) {
    return http.get(`/feedbacks/${id}`);
}

export function replyFeedback(id, reply_content) {
    return http.put(`/feedbacks/${id}/reply`, { reply_content });
}

export function changeFeedbackStatus(id, fb_status) {
    return http.put(`/feedbacks/${id}/status`, { fb_status });
}

export function deleteFeedback(id) {
    return http.delete(`/feedbacks/${id}`);
}

export function batchDeleteFeedbacks(ids) {
    return http.post('/feedbacks/batch-delete', { ids });
}
