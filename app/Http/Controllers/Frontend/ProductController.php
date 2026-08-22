<?php

/**
 * 前台商品控制器
 *
 * @package     App\Http\Controllers\Frontend
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Services\ProductCategoryService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $products,
        private readonly ProductCategoryService $categories,
    ) {}

    public function index(ProductRequest $request): JsonResponse
    {
        return response()->json($this->products->publicPaginate($request->validated()));
    }

    public function show(ProductRequest $request): JsonResponse
    {
        return response()->json($this->products->publicFind($request->routeId('product')));
    }

    public function categories(ProductRequest $request): JsonResponse
    {
        return response()->json($this->categories->publicTree());
    }
}
