<?php

/**
 * 场景表单请求基类
 *
 * @package     App\Http\Requests
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class SceneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function scene(): string
    {
        return $this->route()?->getActionMethod() ?? '';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $method = $this->scene().'Rules';

        return method_exists($this, $method) ? $this->{$method}() : [];
    }

    public function routeId(string $name): string
    {
        $value = $this->route($name);

        if (is_object($value) && method_exists($value, 'getKey')) {
            return (string) $value->getKey();
        }

        return (string) $value;
    }
}
