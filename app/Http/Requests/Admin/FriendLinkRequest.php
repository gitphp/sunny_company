<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;

class FriendLinkRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function indexRules(): array
    {
        return [
            'link_name' => ['nullable', 'string', 'max:32'],
            'link_status' => ['nullable', 'integer', 'in:0,1'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function storeRules(): array
    {
        return $this->linkRules();
    }

    /**
     * @return array<string, mixed>
     */
    public function updateRules(): array
    {
        return $this->linkRules();
    }

    /**
     * @return array<string, mixed>
     */
    public function changeStatusRules(): array
    {
        return [
            'link_status' => ['required', 'integer', 'in:0,1'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function linkRules(): array
    {
        return [
            'link_name' => ['required', 'string', 'max:32'],
            'link_url' => ['required', 'string', 'max:512', 'url'],
            'link_logo' => ['nullable', 'string', 'max:512'],
            'link_desc' => ['nullable', 'string', 'max:255'],
            'link_sort' => ['nullable', 'integer', 'min:0'],
            'link_status' => ['nullable', 'integer', 'in:0,1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'link_name' => '网站名称',
            'link_url' => '网站链接',
        ];
    }
}
