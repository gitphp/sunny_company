<?php

namespace App\Services;

use App\Models\ProductSkuSpecValue;
use App\Models\ProductSpecification;
use App\Models\ProductSpecificationValue;
use App\Support\SerialCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductSpecificationService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function paginate(array $filters): array
    {
        $paginator = ProductSpecification::query()
            ->with(['values'])
            ->when(($filters['spec_name'] ?? '') !== '', fn (Builder $query) => $query->where('spec_name', 'like', '%'.$filters['spec_name'].'%'))
            ->when(isset($filters['spec_status']) && $filters['spec_status'] !== '', fn (Builder $query) => $query->where('spec_status', $filters['spec_status']))
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->paginate((int) ($filters['per_page'] ?? 10));

        return [
            'data' => collect($paginator->items())->map(fn (ProductSpecification $spec) => $this->transform($spec, true))->values(),
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
        $spec = DB::transaction(function () use ($data, $operatorId): ProductSpecification {
            $spec = new ProductSpecification($this->specPayload($data));
            $spec->spec_code = SerialCode::next(ProductSpecification::class, 'spec_code', 'GL');
            $spec->created_by = $operatorId ?: null;
            $spec->save();

            foreach ($data['values'] ?? [] as $item) {
                $this->makeValue($spec, $item, $operatorId);
            }

            return $spec->load('values');
        });

        return [
            'message' => '新增成功',
            'spec' => $this->transform($spec, true),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data, string $operatorId): array
    {
        $spec = DB::transaction(function () use ($id, $data, $operatorId): ProductSpecification {
            $spec = ProductSpecification::query()->findOrFail($id);
            $spec->fill($this->specPayload($data));
            $spec->updated_by = $operatorId ?: null;
            $spec->save();

            return $spec->fresh()->load('values');
        });

        return [
            'message' => '修改成功',
            'spec' => $this->transform($spec, true),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function delete(string $id, string $operatorId): array
    {
        $spec = ProductSpecification::query()->findOrFail($id);

        if (ProductSkuSpecValue::query()->where('spec_id', $id)->exists()) {
            throw ValidationException::withMessages([
                'id' => ['该规格已被商品 SKU 使用，无法删除'],
            ]);
        }

        $spec->values()->each(function (ProductSpecificationValue $value) use ($operatorId): void {
            $value->deleted_by = $operatorId ?: null;
            $value->save();
            $value->delete();
        });

        $spec->deleted_by = $operatorId ?: null;
        $spec->save();
        $spec->delete();

        return ['message' => '删除成功'];
    }

    /**
     * @return array<string, mixed>
     */
    public function changeStatus(string $id, int $status, string $operatorId): array
    {
        $spec = ProductSpecification::query()->findOrFail($id);
        $spec->forceFill([
            'spec_status' => $status,
            'updated_by' => $operatorId ?: null,
        ])->save();

        return [
            'message' => '状态已更新',
            'spec' => $this->transform($spec->fresh()->load('values'), true),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createValue(string $specId, array $data, string $operatorId): array
    {
        $spec = ProductSpecification::query()->findOrFail($specId);
        $value = DB::transaction(fn (): ProductSpecificationValue => $this->makeValue($spec, $data, $operatorId));

        return [
            'message' => '新增成功',
            'value' => $this->transformValue($value),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function updateValue(string $id, array $data, string $operatorId): array
    {
        $value = ProductSpecificationValue::query()->findOrFail($id);
        $value->fill([
            'value' => (string) ($data['value'] ?? ''),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'value_status' => (int) ($data['value_status'] ?? 1),
        ]);
        $value->updated_by = $operatorId ?: null;
        $value->save();

        return [
            'message' => '修改成功',
            'value' => $this->transformValue($value->fresh()),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function deleteValue(string $id, string $operatorId): array
    {
        $value = ProductSpecificationValue::query()->findOrFail($id);

        if ($value->skuLinks()->exists()) {
            throw ValidationException::withMessages([
                'id' => ['该规格值已被商品 SKU 使用，无法删除'],
            ]);
        }

        $value->deleted_by = $operatorId ?: null;
        $value->save();
        $value->delete();

        return ['message' => '删除成功'];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function specPayload(array $data): array
    {
        return [
            'spec_name' => (string) ($data['spec_name'] ?? ''),
            'spec_remark' => (string) ($data['spec_remark'] ?? ''),
            'spec_status' => (int) ($data['spec_status'] ?? 1),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function makeValue(ProductSpecification $spec, array $data, string $operatorId): ProductSpecificationValue
    {
        $name = trim((string) ($data['value'] ?? ''));

        if ($name === '') {
            throw ValidationException::withMessages([
                'value' => ['请填写规格值'],
            ]);
        }

        $value = new ProductSpecificationValue([
            'spec_id' => $spec->id,
            'value' => $name,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'value_status' => (int) ($data['value_status'] ?? 1),
        ]);
        $value->value_code = SerialCode::next(ProductSpecificationValue::class, 'value_code', 'GV');
        $value->created_by = $operatorId ?: null;
        $value->save();

        return $value;
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(ProductSpecification $spec, bool $withValues = false): array
    {
        $data = [
            'id' => (string) $spec->id,
            'spec_code' => $spec->spec_code,
            'spec_name' => $spec->spec_name,
            'spec_remark' => $spec->spec_remark,
            'spec_status' => (int) $spec->spec_status,
            'sort_order' => (int) $spec->sort_order,
        ];

        if ($withValues) {
            $data['values'] = $spec->values->map(fn (ProductSpecificationValue $value) => $this->transformValue($value))->values();
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function transformValue(ProductSpecificationValue $value): array
    {
        return [
            'id' => (string) $value->id,
            'spec_id' => (string) $value->spec_id,
            'value_code' => $value->value_code,
            'value' => $value->value,
            'sort_order' => (int) $value->sort_order,
            'value_status' => (int) $value->value_status,
        ];
    }
}
