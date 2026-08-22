<?php

/**
 * 后台广告素材控制器
 *
 * @package     App\Http\Controllers\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdMaterialRequest;
use App\Services\AdMaterialService;
use Illuminate\Http\JsonResponse;

class AdMaterialController extends Controller
{
    public function __construct(private readonly AdMaterialService $materials) {}

    public function index(AdMaterialRequest $request): JsonResponse
    {
        return response()->json($this->materials->paginate($request->validated()));
    }

    public function store(AdMaterialRequest $request): JsonResponse
    {
        return response()->json($this->materials->create($request->validated()), 201);
    }

    public function update(AdMaterialRequest $request): JsonResponse
    {
        return response()->json($this->materials->update($request->routeId('material'), $request->validated()));
    }

    public function destroy(AdMaterialRequest $request): JsonResponse
    {
        return response()->json($this->materials->delete($request->routeId('material')));
    }

    public function changeStatus(AdMaterialRequest $request): JsonResponse
    {
        return response()->json($this->materials->changeStatus(
            $request->routeId('material'),
            (int) $request->validated('status'),
        ));
    }
}
