import http from './http';

export function fetchCategories(params) {
    return http.get('/article-categories', { params });
}

export function createCategory(payload) {
    return http.post('/article-categories', payload);
}

export function updateCategory(id, payload) {
    return http.put(`/article-categories/${id}`, payload);
}

export function deleteCategory(id) {
    return http.delete(`/article-categories/${id}`);
}
