/**
 * 前台接口
 *
 * @package     Resources\Js\Frontend\Api
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

import http from './http';

export function fetchSite() {
    return http.get('/site');
}

export function fetchHome() {
    return http.get('/home');
}

export function fetchArticles(params) {
    return http.get('/articles', { params });
}

export function fetchArticle(id) {
    return http.get(`/articles/${id}`);
}

export function fetchProducts(params) {
    return http.get('/products', { params });
}

export function fetchProduct(id) {
    return http.get(`/products/${id}`);
}

export function fetchProductCategories() {
    return http.get('/product-categories');
}

export function submitFeedback(payload) {
    return http.post('/feedbacks', payload);
}
