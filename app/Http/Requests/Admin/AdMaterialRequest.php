<?php

/**
 * 广告素材表单请求
 *
 * @package     App\Http\Requests\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;

class AdMaterialRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function indexRules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:200'],
            'position_id' => ['nullable', 'string'],
            'status' => ['nullable', 'integer', 'in:0,1'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function storeRules(): array
    {
        return $this->materialRules();
    }

    /**
     * @return array<string, mixed>
     */
    public function updateRules(): array
    {
        return $this->materialRules();
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
    private function materialRules(): array
    {
        return [
            'position_id' => ['required', 'string'],
            'title' => ['required', 'string', 'max:200'],
            'image_url' => ['required', 'string', 'max:500'],
            'link_url' => ['nullable', 'string', 'max:1000'],
            'target' => ['nullable', 'string', 'in:_blank,_self'],
            'sort_order' => ['nullable', 'integer'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
            'status' => ['nullable', 'integer', 'in:0,1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'position_id' => '广告位',
            'title' => '广告标题',
            'image_url' => '广告图片',
            'end_time' => '结束时间',
        ];
    }
}
