<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;

class ProductCategoryRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function indexRules(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public function storeRules(): array
    {
        return $this->categoryRules();
    }

    /**
     * @return array<string, mixed>
     */
    public function updateRules(): array
    {
        return $this->categoryRules();
    }

    /**
     * @return array<string, mixed>
     */
    private function categoryRules(): array
    {
        return [
            'parent_id' => ['nullable', 'string'],
            'category_name' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:32'],
            'cat_status' => ['nullable', 'integer', 'in:0,1'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'cat_remark' => ['nullable', 'string', 'max:512'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'category_name' => '分类名称',
            'parent_id' => '上级分类',
        ];
    }
}
