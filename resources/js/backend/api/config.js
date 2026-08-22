/**
 * 站点配置接口
 *
 * @package     Resources\Backend\Api
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

import http from './http';

export function fetchSiteConfigs() {
    return http.get('/site-configs');
}

export function saveSiteConfigs(values) {
    return http.put('/site-configs', { values });
}
