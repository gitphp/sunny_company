<?php

/**
 * 岗位与文章表迁移
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
        Schema::create('hr_post', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('岗位主键ID');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父级岗位ID，0=顶级根岗位');
            $table->string('post_name', 64)->default('')->comment('岗位名称');
            $table->string('post_code', 64)->default('')->comment('岗位唯一编码');
            $table->integer('post_sort')->default(0)->comment('排序号');
            $table->tinyInteger('post_status')->default(1)->comment('状态 0=禁用 1=正常启用');
            $table->string('remark', 512)->default('')->comment('岗位描述备注');
            $table->unsignedBigInteger('created_by')->default(0)->comment('创建人用户ID');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');

            $table->unique('post_code', 'uk_post_code');
            $table->index('parent_id', 'idx_parent_id');
        });

        Schema::create('article_category', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->tinyInteger('cat_type')->default(0)->comment('分类类型 0=文章分类 1=导航分类');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父级ID，0表示顶级');
            $table->string('cat_name', 32)->default('')->comment('分类名称');
            $table->string('cat_url', 32)->default('')->comment('URL别名');
            $table->string('description', 255)->default('')->comment('分类描述');
            $table->unsignedInteger('cat_sort')->default(0)->comment('排序权重');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：0=禁用，1=启用');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deleted_at')->nullable();

            $table->index('parent_id', 'idx_parent_id');
            $table->index('cat_type', 'idx_cat_type');
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('主键ID');
            $table->string('title', 255)->default('')->comment('文章标题');
            $table->string('subtitle', 128)->default('')->comment('副标题/摘要');
            $table->string('art_cover', 500)->default('')->comment('封面图URL');
            $table->longText('art_content')->nullable()->comment('文章正文内容');
            $table->unsignedTinyInteger('content_type')->default(1)->comment('内容类型：1=富文本，2=Markdown，3=纯文本');
            $table->string('summary', 512)->default('')->comment('文章摘要');
            $table->unsignedBigInteger('category_id')->default(0)->comment('分类ID');
            $table->json('tag_ids')->nullable()->comment('标签ID列表');
            $table->unsignedBigInteger('author_id')->default(0)->comment('作者用户ID');
            $table->string('author_name', 16)->default('')->comment('作者姓名');
            $table->string('source', 64)->default('')->comment('文章来源');
            $table->string('source_url', 512)->default('')->comment('原文链接');
            $table->unsignedTinyInteger('art_status')->default(1)->comment('状态：1草稿 2待审核 3审核通过 4已发布 5已下线 6审核驳回 7回收站');
            $table->unsignedTinyInteger('is_top')->default(0)->comment('是否置顶');
            $table->unsignedTinyInteger('is_original')->default(1)->comment('是否原创');
            $table->unsignedTinyInteger('is_commentable')->default(1)->comment('是否允许评论');
            $table->string('seo_title', 255)->default('')->comment('SEO标题');
            $table->string('seo_keywords', 255)->default('')->comment('SEO关键词');
            $table->string('seo_description', 512)->default('')->comment('SEO描述');
            $table->json('extra_fields')->nullable()->comment('扩展字段');
            $table->unsignedInteger('view_count')->default(0)->comment('浏览量');
            $table->unsignedInteger('like_count')->default(0)->comment('点赞量');
            $table->unsignedInteger('collect_count')->default(0)->comment('收藏量');
            $table->unsignedInteger('share_count')->default(0)->comment('分享量');
            $table->unsignedInteger('comment_count')->default(0)->comment('评论量');
            $table->dateTime('published_at')->nullable()->comment('发布时间');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->comment('软删除时间');
            $table->unsignedBigInteger('reviewer_id')->default(0)->comment('审核人ID');
            $table->dateTime('reviewed_at')->nullable()->comment('审核时间');
            $table->string('reject_reason', 512)->nullable()->comment('驳回原因');

            $table->index('author_id', 'idx_author_id');
            $table->index('category_id', 'idx_category_id');
            $table->index('art_status', 'idx_status');
            $table->index('is_top', 'idx_is_top');
            $table->index('published_at', 'idx_published_at');
            $table->index('created_at', 'idx_created_at');
            $table->index('deleted_at', 'idx_deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
        Schema::dropIfExists('article_category');
        Schema::dropIfExists('hr_post');
    }
};
