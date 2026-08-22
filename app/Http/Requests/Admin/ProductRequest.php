<?php

/**
 * 商品表单请求
 *
 * @package     App\Http\Requests\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;

class ProductRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function indexRules(): array
    {
        return [
            'product_name' => ['nullable', 'string', 'max:64'],
            'auto_code' => ['nullable', 'string', 'max:36'],
            'category_id' => ['nullable', 'string'],
            'brand_id' => ['nullable', 'string'],
            'product_status' => ['nullable', 'integer', 'in:0,1'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function storeRules(): array
    {
        return $this->productRules();
    }

    /**
     * @return array<string, mixed>
     */
    public function updateRules(): array
    {
        return $this->productRules();
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
            'product_status' => ['required', 'integer', 'in:0,1'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function productRules(): array
    {
        return [
            'product_name' => ['required', 'string', 'max:64'],
            'product_model' => ['nullable', 'string', 'max:128'],
            'category_id' => ['nullable', 'string'],
            'brand_id' => ['nullable', 'string'],
            'material_quality' => ['nullable', 'string', 'max:128'],
            'filling' => ['nullable', 'string', 'max:128'],
            'short_desc' => ['nullable', 'string'],
            'main_image_url' => ['nullable', 'string', 'max:512'],
            'product_status' => ['nullable', 'integer', 'in:0,1'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'skus' => ['nullable', 'array'],
            'skus.*.id' => ['nullable', 'string'],
            'skus.*.sku_code' => ['nullable', 'string', 'max:16'],
            'skus.*.price' => ['nullable', 'numeric', 'min:0'],
            'skus.*.market_price' => ['nullable', 'numeric', 'min:0'],
            'skus.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'skus.*.stock_num' => ['nullable', 'integer', 'min:0'],
            'skus.*.weight' => ['nullable', 'numeric', 'min:0'],
            'skus.*.volume' => ['nullable', 'numeric', 'min:0'],
            'skus.*.sale_status' => ['nullable', 'integer', 'in:0,1'],
            'skus.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'skus.*.spec_value_ids' => ['nullable', 'array'],
            'skus.*.spec_value_ids.*' => ['string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'product_name' => '商品名称',
            'category_id' => '商品分类',
            'skus' => 'SKU',
        ];
    }
}
