<?php

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
