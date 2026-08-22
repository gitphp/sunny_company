<?php

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
}
