<?php

/**
 * 商品分类模型
 *
 * @package     App\Models
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Models;

use App\Support\Snowflake;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

#[Fillable([
    'category_code',
    'category_name',
    'parent_id',
    'level',
    'product_count',
    'unit',
    'cat_status',
    'sort_order',
    'cat_remark',
    'created_by',
    'updated_by',
    'deleted_by',
])]
class ProductCategory extends Model
{
    use SoftDeletes;

    protected $table = 'product_category';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (ProductCategory $category): void {
            if (empty($category->id)) {
                $category->id = Snowflake::id();
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderByDesc('sort_order')->orderBy('id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    /**
     * @return Collection<int, ProductCategory>
     */
    public static function ordered(): Collection
    {
        return static::query()->orderByDesc('sort_order')->orderBy('id')->get();
    }

    /**
     * @return list<string>
     */
    public static function selfAndDescendantIds(string $categoryId): array
    {
        $ids = [$categoryId];
        $pending = [$categoryId];

        while ($pending !== []) {
            $children = static::query()
                ->whereIn('parent_id', $pending)
                ->pluck('id')
                ->map(fn ($id): string => (string) $id)
                ->all();

            $pending = [];

            foreach ($children as $childId) {
                if (in_array($childId, $ids, true)) {
                    continue;
                }

                $ids[] = $childId;
                $pending[] = $childId;
            }
        }

        return $ids;
    }

    /**
     * @param  Collection<int, ProductCategory>  $categories
     * @return array<int, array<string, mixed>>
     */
    public static function buildTree(Collection $categories, string $parentId = '0'): array
    {
        $branch = [];

        foreach ($categories as $category) {
            if ((string) $category->parent_id !== $parentId) {
                continue;
            }

            $item = [
                'id' => (string) $category->id,
                'parent_id' => (string) $category->parent_id,
                'category_code' => $category->category_code,
                'category_name' => $category->category_name,
                'level' => (int) $category->level,
                'product_count' => (int) $category->product_count,
                'unit' => $category->unit,
                'cat_status' => (int) $category->cat_status,
                'sort_order' => (int) $category->sort_order,
                'cat_remark' => $category->cat_remark,
            ];

            $children = self::buildTree($categories, (string) $category->id);

            if ($children !== []) {
                $item['children'] = $children;
            }

            $branch[] = $item;
        }

        return $branch;
    }
}
