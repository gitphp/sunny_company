<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::query()->create([
            'user_name' => $request->string('user_name')->toString(),
            'real_name' => $request->string('real_name')->toString(),
            'user_mobile' => $request->string('user_mobile')->toString(),
            'user_email' => $request->string('user_email')->toString(),
            'password_hash' => $request->string('password')->toString(),
            'password_salt' => '',
            'register_ip' => (string) $request->ip(),
            'register_device' => mb_substr((string) $request->userAgent(), 0, 128),
            'register_channel' => 'web',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'message' => '注册成功',
            'user' => UserResource::make($user)->resolve(),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $account = $request->string('account')->toString();
        $password = $request->string('password')->toString();

        $user = User::query()
            ->where(function ($query) use ($account): void {
                $query->where('user_name', $account)
                    ->orWhere('user_mobile', $account)
                    ->orWhere('user_email', $account);
            })
            ->first();

        if (! $user || ! Hash::check($password, $user->password_hash)) {
            throw ValidationException::withMessages([
                'account' => ['账号或密码错误'],
            ]);
        }

        if (! $user->isLoginAllowed()) {
            throw ValidationException::withMessages([
                'account' => [$user->loginDeniedMessage()],
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $user->forceFill([
            'last_login_ip' => (string) $request->ip(),
            'last_login_at' => now(),
        ])->save();

        return response()->json([
            'message' => '登录成功',
            'user' => UserResource::make($user->fresh())->resolve(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => '已退出登录',
        ]);
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => UserResource::make($request->user())->resolve(),
        ]);
    }
}
