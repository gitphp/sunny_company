<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;

class OptionRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function articleCategoriesRules(): array
    {
        return [
            'cat_type' => ['nullable', 'integer', 'in:0,1'],
        ];
    }
}
