/**
 * 友情链接接口
 *
 * @package     Resources\Backend\Api
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

import http from './http';

export function fetchFriendLinks(params) {
    return http.get('/friend-links', { params });
}

export function createFriendLink(payload) {
    return http.post('/friend-links', payload);
}

export function updateFriendLink(id, payload) {
    return http.put(`/friend-links/${id}`, payload);
}

export function deleteFriendLink(id) {
    return http.delete(`/friend-links/${id}`);
}

export function changeFriendLinkStatus(id, link_status) {
    return http.put(`/friend-links/${id}/status`, { link_status });
}
