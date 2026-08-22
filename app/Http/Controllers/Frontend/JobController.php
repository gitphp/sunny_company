<?php

/**
 * 前台招聘职位控制器
 *
 * @package     App\Http\Controllers\Frontend
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
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
