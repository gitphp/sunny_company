<?php

/**
 * 后台站点配置控制器
 *
 * @package     App\Http\Controllers\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiteConfigRequest;
use App\Services\SiteConfigService;
use Illuminate\Http\JsonResponse;

class SiteConfigController extends Controller
{
    public function __construct(private readonly SiteConfigService $configs) {}

    public function index(SiteConfigRequest $request): JsonResponse
    {
        return response()->json($this->configs->grouped());
    }

    public function update(SiteConfigRequest $request): JsonResponse
    {
        return response()->json($this->configs->save($request->validated('values')));
    }
}
