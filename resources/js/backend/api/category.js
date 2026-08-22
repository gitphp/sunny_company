/**
 * 文章分类接口
 *
 * @package     Resources\Backend\Api
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

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
