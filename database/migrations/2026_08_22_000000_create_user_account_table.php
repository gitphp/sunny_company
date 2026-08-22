<?php

/**
 * 用户表迁移
 *
 * @package     Database\Migrations
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('users');

        if (Schema::hasTable('user_account')) {
            return;
        }

        Schema::create('user_account', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('用户唯一主键ID（雪花ID，不自增，分布式安全）');
            $table->string('user_name', 32)->default('')->comment('账号用户名，唯一，可用于登录');
            $table->string('real_name', 16)->default('')->comment('真名');
            $table->string('user_mobile', 16)->default('')->comment('手机号，唯一索引，登录首选');
            $table->string('user_email', 128)->default('')->comment('邮箱，唯一索引，找回密码');
            $table->string('password_hash', 128)->default('')->comment('BCrypt/Argon2加密密码，禁止明文存储');
            $table->string('password_salt', 64)->default('')->comment('自定义盐值（BCrypt自带盐可留空）');
            $table->tinyInteger('user_status')->default(1)->comment('账号状态：0-禁用 1-正常 2-冻结 3-注销');
            $table->string('lock_reason', 255)->default('')->comment('冻结/封禁原因（风控、违规、人工封禁）');
            $table->dateTime('lock_expire_time')->nullable()->comment('限时冻结到期时间，NULL=永久封禁');
            $table->string('last_login_ip', 64)->default('')->comment('最后登录IP');
            $table->string('last_login_region', 64)->default('')->comment('IP归属地');
            $table->dateTime('last_login_at')->nullable()->comment('最后登录时间');
            $table->string('register_ip', 64)->default('')->comment('注册IP');
            $table->string('register_device', 128)->default('')->comment('注册设备标识');
            $table->string('register_channel', 32)->default('web')->comment('注册渠道：web/app/mini/ios/android');
            $table->tinyInteger('real_auth_status')->default(0)->comment('实名状态：0未实名 1待审核 2已实名 3实名驳回');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除记录）');

            $table->unique('user_name', 'uk_username');
            $table->unique('user_mobile', 'uk_mobile');
            $table->unique('user_email', 'uk_email');
            $table->index(['user_status', 'real_auth_status'], 'idx_status_auth');
            $table->index('created_at', 'idx_deleted_time');
            $table->comment('用户账号表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_account');
    }
};
