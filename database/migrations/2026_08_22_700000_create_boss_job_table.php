<?php

/**
 * 招聘职位表迁移
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
    public function up(): void
    {
        Schema::create('boss_job', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('主键ID');
            $table->string('job_title', 64)->default('')->comment('职位名称');
            $table->string('department', 64)->default('')->comment('所属部门');
            $table->string('workplace', 128)->default('')->comment('工作地点');
            $table->string('experience', 64)->default('')->comment('经验要求');
            $table->string('education', 64)->default('')->comment('学历要求');
            $table->string('salary_range', 64)->default('')->comment('薪资范围');
            $table->text('description')->nullable()->comment('职位描述');
            $table->text('requirements')->nullable()->comment('任职要求');
            $table->text('benefits')->nullable()->comment('福利待遇');
            $table->unsignedTinyInteger('is_hot')->default(0)->comment('是否急聘');
            $table->unsignedTinyInteger('job_status')->default(1)->comment('1=待发布，2=发布中，3=已关闭');
            $table->dateTime('expire_at')->nullable()->comment('过期时间');
            $table->unsignedInteger('view_count')->default(0)->comment('浏览量');
            $table->unsignedInteger('job_sort')->default(0)->comment('排序权重');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');

            $table->index(['job_status', 'is_hot', 'job_sort'], 'idx_status_hot_sort');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boss_job');
    }
};
