import http from './http';

export function fetchPermissions() {
    return http.get('/permissions');
}

export function fetchOptionRoles() {
    return http.get('/options/roles');
}

export function fetchOptionDepartments() {
    return http.get('/options/departments');
}

export function fetchOptionMenus() {
    return http.get('/options/menus');
}

export function fetchOptionPosts() {
    return http.get('/options/posts');
}

export function fetchOptionArticleCategories(params) {
    return http.get('/options/article-categories', { params });
}

export function fetchOptionAdPositions() {
    return http.get('/options/ad-positions');
}

export function fetchOptionProductBrands() {
    return http.get('/options/product-brands');
}

export function fetchOptionProductCategories() {
    return http.get('/options/product-categories');
}

export function fetchOptionProductSpecs() {
    return http.get('/options/product-specs');
}
