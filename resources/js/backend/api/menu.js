import http from './http';

export function fetchAllMenus() {
    return http.get('/system/menus');
}
