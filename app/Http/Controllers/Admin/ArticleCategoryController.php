<?php

/**
 * 后台文章分类控制器
 *
 * @package     App\Http\Controllers\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticleCategoryRequest;
use App\Services\ArticleCategoryService;
use Illuminate\Http\JsonResponse;

class ArticleCategoryController extends Controller
{
    public function __construct(private readonly ArticleCategoryService $categories) {}

    public function index(ArticleCategoryRequest $request): JsonResponse
    {
        return response()->json($this->categories->tree($request->validated()));
    }

    public function store(ArticleCategoryRequest $request): JsonResponse
    {
        return response()->json($this->categories->create($request->validated()), 201);
    }

    public function update(ArticleCategoryRequest $request): JsonResponse
    {
        return response()->json($this->categories->update(
            $request->routeId('category'),
            $request->validated(),
        ));
    }

    public function destroy(ArticleCategoryRequest $request): JsonResponse
    {
        return response()->json($this->categories->delete($request->routeId('category')));
    }
}
