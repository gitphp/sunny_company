import http from './http';

export function fetchSiteConfigs() {
    return http.get('/site-configs');
}

export function saveSiteConfigs(values) {
    return http.put('/site-configs', { values });
}
