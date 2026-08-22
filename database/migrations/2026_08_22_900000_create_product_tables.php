<?php

/**
 * 商品相关表迁移
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
        Schema::create('product_brand', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('主键(雪花ID)');
            $table->string('brand_code', 36)->default('')->comment('系统产生编码BR000001');
            $table->string('brand_name', 32)->default('')->comment('品牌名称');
            $table->string('alias', 64)->default('')->comment('英文别名');
            $table->unsignedTinyInteger('is_system')->default(0)->comment('是否系统预设');
            $table->unsignedTinyInteger('is_show')->default(1)->comment('状态 0=隐藏 1=显示');
            $table->unsignedInteger('sort_order')->default(0)->comment('排序');
            $table->string('brand_remark', 512)->default('')->comment('备注');
            $table->dateTime('created_at', 6)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->dateTime('deleted_at', 6)->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->unique('brand_code', 'uk_brand_code');
        });

        Schema::create('product_category', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('主键(雪花ID)');
            $table->string('category_code', 16)->default('')->comment('系统产生编码FL000001');
            $table->string('category_name', 255)->default('')->comment('分类名称');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父级分类ID');
            $table->unsignedTinyInteger('level')->default(1)->comment('级别');
            $table->unsignedInteger('product_count')->default(0)->comment('商品数量');
            $table->string('unit', 32)->default('')->comment('数量单位');
            $table->unsignedTinyInteger('cat_status')->default(1)->comment('状态 0=隐藏 1=显示');
            $table->unsignedInteger('sort_order')->default(0)->comment('排序');
            $table->string('cat_remark', 512)->default('')->comment('备注');
            $table->dateTime('created_at', 6)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->dateTime('deleted_at', 6)->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->unique('category_code', 'uk_category_code');
            $table->index('parent_id', 'category_parent_id_index');
        });

        Schema::create('product_specification', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('主键(雪花ID)');
            $table->string('spec_code', 36)->default('')->comment('系统产生编码GL000001');
            $table->string('spec_name', 255)->default('')->comment('规格名称');
            $table->string('spec_remark', 512)->default('')->comment('备注');
            $table->unsignedTinyInteger('spec_status')->default(1)->comment('状态');
            $table->unsignedInteger('sort_order')->default(0)->comment('排序');
            $table->dateTime('created_at', 6)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->dateTime('deleted_at', 6)->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->unique('spec_code', 'uk_spec_code');
        });

        Schema::create('product_specification_value', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('主键(雪花ID)');
            $table->unsignedBigInteger('spec_id')->default(0)->comment('规格ID');
            $table->string('value_code', 36)->default('')->comment('系统产生编码GV000001');
            $table->string('value', 255)->default('')->comment('规格值');
            $table->unsignedInteger('sort_order')->default(0)->comment('排序');
            $table->unsignedTinyInteger('value_status')->default(1)->comment('状态');
            $table->dateTime('created_at', 6)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->dateTime('deleted_at', 6)->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->unique('value_code', 'uk_value_code');
            $table->index('spec_id', 'spec_value_spec_id_index');
        });

        Schema::create('product', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('主键(雪花ID)');
            $table->string('auto_code', 36)->default('')->comment('系统产生编码SP000001');
            $table->string('product_name', 64)->default('')->comment('商品名称');
            $table->string('product_model', 128)->default('')->comment('商品型号');
            $table->unsignedBigInteger('category_id')->default(0)->comment('商品分类ID');
            $table->unsignedBigInteger('brand_id')->default(0)->comment('品牌ID');
            $table->string('material_quality', 128)->default('')->comment('材质');
            $table->string('filling', 128)->default('')->comment('填充');
            $table->text('short_desc')->nullable()->comment('商品简短描述');
            $table->string('main_image_url', 512)->default('')->comment('主图URL');
            $table->unsignedTinyInteger('product_status')->default(1)->comment('状态 0=已下架 1=已上架');
            $table->unsignedInteger('sort_order')->default(0)->comment('排序');
            $table->dateTime('created_at', 6)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->dateTime('deleted_at', 6)->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->unique('auto_code', 'uk_product_auto_code');
            $table->index('category_id', 'product_category_id_index');
            $table->index('brand_id', 'product_brand_id_index');
        });

        Schema::create('product_sku', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('主键(雪花ID)');
            $table->unsignedBigInteger('product_id')->default(0)->comment('商品ID');
            $table->string('sku_code', 16)->default('')->comment('SKU编码');
            $table->decimal('price', 10, 2)->unsigned()->default(0)->comment('销售价');
            $table->decimal('market_price', 10, 2)->unsigned()->default(0)->comment('划线价');
            $table->decimal('cost_price', 10, 2)->unsigned()->default(0)->comment('成本价');
            $table->unsignedInteger('stock_num')->default(0)->comment('库存数量');
            $table->decimal('weight', 10, 2)->unsigned()->default(0)->comment('重量(KG)');
            $table->decimal('volume', 10, 4)->unsigned()->default(0)->comment('体积(m³)');
            $table->unsignedTinyInteger('sale_status')->default(1)->comment('销售状态');
            $table->unsignedInteger('sort_order')->default(0)->comment('排序');
            $table->dateTime('created_at', 6)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->dateTime('deleted_at', 6)->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->unique('sku_code', 'sku_code_unique');
            $table->index('product_id', 'sku_product_id_index');
        });

        Schema::create('product_sku_spec_value', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('主键(雪花ID)');
            $table->unsignedBigInteger('sku_id')->default(0)->comment('关联SKU表ID');
            $table->unsignedBigInteger('spec_id')->default(0)->comment('关联规格维度ID');
            $table->unsignedBigInteger('spec_value_id')->default(0)->comment('关联规格值ID');
            $table->dateTime('created_at', 6)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->dateTime('deleted_at', 6)->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->unique(['sku_id', 'spec_id', 'spec_value_id'], 'sku_spec_value_unique');
            $table->index('sku_id', 'sku_spec_sku_id_index');
            $table->index('spec_value_id', 'sku_spec_value_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sku_spec_value');
        Schema::dropIfExists('product_sku');
        Schema::dropIfExists('product');
        Schema::dropIfExists('product_specification_value');
        Schema::dropIfExists('product_specification');
        Schema::dropIfExists('product_category');
        Schema::dropIfExists('product_brand');
    }
};
