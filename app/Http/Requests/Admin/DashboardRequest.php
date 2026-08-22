<?php

/**
 * 仪表盘表单请求
 *
 * @package     App\Http\Requests\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;

class DashboardRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function indexRules(): array
    {
        return [];
    }
}
