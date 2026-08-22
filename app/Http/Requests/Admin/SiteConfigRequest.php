<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;

class SiteConfigRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function updateRules(): array
    {
        return [
            'values' => ['required', 'array'],
        ];
    }
}
