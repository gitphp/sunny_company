<?php

/**
 * 后台操作日志控制器
 *
 * @package     App\Http\Controllers\Admin
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OperationLogRequest;
use App\Services\OperationLogService;
use Illuminate\Http\JsonResponse;

class OperationLogController extends Controller
{
    public function __construct(private readonly OperationLogService $logs) {}

    public function index(OperationLogRequest $request): JsonResponse
    {
        return response()->json($this->logs->paginate($request->validated()));
    }

    public function show(OperationLogRequest $request): JsonResponse
    {
        return response()->json($this->logs->find($request->routeId('log')));
    }

    public function destroy(OperationLogRequest $request): JsonResponse
    {
        return response()->json($this->logs->delete($request->routeId('log')));
    }

    public function batchDestroy(OperationLogRequest $request): JsonResponse
    {
        return response()->json($this->logs->batchDelete(
            array_map('strval', $request->validated('ids')),
        ));
    }
}
