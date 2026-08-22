<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserAuthenticator
{
    public function attempt(string $account, string $password): User
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

    public function recordLogin(User $user, Request $request): void
    {
        $user->forceFill([
            'last_login_ip' => (string) $request->ip(),
            'last_login_at' => now(),
        ])->save();
    }
}
