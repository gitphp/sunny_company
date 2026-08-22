<?php

/**
 * AI模型表单请求
 *
 * @package     App\Http\Requests\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;

class AiProviderRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function indexRules(): array
    {
        return [
            'provider_name' => ['nullable', 'string', 'max:64'],
            'status' => ['nullable', 'integer', 'in:0,1'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function optionsRules(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public function storeRules(): array
    {
        return $this->providerRules(true);
    }

    /**
     * @return array<string, mixed>
     */
    public function updateRules(): array
    {
        return $this->providerRules(false);
    }

    /**
     * @return array<string, mixed>
     */
    public function changeStatusRules(): array
    {
        return [
            'status' => ['required', 'integer', 'in:0,1'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function providerRules(bool $creating): array
    {
        return [
            'provider_name' => ['required', 'string', 'max:64'],
            'driver' => ['nullable', 'string', 'in:openai'],
            'base_url' => ['required', 'string', 'max:255'],
            'api_key' => [$creating ? 'nullable' : 'nullable', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:64'],
            'temperature' => ['nullable', 'numeric', 'min:0', 'max:2'],
            'max_tokens' => ['nullable', 'integer', 'min:1', 'max:8192'],
            'system_prompt' => ['nullable', 'string', 'max:2000'],
            'is_default' => ['nullable', 'integer', 'in:0,1'],
            'status' => ['nullable', 'integer', 'in:0,1'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'provider_name' => '模型名称',
            'base_url' => '接口地址',
            'api_key' => '接口密钥',
            'model' => '模型标识',
        ];
    }
}
