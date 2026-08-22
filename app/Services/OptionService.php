<?php

namespace App\Services;

use App\Models\AdPosition;
use App\Models\ArticleCategory;
use App\Models\AuthRole;
use App\Models\HrDepartment;
use App\Models\HrPost;

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
}
