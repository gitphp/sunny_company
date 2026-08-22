<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Admin\UserResource;
use App\Services\RbacService;
use App\Services\UserAuthenticator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(private readonly RbacService $rbac) {}

    public function login(LoginRequest $request, UserAuthenticator $authenticator): JsonResponse
    {
        $user = $authenticator->attempt(
            $request->string('account')->toString(),
            $request->string('password')->toString(),
        );

        Auth::login($user);
        $request->session()->regenerate();
        $authenticator->recordLogin($user, $request);

        return response()->json([
            'message' => '登录成功',
            'user' => UserResource::make($user->fresh()->load(['department', 'roles']))->resolve(),
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
            'user' => UserResource::make($request->user()->load(['department', 'roles']))->resolve(),
        ]);
    }

    public function menus(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'menus' => $this->rbac->sidebarMenus($user),
            'permissions' => $this->rbac->permissionCodes($user)->values(),
            'is_super' => $this->rbac->isSuperAdmin($user),
            'roles' => $this->rbac->activeRoles($user)->map(fn ($role) => [
                'id' => (string) $role->id,
                'role_name' => $role->role_name,
                'role_code' => $role->role_code,
            ])->values(),
        ]);
    }
}
