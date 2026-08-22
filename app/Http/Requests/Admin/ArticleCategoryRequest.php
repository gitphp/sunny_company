<?php

/**
 * 文章分类表单请求
 *
 * @package     App\Http\Requests\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;
use Illuminate\Validation\Rule;

class ArticleCategoryRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function indexRules(): array
    {
        return [
            'cat_type' => ['nullable', 'integer', 'in:0,1'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function storeRules(): array
    {
        return $this->categoryRules();
    }

    /**
     * @return array<string, mixed>
     */
    public function updateRules(): array
    {
        return $this->categoryRules($this->routeId('category'));
    }

    /**
     * @return array<string, mixed>
     */
    private function categoryRules(?string $ignoreId = null): array
    {
        return [
            'parent_id' => ['nullable', 'string'],
            'cat_type' => ['required', 'integer', 'in:0,1'],
            'cat_name' => ['required', 'string', 'max:32'],
            'cat_url' => $this->catUrlRules($ignoreId),
            'description' => ['nullable', 'string', 'max:255'],
            'cat_sort' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'integer', 'in:0,1'],
        ];
    }

    /**
     * @return list<mixed>
     */
    private function catUrlRules(?string $ignoreId): array
    {
        $rules = ['nullable', 'string', 'max:32'];

        if (! $this->filled('cat_url')) {
            return $rules;
        }

        $unique = Rule::unique('article_category', 'cat_url')->whereNull('deleted_at');

        if ($ignoreId) {
            $unique->ignore($ignoreId);
        }

        $rules[] = 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/';
        $rules[] = $unique;

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'cat_name' => '分类名称',
            'cat_url' => 'URL别名',
            'cat_type' => '分类类型',
        ];
    }
}
