<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OptionRequest;
use App\Services\OptionService;
use Illuminate\Http\JsonResponse;

class OptionController extends Controller
{
    public function __construct(private readonly OptionService $options) {}

    public function roles(OptionRequest $request): JsonResponse
    {
        return response()->json($this->options->roles());
    }

    public function departments(OptionRequest $request): JsonResponse
    {
        return response()->json($this->options->departments());
    }
}
