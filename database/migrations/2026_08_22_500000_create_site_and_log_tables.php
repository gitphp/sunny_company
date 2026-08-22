<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_configs', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('conf_group', 32)->default('basic')->comment('配置分组：basic, seo, contact, social');
            $table->string('conf_key', 128)->default('')->comment('配置键名');
            $table->text('conf_value')->nullable()->comment('配置值');
            $table->string('conf_desc', 255)->default('')->comment('配置说明');
            $table->string('input_type', 30)->default('text')->comment('输入类型：text, textarea, image, file, json');
            $table->unsignedInteger('conf_sort')->default(0)->comment('排序');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('conf_key', 'uk_conf_key');
            $table->index('conf_group', 'idx_conf_group');
        });

        Schema::create('operation_log', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('主键(雪花ID)');
            $table->unsignedBigInteger('operator_id')->default(0)->comment('操作人ID');
            $table->string('operator_name', 50)->default('')->comment('操作人名称');
            $table->string('biz_type', 16)->default('')->comment('业务模块类型');
            $table->string('activity_type', 32)->default('')->comment('活动类型');
            $table->string('action', 16)->default('')->comment('操作类型 INSERT/UPDATE/DELETE/LOGIN');
            $table->unsignedBigInteger('biz_id')->default(0)->comment('目标实体ID');
            $table->string('biz_label', 128)->default('')->comment('高亮展示文本');
            $table->json('old_value')->nullable()->comment('修改前的数据快照');
            $table->json('new_value')->nullable()->comment('修改后的数据快照');
            $table->tinyInteger('operator_status')->default(1)->comment('操作状态 0失败 1成功');
            $table->string('error_msg', 2048)->default('')->comment('错误信息');
            $table->string('client_ip', 32)->default('')->comment('客户端IP');
            $table->string('user_agent', 255)->default('')->comment('用户浏览器/设备信息');
            $table->string('request_url', 255)->default('')->comment('触发日志的API URL');
            $table->string('method_fun', 128)->default('')->comment('触发日志的方法名');
            $table->dateTime('created_at', 6)->nullable()->comment('发生时间');

            $table->index('operator_id', 'idx_operator_id');
            $table->index(['biz_type', 'biz_id'], 'idx_biz');
            $table->index('created_at', 'idx_created_at');
        });

        Schema::create('friend_links', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('link_name', 32)->default('')->comment('网站名称');
            $table->string('link_url', 512)->default('')->comment('网站链接');
            $table->string('link_logo', 512)->default('')->comment('网站Logo');
            $table->string('link_desc', 255)->default('')->comment('网站描述');
            $table->unsignedInteger('link_sort')->default(0)->comment('排序越小越前');
            $table->unsignedTinyInteger('link_status')->default(1)->comment('0=禁用，1=启用');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('link_status', 'idx_link_status');
        });

        Schema::create('feedbacks', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('fb_name', 32)->default('')->comment('联系人姓名');
            $table->string('fb_phone', 16)->default('')->comment('联系电话');
            $table->string('fb_email', 32)->default('')->comment('邮箱');
            $table->string('fb_company', 32)->default('')->comment('公司名称');
            $table->string('fb_title', 128)->default('')->comment('留言标题');
            $table->text('fb_content')->comment('留言内容');
            $table->unsignedTinyInteger('fb_status')->default(0)->comment('0=未处理，1=已处理');
            $table->text('reply_content')->nullable()->comment('回复内容');
            $table->dateTime('replied_at')->nullable()->comment('回复时间');
            $table->string('ip', 32)->default('')->comment('IP地址');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('fb_status', 'idx_status');
            $table->index('created_at', 'idx_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
        Schema::dropIfExists('friend_links');
        Schema::dropIfExists('operation_log');
        Schema::dropIfExists('site_configs');
    }
};
