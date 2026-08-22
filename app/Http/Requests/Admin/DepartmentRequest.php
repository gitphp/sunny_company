<?php

/**
 * 部门表单请求
 *
 * @package     App\Http\Requests\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function storeRules(): array
    {
        return $this->departmentRules();
    }

    /**
     * @return array<string, mixed>
     */
    public function updateRules(): array
    {
        return [
            ...$this->departmentRules(false),
            'dept_code' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z][a-zA-Z0-9_]*$/', Rule::unique('hr_department', 'dept_code')->whereNull('deleted_at')->ignore($this->routeId('department'))],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function departmentRules(bool $creating = true): array
    {
        return [
            'parent_id' => ['nullable', 'string'],
            'dept_name' => ['required', 'string', 'max:64'],
            'dept_code' => $creating
                ? ['required', 'string', 'max:64', 'regex:/^[a-zA-Z][a-zA-Z0-9_]*$/', Rule::unique('hr_department', 'dept_code')->whereNull('deleted_at')]
                : ['required', 'string', 'max:64'],
            'leader_user_id' => ['nullable'],
            'dept_phone' => ['nullable', 'string', 'max:16'],
            'dept_sort' => ['nullable', 'integer'],
            'dept_status' => ['nullable', 'integer', 'in:0,1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'dept_name' => '部门名称',
            'dept_code' => '部门编码',
        ];
    }
}
