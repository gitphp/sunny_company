<?php

/**
 * 广告表单请求
 *
 * @package     App\Http\Requests
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Requests;

class AdRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function showRules(): array
    {
        return [];
    }
}
