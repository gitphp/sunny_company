<?php

/**
 * 操作日志表单请求
 *
 * @package     App\Http\Requests\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;

class OperationLogRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function indexRules(): array
    {
        return [
            'operator_name' => ['nullable', 'string', 'max:50'],
            'biz_type' => ['nullable', 'string', 'max:16'],
            'action' => ['nullable', 'string', 'max:16'],
            'operator_status' => ['nullable', 'integer', 'in:0,1'],
            'begin_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:begin_time'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function batchDestroyRules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'string'],
        ];
    }
}
