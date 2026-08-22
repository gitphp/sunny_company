<?php

/**
 * 文章表单请求
 *
 * @package     App\Http\Requests\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;

class ArticleRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function indexRules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'string'],
            'art_status' => ['nullable', 'integer', 'in:1,2,3,4,5,6,7'],
            'is_top' => ['nullable', 'integer', 'in:0,1'],
            'begin_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:begin_time'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function storeRules(): array
    {
        return $this->articleRules();
    }

    /**
     * @return array<string, mixed>
     */
    public function updateRules(): array
    {
        return $this->articleRules();
    }

    /**
     * @return array<string, mixed>
     */
    public function batchDestroyRules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function changeStatusRules(): array
    {
        return [
            'art_status' => ['nullable', 'integer', 'in:1,2,3,4,5,6,7'],
            'is_top' => ['nullable', 'integer', 'in:0,1'],
            'reject_reason' => ['nullable', 'string', 'max:512'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function articleRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:128'],
            'art_cover' => ['nullable', 'string', 'max:500'],
            'art_content' => ['nullable', 'string'],
            'content_type' => ['nullable', 'integer', 'in:1,2,3'],
            'summary' => ['nullable', 'string', 'max:512'],
            'category_id' => ['nullable', 'string'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['string'],
            'source' => ['nullable', 'string', 'max:64'],
            'source_url' => ['nullable', 'string', 'max:512'],
            'art_status' => ['nullable', 'integer', 'in:1,2,3,4,5,6,7'],
            'is_top' => ['nullable', 'integer', 'in:0,1'],
            'is_original' => ['nullable', 'integer', 'in:0,1'],
            'is_commentable' => ['nullable', 'integer', 'in:0,1'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:512'],
            'reject_reason' => ['nullable', 'string', 'max:512'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => '文章标题',
            'category_id' => '文章分类',
            'art_content' => '文章内容',
            'reject_reason' => '驳回原因',
        ];
    }
}
