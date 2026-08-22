<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'user_name' => ['required', 'string', 'min:3', 'max:32', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:user_account,user_name'],
            'real_name' => ['required', 'string', 'max:16'],
            'user_mobile' => ['required', 'string', 'max:16', 'regex:/^1[3-9]\d{9}$/', 'unique:user_account,user_mobile'],
            'user_email' => ['required', 'email', 'max:128', 'unique:user_account,user_email'],
            'password' => ['required', 'confirmed', Password::min(6)->max(64)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'user_name' => '用户名',
            'real_name' => '真实姓名',
            'user_mobile' => '手机号',
            'user_email' => '邮箱',
            'password' => '密码',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_name.regex' => '用户名只能包含字母、数字和下划线',
            'user_mobile.regex' => '请输入正确的手机号',
        ];
    }
}
