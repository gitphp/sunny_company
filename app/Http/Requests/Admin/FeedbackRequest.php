<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;

class FeedbackRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function indexRules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:128'],
            'fb_status' => ['nullable', 'integer', 'in:0,1'],
            'begin_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:begin_time'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function replyRules(): array
    {
        return [
            'reply_content' => ['required', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function changeStatusRules(): array
    {
        return [
            'fb_status' => ['required', 'integer', 'in:0,1'],
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
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'reply_content' => '回复内容',
        ];
    }
}
