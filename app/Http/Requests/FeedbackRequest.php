<?php

namespace App\Http\Requests;

class FeedbackRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function storeRules(): array
    {
        return [
            'fb_name' => ['required', 'string', 'max:32'],
            'fb_phone' => ['nullable', 'string', 'max:16'],
            'fb_email' => ['nullable', 'email', 'max:32'],
            'fb_company' => ['nullable', 'string', 'max:32'],
            'fb_title' => ['required', 'string', 'max:128'],
            'fb_content' => ['required', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'fb_name' => '联系人',
            'fb_title' => '留言标题',
            'fb_content' => '留言内容',
        ];
    }
}
