<?php

namespace App\Services;

use App\Models\ProductBrand;
use App\Support\SerialCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductBrandService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function paginate(array $filters): array
    {
        $paginator = ProductBrand::query()
            ->when(($filters['brand_name'] ?? '') !== '', fn (Builder $query) => $query->where('brand_name', 'like', '%'.$filters['brand_name'].'%'))
            ->when(isset($filters['is_show']) && $filters['is_show'] !== '', fn (Builder $query) => $query->where('is_show', $filters['is_show']))
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->paginate((int) ($filters['per_page'] ?? 10));

        return [
            'data' => collect($paginator->items())->map(fn (ProductBrand $brand) => $this->transform($brand))->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function create(array $data, string $operatorId): array
    {
        $brand = DB::transaction(function () use ($data, $operatorId): ProductBrand {
            $brand = new ProductBrand($this->payload($data));
            $brand->brand_code = SerialCode::next(ProductBrand::class, 'brand_code', 'BR');
            $brand->created_by = $operatorId ?: null;
            $brand->save();

            return $brand;
        });

        return [
            'message' => '新增成功',
            'brand' => $this->transform($brand),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data, string $operatorId): array
    {
        $brand = DB::transaction(function () use ($id, $data, $operatorId): ProductBrand {
            $brand = ProductBrand::query()->findOrFail($id);
            $brand->fill($this->payload($data));
            $brand->updated_by = $operatorId ?: null;
            $brand->save();

            return $brand->fresh();
        });

        return [
            'message' => '修改成功',
            'brand' => $this->transform($brand),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function delete(string $id, string $operatorId): array
    {
        $brand = ProductBrand::query()->findOrFail($id);

        if ((int) $brand->is_system === 1) {
            throw ValidationException::withMessages([
                'id' => ['系统预设品牌不可删除'],
            ]);
        }

        if ($brand->products()->exists()) {
            throw ValidationException::withMessages([
                'id' => ['该品牌下仍有商品，无法删除'],
            ]);
        }

        $brand->deleted_by = $operatorId ?: null;
        $brand->save();
        $brand->delete();

        return ['message' => '删除成功'];
    }

    /**
     * @return array<string, mixed>
     */
    public function changeStatus(string $id, int $status, string $operatorId): array
    {
        $brand = ProductBrand::query()->findOrFail($id);
        $brand->forceFill([
            'is_show' => $status,
            'updated_by' => $operatorId ?: null,
        ])->save();

        return [
            'message' => '状态已更新',
            'brand' => $this->transform($brand->fresh()),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data): array
    {
        return [
            'brand_name' => (string) ($data['brand_name'] ?? ''),
            'alias' => (string) ($data['alias'] ?? ''),
            'is_show' => (int) ($data['is_show'] ?? 1),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'brand_remark' => (string) ($data['brand_remark'] ?? ''),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(ProductBrand $brand): array
    {
        return [
            'id' => (string) $brand->id,
            'brand_code' => $brand->brand_code,
            'brand_name' => $brand->brand_name,
            'alias' => $brand->alias,
            'is_system' => (int) $brand->is_system,
            'is_show' => (int) $brand->is_show,
            'sort_order' => (int) $brand->sort_order,
            'brand_remark' => $brand->brand_remark,
            'created_at' => $brand->created_at?->toDateTimeString(),
        ];
    }
}
