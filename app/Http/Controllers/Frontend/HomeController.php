<?php

/**
 * 前台首页控制器
 *
 * @package     App\Http\Controllers\Frontend
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomeRequest;
use App\Services\HomeService;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function __construct(private readonly HomeService $home) {}

    public function index(HomeRequest $request): JsonResponse
    {
        return response()->json($this->home->index());
    }

    public function site(HomeRequest $request): JsonResponse
    {
        return response()->json($this->home->site());
    }
}
