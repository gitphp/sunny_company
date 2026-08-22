/**
 * 岗位接口
 *
 * @package     Resources\Backend\Api
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

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
