<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_position', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('主键ID');
            $table->string('pos_name', 100)->default('')->comment('广告位名称');
            $table->string('pos_code', 50)->default('')->comment('广告位唯一标识');
            $table->string('pos_desc', 255)->default('')->comment('广告位描述/备注');
            $table->unsignedInteger('ad_width')->default(0)->comment('建议广告宽度（像素）');
            $table->unsignedInteger('ad_height')->default(0)->comment('建议广告高度（像素）');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：0-禁用，1-正常');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('更新时间');

            $table->unique('pos_code', 'uk_pos_code');
        });

        Schema::create('ad_material', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('主键ID');
            $table->unsignedBigInteger('position_id')->default(0)->comment('关联的广告位ID');
            $table->string('title', 200)->default('')->comment('广告标题/内部备注');
            $table->string('image_url', 500)->default('')->comment('广告图片URL');
            $table->string('link_url', 1000)->default('')->comment('点击跳转链接');
            $table->string('target', 20)->default('_blank')->comment('打开方式');
            $table->integer('sort_order')->default(0)->comment('排序权重，值越小越靠前');
            $table->dateTime('start_time')->nullable()->comment('生效开始时间');
            $table->dateTime('end_time')->nullable()->comment('生效结束时间');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：0-下线，1-上线');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('更新时间');

            $table->index(['position_id', 'sort_order'], 'idx_position_sort');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_material');
        Schema::dropIfExists('ad_position');
    }
};
