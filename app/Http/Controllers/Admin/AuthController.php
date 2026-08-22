<?php

/**
 * 后台认证控制器
 *
 * @package     App\Http\Controllers\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AuthRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $auth) {}

    public function login(AuthRequest $request): JsonResponse
    {
        return response()->json($this->auth->login($request->validated(), $request));
    }

    public function logout(AuthRequest $request): JsonResponse
    {
        return response()->json($this->auth->logout($request));
    }

    public function user(AuthRequest $request): JsonResponse
    {
        return response()->json($this->auth->current($request->user()));
    }

    public function menus(AuthRequest $request): JsonResponse
    {
        return response()->json($this->auth->menus($request->user()));
    }
}
