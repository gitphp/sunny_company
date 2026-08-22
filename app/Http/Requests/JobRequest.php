<?php

/**
 * 招聘职位表单请求
 *
 * @package     App\Http\Requests
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Requests;

class JobRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function indexRules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:64'],
            'department' => ['nullable', 'string', 'max:64'],
            'workplace' => ['nullable', 'string', 'max:128'],
            'is_hot' => ['nullable', 'integer', 'in:0,1'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function showRules(): array
    {
        return [];
    }
}
