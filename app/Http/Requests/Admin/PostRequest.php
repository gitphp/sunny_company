<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;
use Illuminate\Validation\Rule;

class PostRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function storeRules(): array
    {
        return $this->postRules();
    }

    /**
     * @return array<string, mixed>
     */
    public function updateRules(): array
    {
        return [
            ...$this->postRules(false),
            'post_code' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z][a-zA-Z0-9_]*$/', Rule::unique('hr_post', 'post_code')->whereNull('deleted_at')->ignore($this->routeId('post'))],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function postRules(bool $creating = true): array
    {
        return [
            'parent_id' => ['nullable', 'string'],
            'post_name' => ['required', 'string', 'max:64'],
            'post_code' => $creating
                ? ['required', 'string', 'max:64', 'regex:/^[a-zA-Z][a-zA-Z0-9_]*$/', Rule::unique('hr_post', 'post_code')->whereNull('deleted_at')]
                : ['required', 'string', 'max:64'],
            'post_sort' => ['nullable', 'integer'],
            'post_status' => ['nullable', 'integer', 'in:0,1'],
            'remark' => ['nullable', 'string', 'max:512'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'post_name' => '岗位名称',
            'post_code' => '岗位编码',
        ];
    }
}
