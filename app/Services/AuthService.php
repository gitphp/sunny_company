<?php

namespace App\Services;

use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthService
{
    public function __construct(
        private readonly RbacService $rbac,
        private readonly OperationLogService $logs,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function login(array $data, Request $request): array
    {
        try {
            $user = $this->attempt((string) $data['account'], (string) $data['password']);
        } catch (ValidationException $exception) {
            $error = (string) collect($exception->errors())->flatten()->first();
            $this->writeAuthLog($request, 'LOGIN', 0, null, (string) $data['account'], $error);
            throw $exception;
        }

        Auth::login($user);
        $request->session()->regenerate();

        $user->forceFill([
            'last_login_ip' => (string) $request->ip(),
            'last_login_at' => now(),
        ])->save();

        $this->writeAuthLog($request, 'LOGIN', 1, $user, (string) $user->user_name);

        return [
            'message' => '登录成功',
            'user' => UserResource::make($user->fresh()->load(['department', 'post', 'roles']))->resolve(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function logout(Request $request): array
    {
        $user = $request->user();
        $this->writeAuthLog($request, 'LOGOUT', 1, $user, (string) ($user?->user_name ?? ''), '', 'logout');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return ['message' => '已退出登录'];
    }

    /**
     * @return array<string, mixed>
     */
    public function current(User $user): array
    {
        return [
            'user' => UserResource::make($user->load(['department', 'post', 'roles']))->resolve(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function menus(User $user): array
    {
        return [
            'menus' => $this->rbac->sidebarMenus($user),
            'permissions' => $this->rbac->permissionCodes($user)->values(),
            'is_super' => $this->rbac->isSuperAdmin($user),
            'roles' => $this->rbac->activeRoles($user)->map(fn ($role) => [
                'id' => (string) $role->id,
                'role_name' => $role->role_name,
                'role_code' => $role->role_code,
            ])->values(),
        ];
    }

    private function attempt(string $account, string $password): User
    {
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

        return $user;
    }

    private function writeAuthLog(
        Request $request,
        string $action,
        int $status,
        ?User $user,
        string $label,
        string $error = '',
        string $activity = 'login',
    ): void {
        try {
            $this->logs->write([
                'operator_id' => $user?->id ?? 0,
                'operator_name' => (string) ($user?->real_name ?: $user?->user_name ?: $label),
                'biz_type' => 'auth',
                'activity_type' => $activity,
                'action' => $action,
                'biz_id' => $user?->id ?? 0,
                'biz_label' => $label,
                'operator_status' => $status,
                'error_msg' => $error,
                'client_ip' => (string) $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'request_url' => mb_substr($request->fullUrl(), 0, 255),
                'method_fun' => (string) ($request->route()?->getActionName() ?? ''),
            ]);
        } catch (Throwable) {
            //
        }
    }
}
