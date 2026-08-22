<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
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
        $deptId = (string) $this->route('department');

        return [
            'parent_id' => ['nullable', 'string'],
            'dept_name' => ['required', 'string', 'max:64'],
            'dept_code' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z][a-zA-Z0-9_]*$/', Rule::unique('hr_department', 'dept_code')->whereNull('deleted_at')->ignore($deptId)],
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
