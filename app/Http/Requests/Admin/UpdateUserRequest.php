<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
        $userId = (string) $this->route('user');

        return [
            'user_name' => ['required', 'string', 'min:3', 'max:32', 'regex:/^[a-zA-Z0-9_]+$/', Rule::unique('user_account', 'user_name')->ignore($userId)],
            'real_name' => ['required', 'string', 'max:16'],
            'user_mobile' => ['required', 'string', 'max:16', 'regex:/^1[3-9]\d{9}$/', Rule::unique('user_account', 'user_mobile')->ignore($userId)],
            'user_email' => ['required', 'email', 'max:128', Rule::unique('user_account', 'user_email')->ignore($userId)],
            'password' => ['nullable', Password::min(6)->max(64)],
            'user_status' => ['nullable', 'integer', 'in:0,1,2,3'],
            'real_auth_status' => ['nullable', 'integer', 'in:0,1,2,3'],
            'register_channel' => ['nullable', 'string', 'max:32'],
            'lock_reason' => ['nullable', 'string', 'max:255'],
            'lock_expire_time' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'user_name' => '用户名称',
            'real_name' => '真实姓名',
            'user_mobile' => '手机号码',
            'user_email' => '邮箱',
            'password' => '密码',
        ];
    }
}
