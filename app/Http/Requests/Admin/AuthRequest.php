<?php

/**
 * 认证表单请求
 *
 * @package     App\Http\Requests\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;

class AuthRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function loginRules(): array
    {
        return [
            'account' => ['required', 'string', 'max:128'],
            'password' => ['required', 'string', 'min:6', 'max:64'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'account' => '账号',
            'password' => '密码',
        ];
    }
}
