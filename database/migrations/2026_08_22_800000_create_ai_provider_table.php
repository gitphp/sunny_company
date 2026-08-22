<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_provider', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('主键ID');
            $table->string('provider_name', 64)->default('')->comment('显示名称');
            $table->string('driver', 32)->default('openai')->comment('协议驱动，openai=兼容接口');
            $table->string('base_url', 255)->default('')->comment('接口地址');
            $table->string('api_key', 255)->default('')->comment('接口密钥');
            $table->string('model', 64)->default('')->comment('模型名称');
            $table->decimal('temperature', 3, 2)->default(0.70)->comment('温度');
            $table->unsignedInteger('max_tokens')->default(2048)->comment('最大输出长度');
            $table->string('system_prompt', 2000)->default('')->comment('系统提示词');
            $table->unsignedTinyInteger('is_default')->default(0)->comment('是否默认');
            $table->unsignedTinyInteger('status')->default(1)->comment('0=禁用，1=启用');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_provider');
    }
};
