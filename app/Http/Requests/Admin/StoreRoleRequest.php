<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'role_name' => ['required', 'string', 'max:64'],
            'role_code' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z][a-zA-Z0-9_]*$/', Rule::unique('auth_role', 'role_code')->whereNull('deleted_at')],
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
