<?php

/**
 * 后台下拉选项控制器
 *
 * @package     App\Http\Controllers\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

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

    public function posts(OptionRequest $request): JsonResponse
    {
        return response()->json($this->options->posts());
    }

    public function articleCategories(OptionRequest $request): JsonResponse
    {
        return response()->json($this->options->articleCategories($request->validated()));
    }

    public function adPositions(OptionRequest $request): JsonResponse
    {
        return response()->json($this->options->adPositions());
    }

    public function productBrands(OptionRequest $request): JsonResponse
    {
        return response()->json($this->options->productBrands());
    }

    public function productCategories(OptionRequest $request): JsonResponse
    {
        return response()->json($this->options->productCategories());
    }

    public function productSpecs(OptionRequest $request): JsonResponse
    {
        return response()->json($this->options->productSpecs());
    }
}
