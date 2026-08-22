<?php

namespace App\Models;

use App\Enums\RealAuthStatus;
use App\Enums\UserStatus;
use App\Support\Snowflake;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'user_name',
    'real_name',
    'user_mobile',
    'user_email',
    'password_hash',
    'password_salt',
    'user_status',
    'lock_reason',
    'lock_expire_time',
    'last_login_ip',
    'last_login_region',
    'last_login_at',
    'register_ip',
    'register_device',
    'register_channel',
    'real_auth_status',
    'dept_id',
    'post_id',
])]
#[Hidden(['password_hash', 'password_salt'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'user_account';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if (empty($user->id)) {
                $user->id = Snowflake::id();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_status' => UserStatus::class,
            'real_auth_status' => RealAuthStatus::class,
            'password_hash' => 'hashed',
            'lock_expire_time' => 'datetime',
            'last_login_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function getRememberTokenName(): string
    {
        return '';
    }

    public function setRememberToken($value): void
    {
        // user_account 表无 remember_token 字段
    }

    public function isLoginAllowed(): bool
    {
        if ($this->user_status === UserStatus::Frozen) {
            if ($this->lock_expire_time === null || $this->lock_expire_time->isFuture()) {
                return false;
            }

            $this->forceFill([
                'user_status' => UserStatus::Normal,
                'lock_reason' => '',
                'lock_expire_time' => null,
            ])->save();
        }

        return $this->user_status === UserStatus::Normal;
    }

    public function loginDeniedMessage(): string
    {
        return match ($this->user_status) {
            UserStatus::Disabled => '账号已被禁用',
            UserStatus::Frozen => $this->lock_reason !== ''
                ? "账号已冻结：{$this->lock_reason}"
                : '账号已冻结',
            UserStatus::Cancelled => '账号已注销',
            default => '账号状态异常，无法登录',
        };
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(HrDepartment::class, 'dept_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(HrPost::class, 'post_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(AuthRole::class, 'auth_user_role', 'user_id', 'role_id');
    }

    public function getEmailForPasswordReset(): string
    {
        return $this->user_email;
    }

    public function routeNotificationForMail(): string
    {
        return $this->user_email;
    }
}
