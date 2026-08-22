<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;

class AiRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function chatRules(): array
    {
        return [
            'provider_id' => ['nullable', 'string'],
            'stream' => ['nullable', 'boolean'],
            'messages' => ['required', 'array', 'min:1', 'max:40'],
            'messages.*.role' => ['required', 'string', 'in:user,assistant,system'],
            'messages.*.content' => ['required', 'string', 'max:8000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'provider_id' => '模型',
            'messages' => '对话内容',
        ];
    }
}
