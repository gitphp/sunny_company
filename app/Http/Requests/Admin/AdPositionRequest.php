<?php

/**
 * 广告位表单请求
 *
 * @package     App\Http\Requests\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;
use Illuminate\Validation\Rule;

class AdPositionRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function indexRules(): array
    {
        return [
            'pos_name' => ['nullable', 'string', 'max:100'],
            'pos_code' => ['nullable', 'string', 'max:50'],
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
        return $this->positionRules();
    }

    /**
     * @return array<string, mixed>
     */
    public function updateRules(): array
    {
        return [
            ...$this->positionRules(false),
            'pos_code' => ['required', 'string', 'max:50', 'regex:/^[a-z][a-z0-9_]*$/', Rule::unique('ad_position', 'pos_code')->ignore($this->routeId('position'))],
        ];
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
    private function positionRules(bool $creating = true): array
    {
        return [
            'pos_name' => ['required', 'string', 'max:100'],
            'pos_code' => $creating
                ? ['required', 'string', 'max:50', 'regex:/^[a-z][a-z0-9_]*$/', Rule::unique('ad_position', 'pos_code')]
                : ['required', 'string', 'max:50'],
            'pos_desc' => ['nullable', 'string', 'max:255'],
            'ad_width' => ['nullable', 'integer', 'min:0'],
            'ad_height' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'integer', 'in:0,1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'pos_name' => '广告位名称',
            'pos_code' => '广告位标识',
        ];
    }
}
