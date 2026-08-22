<?php

/**
 * 后台商品分类控制器
 *
 * @package     App\Http\Controllers\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductCategoryRequest;
use App\Services\ProductCategoryService;
use Illuminate\Http\JsonResponse;

class ProductCategoryController extends Controller
{
    public function __construct(private readonly ProductCategoryService $categories) {}

    public function index(ProductCategoryRequest $request): JsonResponse
    {
        return response()->json($this->categories->tree());
    }

    public function store(ProductCategoryRequest $request): JsonResponse
    {
        return response()->json($this->categories->create($request->validated(), $this->operatorId($request)), 201);
    }

    public function update(ProductCategoryRequest $request): JsonResponse
    {
        return response()->json($this->categories->update(
            $request->routeId('category'),
            $request->validated(),
            $this->operatorId($request),
        ));
    }

    public function destroy(ProductCategoryRequest $request): JsonResponse
    {
        return response()->json($this->categories->delete($request->routeId('category'), $this->operatorId($request)));
    }

    private function operatorId(ProductCategoryRequest $request): string
    {
        return (string) ($request->user()?->id ?? 0);
    }
}
