<?php

/**
 * 用户岗位字段迁移
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
        if (Schema::hasColumn('user_account', 'post_id')) {
            return;
        }

        Schema::table('user_account', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id')->default(0)->after('dept_id')->comment('所属岗位ID');
            $table->index('post_id', 'idx_post_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('user_account', 'post_id')) {
            return;
        }

        Schema::table('user_account', function (Blueprint $table) {
            $table->dropIndex('idx_post_id');
            $table->dropColumn('post_id');
        });
    }
};
