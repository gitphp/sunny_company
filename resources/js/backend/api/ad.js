/**
 * 广告接口
 *
 * @package     Resources\Backend\Api
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

import http from './http';

export function fetchAdPositions(params) {
    return http.get('/ad-positions', { params });
}

export function createAdPosition(payload) {
    return http.post('/ad-positions', payload);
}

export function updateAdPosition(id, payload) {
    return http.put(`/ad-positions/${id}`, payload);
}

export function deleteAdPosition(id) {
    return http.delete(`/ad-positions/${id}`);
}

export function changeAdPositionStatus(id, status) {
    return http.put(`/ad-positions/${id}/status`, { status });
}

export function fetchAdMaterials(params) {
    return http.get('/ad-materials', { params });
}

export function createAdMaterial(payload) {
    return http.post('/ad-materials', payload);
}

export function updateAdMaterial(id, payload) {
    return http.put(`/ad-materials/${id}`, payload);
}

export function deleteAdMaterial(id) {
    return http.delete(`/ad-materials/${id}`);
}

export function changeAdMaterialStatus(id, status) {
    return http.put(`/ad-materials/${id}/status`, { status });
}
