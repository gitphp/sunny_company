<?php

namespace App\Services;

use App\Models\AdPosition;
use App\Models\ArticleCategory;
use App\Models\AuthRole;
use App\Models\HrDepartment;
use App\Models\HrPost;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductSpecification;

class OptionService
{
    /**
     * @return array<string, mixed>
     */
    public function roles(): array
    {
        $roles = AuthRole::query()
            ->where('role_status', 1)
            ->orderByDesc('role_sort')
            ->get(['id', 'role_name', 'role_code']);

        return [
            'roles' => $roles->map(fn (AuthRole $role) => [
                'id' => (string) $role->id,
                'role_name' => $role->role_name,
                'role_code' => $role->role_code,
            ])->values(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function departments(): array
    {
        return [
            'departments' => HrDepartment::buildTree(
                HrDepartment::query()->where('dept_status', 1)->orderByDesc('dept_sort')->orderBy('id')->get()
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function posts(): array
    {
        return [
            'posts' => HrPost::buildTree(
                HrPost::query()->where('post_status', 1)->orderByDesc('post_sort')->orderBy('id')->get()
            ),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function articleCategories(array $filters = []): array
    {
        $type = isset($filters['cat_type']) && $filters['cat_type'] !== ''
            ? (int) $filters['cat_type']
            : null;

        $query = ArticleCategory::query()
            ->where('status', 1)
            ->when($type !== null, fn ($builder) => $builder->where('cat_type', $type))
            ->orderByDesc('cat_sort')
            ->orderBy('id');

        return [
            'categories' => ArticleCategory::buildTree($query->get()),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function adPositions(): array
    {
        $positions = AdPosition::query()
            ->where('status', 1)
            ->orderByDesc('id')
            ->get(['id', 'pos_name', 'pos_code', 'ad_width', 'ad_height']);

        return [
            'positions' => $positions->map(fn (AdPosition $position) => [
                'id' => (string) $position->id,
                'pos_name' => $position->pos_name,
                'pos_code' => $position->pos_code,
                'ad_width' => (int) $position->ad_width,
                'ad_height' => (int) $position->ad_height,
            ])->values(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function productBrands(): array
    {
        $brands = ProductBrand::query()
            ->where('is_show', 1)
            ->orderByDesc('sort_order')
            ->orderBy('id')
            ->get(['id', 'brand_name', 'brand_code']);

        return [
            'brands' => $brands->map(fn (ProductBrand $brand) => [
                'id' => (string) $brand->id,
                'brand_name' => $brand->brand_name,
                'brand_code' => $brand->brand_code,
            ])->values(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function productCategories(): array
    {
        return [
            'categories' => ProductCategory::buildTree(
                ProductCategory::query()->where('cat_status', 1)->orderByDesc('sort_order')->orderBy('id')->get()
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function productSpecs(): array
    {
        $specs = ProductSpecification::query()
            ->with(['values' => fn ($query) => $query->where('value_status', 1)])
            ->where('spec_status', 1)
            ->orderByDesc('sort_order')
            ->orderBy('id')
            ->get();

        return [
            'specs' => $specs->map(fn (ProductSpecification $spec) => [
                'id' => (string) $spec->id,
                'spec_name' => $spec->spec_name,
                'values' => $spec->values->map(fn ($value) => [
                    'id' => (string) $value->id,
                    'spec_id' => (string) $value->spec_id,
                    'value' => $value->value,
                ])->values(),
            ])->values(),
        ];
    }
}
