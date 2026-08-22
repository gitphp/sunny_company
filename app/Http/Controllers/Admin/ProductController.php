<?php

/**
 * 后台商品控制器
 *
 * @package     App\Http\Controllers\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $products) {}

    public function index(ProductRequest $request): JsonResponse
    {
        return response()->json($this->products->paginate($request->validated()));
    }

    public function store(ProductRequest $request): JsonResponse
    {
        return response()->json($this->products->create($request->validated(), $this->operatorId($request)), 201);
    }

    public function show(ProductRequest $request): JsonResponse
    {
        return response()->json($this->products->find($request->routeId('product')));
    }

    public function update(ProductRequest $request): JsonResponse
    {
        return response()->json($this->products->update(
            $request->routeId('product'),
            $request->validated(),
            $this->operatorId($request),
        ));
    }

    public function destroy(ProductRequest $request): JsonResponse
    {
        return response()->json($this->products->delete($request->routeId('product'), $this->operatorId($request)));
    }

    public function batchDestroy(ProductRequest $request): JsonResponse
    {
        return response()->json($this->products->batchDelete(
            array_map('strval', $request->validated('ids')),
            $this->operatorId($request),
        ));
    }

    public function changeStatus(ProductRequest $request): JsonResponse
    {
        return response()->json($this->products->changeStatus(
            $request->routeId('product'),
            (int) $request->validated('product_status'),
            $this->operatorId($request),
        ));
    }

    private function operatorId(ProductRequest $request): string
    {
        return (string) ($request->user()?->id ?? 0);
    }
}
