<?php

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
