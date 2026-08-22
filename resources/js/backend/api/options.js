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
