import http from './http';

export function fetchArticles(params) {
    return http.get('/articles', { params });
}

export function fetchArticle(id) {
    return http.get(`/articles/${id}`);
}

export function createArticle(payload) {
    return http.post('/articles', payload);
}

export function updateArticle(id, payload) {
    return http.put(`/articles/${id}`, payload);
}

export function deleteArticle(id) {
    return http.delete(`/articles/${id}`);
}

export function batchDeleteArticles(ids) {
    return http.post('/articles/batch-delete', { ids });
}

export function changeArticleStatus(id, payload) {
    return http.put(`/articles/${id}/status`, payload);
}
