import http from './http';

export function fetchPosts() {
    return http.get('/posts');
}

export function createPost(payload) {
    return http.post('/posts', payload);
}

export function updatePost(id, payload) {
    return http.put(`/posts/${id}`, payload);
}

export function deletePost(id) {
    return http.delete(`/posts/${id}`);
}
