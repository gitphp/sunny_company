<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function indexRules(): array
    {
        return [
            'user_name' => ['nullable', 'string', 'max:32'],
            'user_mobile' => ['nullable', 'string', 'max:16'],
            'user_status' => ['nullable', 'integer', 'in:0,1,2,3'],
            'begin_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:begin_time'],
            'dept_id' => ['nullable', 'string'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function exportRules(): array
    {
        return $this->indexRules();
    }

    /**
     * @return array<string, mixed>
     */
    public function storeRules(): array
    {
        return [
            ...$this->profileRules(),
            'user_name' => ['required', 'string', 'min:3', 'max:32', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:user_account,user_name'],
            'user_mobile' => ['required', 'string', 'max:16', 'regex:/^1[3-9]\d{9}$/', 'unique:user_account,user_mobile'],
            'user_email' => ['required', 'email', 'max:128', 'unique:user_account,user_email'],
            'password' => ['required', Password::min(6)->max(64)],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function updateRules(): array
    {
        $userId = $this->routeId('user');

        return [
            ...$this->profileRules(),
            'user_name' => ['required', 'string', 'min:3', 'max:32', 'regex:/^[a-zA-Z0-9_]+$/', Rule::unique('user_account', 'user_name')->ignore($userId)],
            'user_mobile' => ['required', 'string', 'max:16', 'regex:/^1[3-9]\d{9}$/', Rule::unique('user_account', 'user_mobile')->ignore($userId)],
            'user_email' => ['required', 'email', 'max:128', Rule::unique('user_account', 'user_email')->ignore($userId)],
            'password' => ['nullable', Password::min(6)->max(64)],
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

    /**
     * @return array<string, mixed>
     */
    public function changeStatusRules(): array
    {
        return [
            'user_status' => ['required', 'integer', 'in:0,1'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function resetPasswordRules(): array
    {
        return [
            'password' => ['required', 'string', 'min:6', 'max:64'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function profileRules(): array
    {
        return [
            'real_name' => ['required', 'string', 'max:16'],
            'user_status' => ['nullable', 'integer', 'in:0,1,2,3'],
            'real_auth_status' => ['nullable', 'integer', 'in:0,1,2,3'],
            'register_channel' => ['nullable', 'string', 'max:32'],
            'lock_reason' => ['nullable', 'string', 'max:255'],
            'lock_expire_time' => ['nullable', 'date'],
            'dept_id' => ['nullable', 'string'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['string'],
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
