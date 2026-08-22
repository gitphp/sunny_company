<?php

/**
 * 留言表单请求
 *
 * @package     App\Http\Requests
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

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
