<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobRequest;
use App\Services\BossJobService;
use Illuminate\Http\JsonResponse;

class JobController extends Controller
{
    public function __construct(private readonly BossJobService $jobs) {}

    public function index(JobRequest $request): JsonResponse
    {
        return response()->json($this->jobs->publicPaginate($request->validated()));
    }

    public function show(JobRequest $request): JsonResponse
    {
        return response()->json($this->jobs->publicFind($request->routeId('job')));
    }
}
