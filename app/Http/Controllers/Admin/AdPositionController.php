<?php

/**
 * 后台广告位控制器
 *
 * @package     App\Http\Controllers\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdPositionRequest;
use App\Services\AdPositionService;
use Illuminate\Http\JsonResponse;

class AdPositionController extends Controller
{
    public function __construct(private readonly AdPositionService $positions) {}

    public function index(AdPositionRequest $request): JsonResponse
    {
        return response()->json($this->positions->paginate($request->validated()));
    }

    public function store(AdPositionRequest $request): JsonResponse
    {
        return response()->json($this->positions->create($request->validated()), 201);
    }

    public function update(AdPositionRequest $request): JsonResponse
    {
        return response()->json($this->positions->update($request->routeId('position'), $request->validated()));
    }

    public function destroy(AdPositionRequest $request): JsonResponse
    {
        return response()->json($this->positions->delete($request->routeId('position')));
    }

    public function changeStatus(AdPositionRequest $request): JsonResponse
    {
        return response()->json($this->positions->changeStatus(
            $request->routeId('position'),
            (int) $request->validated('status'),
        ));
    }
}
