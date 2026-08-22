<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\JobRequest;
use App\Services\BossJobService;
use Illuminate\Http\JsonResponse;

class JobController extends Controller
{
    public function __construct(private readonly BossJobService $jobs) {}

    public function index(JobRequest $request): JsonResponse
    {
        return response()->json($this->jobs->paginate($request->validated()));
    }

    public function store(JobRequest $request): JsonResponse
    {
        return response()->json($this->jobs->create($request->validated()), 201);
    }

    public function show(JobRequest $request): JsonResponse
    {
        return response()->json($this->jobs->find($request->routeId('job')));
    }

    public function update(JobRequest $request): JsonResponse
    {
        return response()->json($this->jobs->update($request->routeId('job'), $request->validated()));
    }

    public function destroy(JobRequest $request): JsonResponse
    {
        return response()->json($this->jobs->delete($request->routeId('job')));
    }

    public function batchDestroy(JobRequest $request): JsonResponse
    {
        return response()->json($this->jobs->batchDelete(
            array_map('strval', $request->validated('ids')),
        ));
    }

    public function changeStatus(JobRequest $request): JsonResponse
    {
        return response()->json($this->jobs->changeStatus(
            $request->routeId('job'),
            $request->validated(),
        ));
    }
}
