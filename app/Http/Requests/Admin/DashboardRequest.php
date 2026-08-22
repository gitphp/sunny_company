<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\SceneRequest;

class DashboardRequest extends SceneRequest
{
    /**
     * @return array<string, mixed>
     */
    public function indexRules(): array
    {
        return [];
    }
}
