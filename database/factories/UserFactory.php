<?php

namespace Database\Factories;

use App\Enums\RealAuthStatus;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_name' => fake()->unique()->userName(),
            'real_name' => fake()->name(),
            'user_mobile' => fake()->unique()->numerify('1##########'),
            'user_email' => fake()->unique()->safeEmail(),
            'password_hash' => static::$password ??= 'password',
            'password_salt' => '',
            'user_status' => UserStatus::Normal,
            'lock_reason' => '',
            'lock_expire_time' => null,
            'last_login_ip' => '',
            'last_login_region' => '',
            'last_login_at' => null,
            'register_ip' => fake()->ipv4(),
            'register_device' => 'factory',
            'register_channel' => 'web',
            'real_auth_status' => RealAuthStatus::Unverified,
            'dept_id' => 0,
            'post_id' => 0,
        ];
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_status' => UserStatus::Disabled,
        ]);
    }

    public function frozen(?string $reason = '风控冻结'): static
    {
        return $this->state(fn (array $attributes) => [
            'user_status' => UserStatus::Frozen,
            'lock_reason' => $reason ?? '',
            'lock_expire_time' => now()->addDays(7),
        ]);
    }
}
