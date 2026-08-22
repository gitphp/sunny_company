<?php

/**
 * 商品规格表单请求
 *
 * @package     App\Http\Requests\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;

class ProductSpecificationRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function indexRules(): array
    {
        return [
            'spec_name' => ['nullable', 'string', 'max:255'],
            'spec_status' => ['nullable', 'integer', 'in:0,1'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function storeRules(): array
    {
        return [
            ...$this->specRules(),
            'values' => ['nullable', 'array'],
            'values.*.value' => ['required', 'string', 'max:255'],
            'values.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'values.*.value_status' => ['nullable', 'integer', 'in:0,1'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function updateRules(): array
    {
        return $this->specRules();
    }

    /**
     * @return array<string, mixed>
     */
    public function changeStatusRules(): array
    {
        return [
            'spec_status' => ['required', 'integer', 'in:0,1'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function createValueRules(): array
    {
        return $this->valueRules();
    }

    /**
     * @return array<string, mixed>
     */
    public function updateValueRules(): array
    {
        return $this->valueRules();
    }

    /**
     * @return array<string, mixed>
     */
    private function specRules(): array
    {
        return [
            'spec_name' => ['required', 'string', 'max:255'],
            'spec_remark' => ['nullable', 'string', 'max:512'],
            'spec_status' => ['nullable', 'integer', 'in:0,1'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function valueRules(): array
    {
        return [
            'value' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'value_status' => ['nullable', 'integer', 'in:0,1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'spec_name' => '规格名称',
            'value' => '规格值',
        ];
    }
}
