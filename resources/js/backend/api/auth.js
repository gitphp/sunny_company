import http from './http';

export function login(payload) {
    return http.post('/login', payload);
}

export function logout() {
    return http.post('/logout');
}

export function fetchUser() {
    return http.get('/user');
}

export function fetchMenus() {
    return http.get('/menus');
}
