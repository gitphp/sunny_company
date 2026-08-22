/**
 * 文件上传接口
 *
 * @package     Resources\Backend\Api
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

import http from './http';

export function uploadFile(file, scene = 'products') {
    const payload = new FormData();
    payload.append('file', file);
    if (scene) {
        payload.append('scene', scene);
    }

    return http.post('/uploads', payload);
}
