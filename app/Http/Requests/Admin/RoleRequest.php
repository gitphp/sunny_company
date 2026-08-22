<?php

/**
 * 角色表单请求
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

class RoleRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function indexRules(): array
    {
        return [
            'role_name' => ['nullable', 'string', 'max:64'],
            'role_code' => ['nullable', 'string', 'max:64'],
            'role_status' => ['nullable', 'integer', 'in:0,1'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function storeRules(): array
    {
        return [
            ...$this->roleRules(),
            'role_code' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z][a-zA-Z0-9_]*$/', Rule::unique('auth_role', 'role_code')->whereNull('deleted_at')],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function updateRules(): array
    {
        return [
            ...$this->roleRules(),
            'role_code' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z][a-zA-Z0-9_]*$/', Rule::unique('auth_role', 'role_code')->whereNull('deleted_at')->ignore($this->routeId('role'))],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function changeStatusRules(): array
    {
        return [
            'role_status' => ['required', 'integer', 'in:0,1'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function roleRules(): array
    {
        return [
            'role_name' => ['required', 'string', 'max:64'],
            'role_sort' => ['nullable', 'integer', 'min:0'],
            'data_scope' => ['required', 'integer', 'in:1,2,3,4,5'],
            'scope_departments' => ['nullable', 'array'],
            'scope_departments.*' => ['string'],
            'role_status' => ['nullable', 'integer', 'in:0,1'],
            'role_remark' => ['nullable', 'string', 'max:512'],
            'menu_ids' => ['nullable', 'array'],
            'menu_ids.*' => ['string'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'role_name' => '角色名称',
            'role_code' => '权限字符',
        ];
    }
}
