<?php

/**
 * 数据库填充入口
 *
 * @package     Database\Seeders
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AuthMenuSeeder::class);

        if (! User::query()->where('user_name', 'admin')->exists()) {
            User::factory()->create([
                'id' => 934035802554576899,
                'user_name' => 'admin',
                'real_name' => '管理员',
                'user_mobile' => '13800000000',
                'user_email' => 'admin@example.com',
                'password_hash' => 'password',
                'register_channel' => 'web',
            ]);
        }

        $this->call(RbacSeeder::class);
    }
}
