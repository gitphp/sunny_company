<?php

/**
 * 文章分类服务类
 *
 * @package     App\Services
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Services;

use App\Models\ArticleCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ArticleCategoryService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function tree(array $filters = []): array
    {
        $type = isset($filters['cat_type']) && $filters['cat_type'] !== ''
            ? (int) $filters['cat_type']
            : null;

        return [
            'categories' => ArticleCategory::buildTree(ArticleCategory::ordered($type)),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function create(array $data): array
    {
        $category = DB::transaction(function () use ($data): ArticleCategory {
            $category = new ArticleCategory($this->payload($data));
            $category->parent_id = $this->parentId($data);
            $category->save();

            return $category;
        });

        return [
            'message' => '新增成功',
            'category' => $this->transform($category),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data): array
    {
        $category = DB::transaction(function () use ($id, $data): ArticleCategory {
            $category = ArticleCategory::query()->findOrFail($id);
            $parentId = $this->parentId($data);

            if ($this->isSelfOrDescendant($category, $parentId)) {
                throw ValidationException::withMessages([
                    'parent_id' => ['不能选择自己或下级作为父分类'],
                ]);
            }

            $category->fill($this->payload($data));
            $category->parent_id = $parentId;
            $category->save();

            return $category->fresh();
        });

        return [
            'message' => '修改成功',
            'category' => $this->transform($category),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function delete(string $id): array
    {
        $category = ArticleCategory::query()->findOrFail($id);

        if ($category->children()->exists()) {
            throw ValidationException::withMessages([
                'id' => ['请先删除下级分类'],
            ]);
        }

        if ($category->articles()->exists()) {
            throw ValidationException::withMessages([
                'id' => ['该分类下仍有文章，无法删除'],
            ]);
        }

        $category->delete();

        return ['message' => '删除成功'];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data): array
    {
        return [
            'cat_type' => (int) ($data['cat_type'] ?? 0),
            'cat_name' => (string) ($data['cat_name'] ?? ''),
            'cat_url' => (string) ($data['cat_url'] ?? ''),
            'description' => (string) ($data['description'] ?? ''),
            'cat_sort' => (int) ($data['cat_sort'] ?? 0),
            'status' => (int) ($data['status'] ?? 1),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function parentId(array $data): int|string
    {
        $parentId = (string) ($data['parent_id'] ?? '0');

        if ($parentId === '' || $parentId === '0') {
            return 0;
        }

        ArticleCategory::query()->findOrFail($parentId);

        return $parentId;
    }

    private function isSelfOrDescendant(ArticleCategory $category, int|string $parentId): bool
    {
        $parentId = (string) $parentId;

        if ($parentId === '' || $parentId === '0') {
            return false;
        }

        $descendantIds = ArticleCategory::selfAndDescendantIds((string) $category->id);

        return in_array($parentId, $descendantIds, true);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(ArticleCategory $category): array
    {
        return [
            'id' => (string) $category->id,
            'parent_id' => (string) $category->parent_id,
            'cat_type' => $category->cat_type?->value,
            'cat_type_label' => $category->cat_type?->label(),
            'cat_name' => $category->cat_name,
            'cat_url' => $category->cat_url,
            'description' => $category->description,
            'cat_sort' => $category->cat_sort,
            'status' => $category->status,
        ];
    }
}
