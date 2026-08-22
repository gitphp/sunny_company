<?php

/**
 * 商品服务类
 *
 * @package     App\Services
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Services;

use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductSku;
use App\Models\ProductSkuSpecValue;
use App\Models\ProductSpecificationValue;
use App\Support\SerialCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function paginate(array $filters): array
    {
        $paginator = Product::query()
            ->with(['category:id,category_name', 'brand:id,brand_name', 'skus'])
            ->when(($filters['product_name'] ?? '') !== '', function (Builder $query) use ($filters): void {
                $keyword = $filters['product_name'];
                $query->where(function (Builder $builder) use ($keyword): void {
                    $builder->where('product_name', 'like', '%'.$keyword.'%')
                        ->orWhere('auto_code', 'like', '%'.$keyword.'%');
                });
            })
            ->when(isset($filters['product_status']) && $filters['product_status'] !== '', fn (Builder $query) => $query->where('product_status', $filters['product_status']))
            ->when(($filters['brand_id'] ?? '') !== '' && ($filters['brand_id'] ?? '0') !== '0', fn (Builder $query) => $query->where('brand_id', $filters['brand_id']))
            ->when(($filters['category_id'] ?? '') !== '' && ($filters['category_id'] ?? '0') !== '0', function (Builder $query) use ($filters): void {
                $query->whereIn('category_id', ProductCategory::selfAndDescendantIds((string) $filters['category_id']));
            })
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->paginate((int) ($filters['per_page'] ?? 10));

        return [
            'data' => collect($paginator->items())->map(fn (Product $product) => $this->transform($product))->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function find(string $id): array
    {
        $product = Product::query()
            ->with(['category:id,category_name', 'brand:id,brand_name', 'skus.specValues.spec', 'skus.specValues.specValue'])
            ->findOrFail($id);

        return [
            'product' => $this->transform($product, true),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function create(array $data, string $operatorId): array
    {
        $product = DB::transaction(function () use ($data, $operatorId): Product {
            $this->assertPublishable($data);
            $product = new Product($this->payload($data));
            $product->auto_code = SerialCode::next(Product::class, 'auto_code', 'SP');
            $product->created_by = $operatorId ?: null;
            $product->save();
            $this->syncSkus($product, $data['skus'] ?? [], $operatorId);
            $this->refreshProductCount((string) $product->category_id);

            return $product->fresh()->load(['category:id,category_name', 'brand:id,brand_name', 'skus.specValues.spec', 'skus.specValues.specValue']);
        });

        return [
            'message' => '新增成功',
            'product' => $this->transform($product, true),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data, string $operatorId): array
    {
        $product = DB::transaction(function () use ($id, $data, $operatorId): Product {
            $product = Product::query()->findOrFail($id);
            $this->assertPublishable($data);
            $oldCategoryId = (string) $product->category_id;
            $product->fill($this->payload($data));
            $product->updated_by = $operatorId ?: null;
            $product->save();
            $this->syncSkus($product, $data['skus'] ?? [], $operatorId);
            $this->refreshProductCount($oldCategoryId);
            $this->refreshProductCount((string) $product->category_id);

            return $product->fresh()->load(['category:id,category_name', 'brand:id,brand_name', 'skus.specValues.spec', 'skus.specValues.specValue']);
        });

        return [
            'message' => '修改成功',
            'product' => $this->transform($product, true),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function delete(string $id, string $operatorId): array
    {
        $product = Product::query()->findOrFail($id);
        $categoryId = (string) $product->category_id;
        $this->deleteProduct($product, $operatorId);
        $this->refreshProductCount($categoryId);

        return ['message' => '删除成功'];
    }

    /**
     * @param  list<string>  $ids
     * @return array<string, string>
     */
    public function batchDelete(array $ids, string $operatorId): array
    {
        $products = Product::query()->whereIn('id', $ids)->get();
        $categoryIds = $products->pluck('category_id')->map(fn ($id) => (string) $id)->unique()->all();

        foreach ($products as $product) {
            $this->deleteProduct($product, $operatorId);
        }

        foreach ($categoryIds as $categoryId) {
            $this->refreshProductCount($categoryId);
        }

        return ['message' => '删除成功'];
    }

    /**
     * @return array<string, mixed>
     */
    public function changeStatus(string $id, int $status, string $operatorId): array
    {
        $product = Product::query()->findOrFail($id);

        if ($status === 1) {
            $this->assertPublishable([
                'product_name' => $product->product_name,
                'category_id' => $product->category_id,
                'product_status' => 1,
            ]);
        }

        $product->forceFill([
            'product_status' => $status,
            'updated_by' => $operatorId ?: null,
        ])->save();

        return [
            'message' => '状态已更新',
            'product' => $this->transform($product->fresh()->load(['category:id,category_name', 'brand:id,brand_name', 'skus'])),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function publicPaginate(array $filters): array
    {
        $paginator = Product::query()
            ->with(['category:id,category_name', 'brand:id,brand_name', 'skus' => fn ($query) => $query->where('sale_status', 1)])
            ->where('product_status', 1)
            ->when(($filters['keyword'] ?? '') !== '', fn (Builder $query) => $query->where('product_name', 'like', '%'.$filters['keyword'].'%'))
            ->when(($filters['brand_id'] ?? '') !== '' && ($filters['brand_id'] ?? '0') !== '0', fn (Builder $query) => $query->where('brand_id', $filters['brand_id']))
            ->when(($filters['category_id'] ?? '') !== '' && ($filters['category_id'] ?? '0') !== '0', function (Builder $query) use ($filters): void {
                $query->whereIn('category_id', ProductCategory::selfAndDescendantIds((string) $filters['category_id']));
            })
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->paginate((int) ($filters['per_page'] ?? 10));

        return [
            'data' => collect($paginator->items())->map(fn (Product $product) => $this->transform($product))->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function publicFind(string $id): array
    {
        $product = Product::query()
            ->with(['category:id,category_name', 'brand:id,brand_name', 'skus.specValues.spec', 'skus.specValues.specValue'])
            ->where('product_status', 1)
            ->find($id);

        if (! $product) {
            throw (new ModelNotFoundException)->setModel(Product::class, [$id]);
        }

        return [
            'product' => $this->transform($product, true),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data): array
    {
        $categoryId = (string) ($data['category_id'] ?? '0');
        $brandId = (string) ($data['brand_id'] ?? '0');

        if ($categoryId !== '' && $categoryId !== '0') {
            ProductCategory::query()->findOrFail($categoryId);
        }

        if ($brandId !== '' && $brandId !== '0') {
            ProductBrand::query()->findOrFail($brandId);
        }

        return [
            'product_name' => (string) ($data['product_name'] ?? ''),
            'product_model' => (string) ($data['product_model'] ?? ''),
            'category_id' => ($categoryId === '' || $categoryId === '0') ? 0 : $categoryId,
            'brand_id' => ($brandId === '' || $brandId === '0') ? 0 : $brandId,
            'material_quality' => (string) ($data['material_quality'] ?? ''),
            'filling' => (string) ($data['filling'] ?? ''),
            'short_desc' => (string) ($data['short_desc'] ?? ''),
            'main_image_url' => (string) ($data['main_image_url'] ?? ''),
            'product_status' => (int) ($data['product_status'] ?? 1),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function assertPublishable(array $data): void
    {
        if ((int) ($data['product_status'] ?? 1) !== 1) {
            return;
        }

        if (trim((string) ($data['product_name'] ?? '')) === '') {
            throw ValidationException::withMessages([
                'product_name' => ['上架商品必须填写名称'],
            ]);
        }

        $categoryId = (string) ($data['category_id'] ?? '0');

        if ($categoryId === '' || $categoryId === '0') {
            throw ValidationException::withMessages([
                'category_id' => ['上架商品必须选择分类'],
            ]);
        }
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    private function syncSkus(Product $product, array $rows, string $operatorId): void
    {
        $keepIds = [];
        $signatures = [];

        foreach ($rows as $index => $row) {
            $specValueIds = array_values(array_filter(array_map('strval', $row['spec_value_ids'] ?? [])));
            $signature = $this->specSignature($specValueIds);

            if (isset($signatures[$signature])) {
                throw ValidationException::withMessages([
                    'skus' => ['存在重复的规格组合'],
                ]);
            }

            $signatures[$signature] = true;
            $sku = $this->persistSku($product, $row, $specValueIds, $operatorId, $index);
            $keepIds[] = (string) $sku->id;
        }

        $product->skus()->whereNotIn('id', $keepIds !== [] ? $keepIds : [0])->get()->each(function (ProductSku $sku) use ($operatorId): void {
            $this->deleteSku($sku, $operatorId);
        });
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  list<string>  $specValueIds
     */
    private function persistSku(Product $product, array $row, array $specValueIds, string $operatorId, int $index): ProductSku
    {
        $skuId = (string) ($row['id'] ?? '');
        $sku = $skuId !== ''
            ? ProductSku::query()->where('product_id', $product->id)->find($skuId)
            : null;

        if (! $sku) {
            $sku = new ProductSku;
            $sku->product_id = $product->id;
            $sku->sku_code = $this->skuCode($row);
            $sku->created_by = $operatorId ?: null;
        } else {
            $sku->updated_by = $operatorId ?: null;
            $incoming = trim((string) ($row['sku_code'] ?? ''));

            if ($incoming !== '' && $incoming !== $sku->sku_code) {
                $this->assertSkuCodeUnique($incoming, (string) $sku->id);
                $sku->sku_code = $incoming;
            }
        }

        $sku->fill([
            'price' => $row['price'] ?? 0,
            'market_price' => $row['market_price'] ?? 0,
            'cost_price' => $row['cost_price'] ?? 0,
            'stock_num' => (int) ($row['stock_num'] ?? 0),
            'weight' => $row['weight'] ?? 0,
            'volume' => $row['volume'] ?? 0,
            'sale_status' => (int) ($row['sale_status'] ?? 1),
            'sort_order' => (int) ($row['sort_order'] ?? (100 - $index)),
        ]);
        $sku->save();
        $this->syncSkuSpecs($sku, $specValueIds, $operatorId);

        return $sku;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function skuCode(array $row): string
    {
        $code = trim((string) ($row['sku_code'] ?? ''));

        if ($code !== '') {
            $this->assertSkuCodeUnique($code);

            return mb_substr($code, 0, 16);
        }

        return SerialCode::next(ProductSku::class, 'sku_code', 'SK');
    }

    private function assertSkuCodeUnique(string $code, string $ignoreId = ''): void
    {
        $exists = ProductSku::withTrashed()
            ->where('sku_code', $code)
            ->when($ignoreId !== '', fn (Builder $query) => $query->where('id', '!=', $ignoreId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'skus' => ['SKU 编码 '.$code.' 已存在'],
            ]);
        }
    }

    /**
     * @param  list<string>  $specValueIds
     */
    private function syncSkuSpecs(ProductSku $sku, array $specValueIds, string $operatorId): void
    {
        $keepIds = [];

        foreach ($specValueIds as $valueId) {
            $value = ProductSpecificationValue::query()->findOrFail($valueId);
            $link = ProductSkuSpecValue::withTrashed()->firstOrNew([
                'sku_id' => $sku->id,
                'spec_id' => $value->spec_id,
                'spec_value_id' => $value->id,
            ]);

            if ($link->trashed()) {
                $link->restore();
            }

            if (! $link->exists) {
                $link->created_by = $operatorId ?: null;
                $link->save();
            } else {
                $link->save();
            }

            $keepIds[] = (string) $link->id;
        }

        $sku->specValues()->whereNotIn('id', $keepIds !== [] ? $keepIds : [0])->get()->each(function (ProductSkuSpecValue $link) use ($operatorId): void {
            $link->deleted_by = $operatorId ?: null;
            $link->save();
            $link->delete();
        });
    }

    /**
     * @param  list<string>  $specValueIds
     */
    private function specSignature(array $specValueIds): string
    {
        $ids = $specValueIds;
        sort($ids);

        return implode(',', $ids);
    }

    private function deleteProduct(Product $product, string $operatorId): void
    {
        $product->skus()->get()->each(fn (ProductSku $sku) => $this->deleteSku($sku, $operatorId));
        $product->deleted_by = $operatorId ?: null;
        $product->save();
        $product->delete();
    }

    private function deleteSku(ProductSku $sku, string $operatorId): void
    {
        $sku->specValues()->get()->each(function (ProductSkuSpecValue $link) use ($operatorId): void {
            $link->deleted_by = $operatorId ?: null;
            $link->save();
            $link->delete();
        });
        $sku->deleted_by = $operatorId ?: null;
        $sku->save();
        $sku->delete();
    }

    private function refreshProductCount(string $categoryId): void
    {
        if ($categoryId === '' || $categoryId === '0') {
            return;
        }

        ProductCategory::query()->where('id', $categoryId)->update([
            'product_count' => Product::query()->where('category_id', $categoryId)->count(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(Product $product, bool $detail = false): array
    {
        $skus = $product->skus ?? collect();
        $onSale = $skus->where('sale_status', 1);
        $data = [
            'id' => (string) $product->id,
            'auto_code' => $product->auto_code,
            'product_name' => $product->product_name,
            'product_model' => $product->product_model,
            'category_id' => (string) $product->category_id,
            'category_name' => $product->category?->category_name,
            'brand_id' => (string) $product->brand_id,
            'brand_name' => $product->brand?->brand_name,
            'material_quality' => $product->material_quality,
            'filling' => $product->filling,
            'main_image_url' => $product->main_image_url,
            'product_status' => (int) $product->product_status,
            'sort_order' => (int) $product->sort_order,
            'sku_count' => $skus->count(),
            'stock_num' => (int) $skus->sum('stock_num'),
            'min_price' => $onSale->isNotEmpty() ? (string) $onSale->min('price') : (string) ($skus->min('price') ?? '0.00'),
            'created_at' => $product->created_at?->toDateTimeString(),
        ];

        if ($detail) {
            $data['short_desc'] = $product->short_desc;
            $data['skus'] = $skus->map(fn (ProductSku $sku) => $this->transformSku($sku))->values();
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function transformSku(ProductSku $sku): array
    {
        $links = $sku->specValues ?? collect();

        return [
            'id' => (string) $sku->id,
            'sku_code' => $sku->sku_code,
            'price' => (string) $sku->price,
            'market_price' => (string) $sku->market_price,
            'cost_price' => (string) $sku->cost_price,
            'stock_num' => (int) $sku->stock_num,
            'weight' => (string) $sku->weight,
            'volume' => (string) $sku->volume,
            'sale_status' => (int) $sku->sale_status,
            'sort_order' => (int) $sku->sort_order,
            'spec_value_ids' => $links->map(fn (ProductSkuSpecValue $link) => (string) $link->spec_value_id)->values(),
            'spec_text' => $links->map(function (ProductSkuSpecValue $link) {
                $name = $link->spec?->spec_name;
                $value = $link->specValue?->value;

                return $name && $value ? $name.':'.$value : null;
            })->filter()->implode(' / '),
        ];
    }
}
