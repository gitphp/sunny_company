<?php

namespace App\Services;

use App\Models\ProductCategory;
use App\Support\SerialCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductCategoryService
{
    /**
     * @return array<string, mixed>
     */
    public function tree(): array
    {
        return [
            'categories' => ProductCategory::buildTree(ProductCategory::ordered()),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function create(array $data, string $operatorId): array
    {
        $category = DB::transaction(function () use ($data, $operatorId): ProductCategory {
            $parent = $this->parent($data);
            $category = new ProductCategory($this->payload($data));
            $category->category_code = SerialCode::next(ProductCategory::class, 'category_code', 'FL');
            $category->parent_id = $parent?->id ?? 0;
            $category->level = $parent ? ((int) $parent->level + 1) : 1;
            $category->created_by = $operatorId ?: null;
            $this->assertLevel($category->level);
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
    public function update(string $id, array $data, string $operatorId): array
    {
        $category = DB::transaction(function () use ($id, $data, $operatorId): ProductCategory {
            $category = ProductCategory::query()->findOrFail($id);
            $parent = $this->parent($data);

            if ($parent && $this->isSelfOrDescendant($category, (string) $parent->id)) {
                throw ValidationException::withMessages([
                    'parent_id' => ['不能选择自己或下级作为父分类'],
                ]);
            }

            $level = $parent ? ((int) $parent->level + 1) : 1;
            $this->assertLevel($level);

            $category->fill($this->payload($data));
            $category->parent_id = $parent?->id ?? 0;
            $category->level = $level;
            $category->updated_by = $operatorId ?: null;
            $category->save();
            $this->syncDescendantLevels($category);

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
    public function delete(string $id, string $operatorId): array
    {
        $category = ProductCategory::query()->findOrFail($id);

        if ($category->children()->exists()) {
            throw ValidationException::withMessages([
                'id' => ['请先删除下级分类'],
            ]);
        }

        if ($category->products()->exists()) {
            throw ValidationException::withMessages([
                'id' => ['该分类下仍有商品，无法删除'],
            ]);
        }

        $category->deleted_by = $operatorId ?: null;
        $category->save();
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
            'category_name' => (string) ($data['category_name'] ?? ''),
            'unit' => (string) ($data['unit'] ?? ''),
            'cat_status' => (int) ($data['cat_status'] ?? 1),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'cat_remark' => (string) ($data['cat_remark'] ?? ''),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function parent(array $data): ?ProductCategory
    {
        $parentId = (string) ($data['parent_id'] ?? '0');

        if ($parentId === '' || $parentId === '0') {
            return null;
        }

        return ProductCategory::query()->findOrFail($parentId);
    }

    private function assertLevel(int $level): void
    {
        if ($level > 3) {
            throw ValidationException::withMessages([
                'parent_id' => ['分类最多支持三级'],
            ]);
        }
    }

    private function isSelfOrDescendant(ProductCategory $category, string $parentId): bool
    {
        return in_array($parentId, ProductCategory::selfAndDescendantIds((string) $category->id), true);
    }

    private function syncDescendantLevels(ProductCategory $category): void
    {
        $children = ProductCategory::query()->where('parent_id', $category->id)->get();

        foreach ($children as $child) {
            $level = (int) $category->level + 1;
            $this->assertLevel($level);
            $child->level = $level;
            $child->save();
            $this->syncDescendantLevels($child);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(ProductCategory $category): array
    {
        return [
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
    }
}
