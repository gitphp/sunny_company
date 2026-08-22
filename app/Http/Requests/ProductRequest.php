<?php

namespace App\Http\Requests;

class ProductRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function indexRules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:64'],
            'category_id' => ['nullable', 'string'],
            'brand_id' => ['nullable', 'string'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function showRules(): array
    {
        return [];
    }
}
