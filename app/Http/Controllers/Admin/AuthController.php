<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\AuthMenu;
use App\Services\UserAuthenticator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
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

    public function menus(): JsonResponse
    {
        $menus = AuthMenu::ordered();
        $tree = AuthMenu::buildTree($menus);

        return response()->json([
            'menus' => $this->sidebarTree($tree),
            'permissions' => $menus
                ->pluck('permission_code')
                ->filter()
                ->values(),
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $tree
     * @return array<int, array<string, mixed>>
     */
    private function sidebarTree(array $tree): array
    {
        $items = [];

        foreach ($tree as $node) {
            if (($node['menu_status'] ?? 1) !== 1) {
                continue;
            }

            if ($node['is_button'] ?? false) {
                continue;
            }

            if (isset($node['children'])) {
                $node['children'] = $this->sidebarTree($node['children']);
            }

            $items[] = $node;
        }

        return $items;
    }
}
