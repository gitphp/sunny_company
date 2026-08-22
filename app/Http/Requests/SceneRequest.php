<?php

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
