<?php

/**
 * 后台商品规格控制器
 *
 * @package     App\Http\Controllers\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductSpecificationRequest;
use App\Services\ProductSpecificationService;
use Illuminate\Http\JsonResponse;

class ProductSpecificationController extends Controller
{
    public function __construct(private readonly ProductSpecificationService $specs) {}

    public function index(ProductSpecificationRequest $request): JsonResponse
    {
        return response()->json($this->specs->paginate($request->validated()));
    }

    public function store(ProductSpecificationRequest $request): JsonResponse
    {
        return response()->json($this->specs->create($request->validated(), $this->operatorId($request)), 201);
    }

    public function update(ProductSpecificationRequest $request): JsonResponse
    {
        return response()->json($this->specs->update(
            $request->routeId('spec'),
            $request->validated(),
            $this->operatorId($request),
        ));
    }

    public function destroy(ProductSpecificationRequest $request): JsonResponse
    {
        return response()->json($this->specs->delete($request->routeId('spec'), $this->operatorId($request)));
    }

    public function changeStatus(ProductSpecificationRequest $request): JsonResponse
    {
        return response()->json($this->specs->changeStatus(
            $request->routeId('spec'),
            (int) $request->validated('spec_status'),
            $this->operatorId($request),
        ));
    }

    public function createValue(ProductSpecificationRequest $request): JsonResponse
    {
        return response()->json($this->specs->createValue(
            $request->routeId('spec'),
            $request->validated(),
            $this->operatorId($request),
        ), 201);
    }

    public function updateValue(ProductSpecificationRequest $request): JsonResponse
    {
        return response()->json($this->specs->updateValue(
            $request->routeId('value'),
            $request->validated(),
            $this->operatorId($request),
        ));
    }

    public function destroyValue(ProductSpecificationRequest $request): JsonResponse
    {
        return response()->json($this->specs->deleteValue(
            $request->routeId('value'),
            $this->operatorId($request),
        ));
    }

    private function operatorId(ProductSpecificationRequest $request): string
    {
        return (string) ($request->user()?->id ?? 0);
    }
}
