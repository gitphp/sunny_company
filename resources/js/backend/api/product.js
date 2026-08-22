import http from './http';

export function fetchProductBrands(params) {
    return http.get('/product-brands', { params });
}

export function createProductBrand(payload) {
    return http.post('/product-brands', payload);
}

export function updateProductBrand(id, payload) {
    return http.put(`/product-brands/${id}`, payload);
}

export function deleteProductBrand(id) {
    return http.delete(`/product-brands/${id}`);
}

export function changeProductBrandStatus(id, is_show) {
    return http.put(`/product-brands/${id}/status`, { is_show });
}

export function fetchProductCategories() {
    return http.get('/product-categories');
}

export function createProductCategory(payload) {
    return http.post('/product-categories', payload);
}

export function updateProductCategory(id, payload) {
    return http.put(`/product-categories/${id}`, payload);
}

export function deleteProductCategory(id) {
    return http.delete(`/product-categories/${id}`);
}

export function fetchProductSpecs(params) {
    return http.get('/product-specs', { params });
}

export function createProductSpec(payload) {
    return http.post('/product-specs', payload);
}

export function updateProductSpec(id, payload) {
    return http.put(`/product-specs/${id}`, payload);
}

export function deleteProductSpec(id) {
    return http.delete(`/product-specs/${id}`);
}

export function changeProductSpecStatus(id, spec_status) {
    return http.put(`/product-specs/${id}/status`, { spec_status });
}

export function createProductSpecValue(specId, payload) {
    return http.post(`/product-specs/${specId}/values`, payload);
}

export function updateProductSpecValue(specId, valueId, payload) {
    return http.put(`/product-specs/${specId}/values/${valueId}`, payload);
}

export function deleteProductSpecValue(specId, valueId) {
    return http.delete(`/product-specs/${specId}/values/${valueId}`);
}

export function fetchProducts(params) {
    return http.get('/products', { params });
}

export function fetchProduct(id) {
    return http.get(`/products/${id}`);
}

export function createProduct(payload) {
    return http.post('/products', payload);
}

export function updateProduct(id, payload) {
    return http.put(`/products/${id}`, payload);
}

export function deleteProduct(id) {
    return http.delete(`/products/${id}`);
}

export function batchDeleteProducts(ids) {
    return http.post('/products/batch-delete', { ids });
}

export function changeProductStatus(id, product_status) {
    return http.put(`/products/${id}/status`, { product_status });
}
