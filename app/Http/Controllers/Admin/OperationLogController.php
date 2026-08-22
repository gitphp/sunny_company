<?php

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
