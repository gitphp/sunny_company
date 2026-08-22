<?php

/**
 * 后台商品品牌控制器
 *
 * @package     App\Http\Controllers\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductBrandRequest;
use App\Services\ProductBrandService;
use Illuminate\Http\JsonResponse;

class ProductBrandController extends Controller
{
    public function __construct(private readonly ProductBrandService $brands) {}

    public function index(ProductBrandRequest $request): JsonResponse
    {
        return response()->json($this->brands->paginate($request->validated()));
    }

    public function store(ProductBrandRequest $request): JsonResponse
    {
        return response()->json($this->brands->create($request->validated(), $this->operatorId($request)), 201);
    }

    public function update(ProductBrandRequest $request): JsonResponse
    {
        return response()->json($this->brands->update(
            $request->routeId('brand'),
            $request->validated(),
            $this->operatorId($request),
        ));
    }

    public function destroy(ProductBrandRequest $request): JsonResponse
    {
        return response()->json($this->brands->delete($request->routeId('brand'), $this->operatorId($request)));
    }

    public function changeStatus(ProductBrandRequest $request): JsonResponse
    {
        return response()->json($this->brands->changeStatus(
            $request->routeId('brand'),
            (int) $request->validated('is_show'),
            $this->operatorId($request),
        ));
    }

    private function operatorId(ProductBrandRequest $request): string
    {
        return (string) ($request->user()?->id ?? 0);
    }
}
